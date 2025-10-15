<?php


namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\donations;
use App\Models\User;
use App\Models\blood_stocks;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;

class DonationController extends Controller
{
    /**
     * Liste de tous les dons
     */
    public function index(Request $request)
    {
        $query = donations::with(['donor', 'agent'])->orderBy('donation_date', 'desc');

        // Filtres
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('donor', function($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($request->filled('blood_group')) {
            $query->where('blood_group', $request->blood_group);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('donation_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('donation_date', '<=', $request->date_to);
        }

        $donations = $query->paginate(20);

        // Statistiques
        $stats = [
            'total' => donations::count(),
            'today' => donations::whereDate('donation_date', today())->count(),
            'pending' => donations::where('status', 'pending')->count(),
            'validated' => donations::where('status', 'validated')->count(),
            'total_volume' => donations::sum('quantity_ml'),
        ];

        return view('agent.donations.index', compact('donations', 'stats'));
    }

    /**
     * Formulaire de création de don
     */
    public function create()
    {
        $donors = User::donors()->active()->orderBy('first_name')->get();
        return view('agent.donations.create', compact('donors'));
    }

    /**
     * Enregistrer un nouveau don
     */
    public function store(Request $request)
{
    //  Validation des champs
    $validated = $request->validate([
        'donor_id'         => 'required|exists:users,id',
        'donation_date'    => 'required|date|before_or_equal:today',
        'donation_type'    => 'required|string',
        'quantity_units'   => 'required|numeric|min:200|max:500',
        'bag_number'       => 'required|string|unique:donations,bag_number',
        'weight'           => 'required|numeric|min:50',
        'blood_pressure'   => 'required|string',
        'hemoglobin_level' => 'required|numeric|min:12',
        'medical_notes'    => 'nullable|string',
    ]);

    //  Agent actuel
    $validated['agent_id'] = auth()->id();

    //  Groupe sanguin = celui du donneur
    $donor = User::findOrFail($validated['donor_id']);
    $validated['blood_group'] = $donor->blood_group;

    //  Statut par défaut
    $validated['status'] = 'pending';
    $validated['consent_given'] = true;
    $validated['medical_check_passed'] = true;
    $validated['eligibility_verified'] = true;
    $validated['bag_number'] = $request->bag_number;

    //  Enregistrement du don
    $donation = donations::create($validated);

    // Mise à jour du stock
    $stock = blood_stocks::firstOrCreate(
        ['blood_group' => $validated['blood_group']],
        [
            'quantity_units' => 0,
            'status' => 'ok',
            'expiry_date' => now()->addDays(42),
        ]
    );

    $stock->quantity_units += $validated['quantity_units'];

    // Mise à jour du statut du stock
    if ($stock->quantity_units <= 0) {
        $stock->status = 'empty';
    } elseif ($stock->quantity_units < 500) {
        $stock->status = 'critical';
    } else {
        $stock->status = 'ok';
    }

    $stock->save();

    // Redirection
    return redirect()->route('agent.donations.index')->with('success', 'Don enregistré avec succès');
}

    /**
     * Générer un reçu PDF
     */
    public function receipt($id)
    {
        $donation = donations::with(['donor', 'agent'])->findOrFail($id);

        $pdf = Pdf::loadView('agent.donations.receipt-pdf', compact('donation'));

        return $pdf->download('recu-don-' . $donation->bag_number . '.pdf');
    }

    /**
     * Valider un don
     */
    public function validateDonation($id)
    {
        $donation = donations::findOrFail($id);

        if ($donation->status === 'validated') {
            return redirect()->back()->with('error', 'Ce don est déjà validé');
        }

        DB::beginTransaction();
        try {
            $donation->update([
                'status' => 'validated',
                'validated_at' => now(),
                'validated_by' => Auth::id(),
            ]);

            $expiryDate = Carbon::parse($donation->donation_date)->addDays(42);

            blood_stocks::create([
                'blood_group' => $donation->blood_group,
                'quantity' => 1,
                'bag_number' => $donation->bag_number,
                'collection_date' => $donation->donation_date,
                'expiry_date' => $expiryDate,
                'location' => $donation->location,
                'status' => 'available',
                'donor_id' => $donation->donor_id,
            ]);

            DB::commit();

            return redirect()->route('agent.donations.show', $id)
                ->with('success', 'Don validé et ajouté au stock avec succès');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Erreur lors de la validation : ' . $e->getMessage());
        }
    }

    /**
     * Vérifier l'éligibilité d'un donneur
     */
    private function checkEligibility($donor)
    {
        if ($donor->last_donation_date) {
            $lastDonation = Carbon::parse($donor->last_donation_date);
            $today = Carbon::today();
            $daysSinceLastDonation = $lastDonation->diffInDays($today);

            $requiredInterval = ($donor->gender === 'M') ? 60 : 90;

            if ($daysSinceLastDonation < $requiredInterval) {
                return false;
            }
        }

        if ($donor->birth_date) {
            $age = Carbon::parse($donor->birth_date)->age;
            if ($age < 18 || $age > 65) {
                return false;
            }
        }

        return true;
    }
}
