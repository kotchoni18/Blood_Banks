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
    //1. Validation
    $validated = $request->validate([
        'donor_id'         => 'required|exists:users,id',
        'donation_date'    => 'required|date|before_or_equal:today',
        'donation_type'    => 'required|string',
        'quantity_ml'      => 'required|numeric|min:200|max:500',
        'bag_number'       => 'required|string|unique:donations,bag_number',
        'weight'           => 'required|numeric|min:50',
        'blood_pressure'   => 'required|string',
        'hemoglobin_level' => 'required|numeric|min:12',
        'notes'            => 'nullable|string',
    ]);

    // 2. Ajouter agent connecté
    $validated['agent_id'] = auth()->id();

    // 3. Récupérer le groupe sanguin du donneur automatiquement
    $donor = User::findOrFail($validated['donor_id']);
    $validated['blood_group'] = $donor->blood_group;

    // 4. Champs optionnels ou automatiques
    $validated['status'] = 'pending'; // ou 'completed'
    $validated['consent_given'] = true;
    $validated['medical_check_passed'] = true;
    $validated['eligibility_verified'] = true;

    // 5. Enregistrement du don
    $donation = donations::create($validated);

    // 6. Mise à jour du stock
    $stock = blood_stocks::firstOrCreate(
        ['blood_group' => $validated['blood_group']],
        [
            'quantity_units' => 0,
            'status' => 'ok',
            'expiry_date' => now()->addDays(42),
        ]
    );

    $stock->quantity_units += $validated['quantity_ml'];

    if ($stock->quantity_units <= 0) {
        $stock->status = 'empty';
    } elseif ($stock->quantity_units < 500) {
        $stock->status = 'critical';
    } else {
        $stock->status = 'ok';
    }

    $stock->save();

    // 7. Réponse
    return redirect()->route('agent.donations.index')
        ->with('success', 'Don enregistré avec succès');
    }
    /**
     * Afficher les détails d'un don
     */
    public function show($id)
    {
        $donation = donations::with(['donor', 'agent'])->findOrFail($id);
        return view('agent.donations.show', compact('donation'));
    }

    /**
     * Formulaire de modification
     */
    public function edit($id)
    {
        $donation = donations::findOrFail($id);

        if ($donation->status !== 'pending') {
            return redirect()->route('agent.donations.show', $id)
                ->with('error', 'Ce don ne peut plus être modifié.');
        }

        $donors = User::donors()->active()->orderBy('first_name')->get();
        return view('agent.donations.edit', compact('donation', 'donors'));
    }

    /**
     * Mettre à jour un don
     */
    public function update(Request $request, $id)
    {
        $donation = donations::findOrFail($id);

        if ($donation->status !== 'pending') {
            return redirect()->route('agent.donations.show', $id)
                ->with('error', 'Ce don ne peut plus être modifié.');
        }

        $validated = $request->validate([
            'quantity_ml' => 'required|numeric|min:200|max:500',
            'weight' => 'required|numeric|min:50',
            'blood_pressure' => 'required|string',
            'hemoglobin_level' => 'required|numeric|min:12',
            'temperature' => 'nullable|numeric|min:35|max:38',
            'notes' => 'nullable|string',
        ]);

        $donation->update($validated);

        return redirect()->route('agent.donations.show', $id)
            ->with('success', 'Don mis à jour avec succès');
    }

    /**
     * Supprimer un don
     */
    public function destroy($id)
    {
        $donation = donations::findOrFail($id);

        if ($donation->status === 'validated') {
            return redirect()->back()
                ->with('error', 'Impossible de supprimer un don validé.');
        }

        DB::beginTransaction();
        try {
            if ($donation->donor) {
                $donation->donor->decrement('donation_count');
            }

            $donation->delete();
            DB::commit();

            return redirect()->route('agent.donations.index')
                ->with('success', 'Don supprimé avec succès');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Erreur lors de la suppression');
        }
    }

    /**
     * Dons du jour
     */
    public function todayDonations()
    {
        $donations = donations::with(['donor', 'agent'])
            ->whereDate('donation_date', today())
            ->orderBy('created_at', 'desc')
            ->get();

        $stats = [
            'count' => $donations->count(),
            'volume' => $donations->sum('quantity_ml'),
            'by_blood_group' => $donations->groupBy('blood_group')->map->count(),
        ];

        return view('agent.donations.today', compact('donations', 'stats'));
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
