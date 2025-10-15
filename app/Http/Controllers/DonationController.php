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
    // 1. Validation
    $validated = $request->validate([
        'donor_id'         => 'required|exists:users,id',
        'donation_date'    => 'required|date|before_or_equal:today',
        'donation_type'    => 'required|in:whole_blood,plasma,platelets,double_red',
        'quantity_ml'      => 'required|numeric|min:200|max:500',
        'bag_number'       => 'required|string|unique:donations,bag_number',
        'weight'           => 'required|numeric|min:50',
        'blood_pressure'   => 'required|string',
        'hemoglobin_level' => 'required|numeric|min:12',
        'medical_notes'    => 'nullable|string',
    ]);

    // 2. Agent connecté
    $validated['agent_id'] = auth()->id();

    // 3. Groupe sanguin du donneur
    $donor = User::findOrFail($validated['donor_id']);
    $validated['blood_group'] = $donor->blood_group;

    // 4. Champs par défaut
    $validated['status'] = 'pending';
    $validated['consent_given'] = true;
    $validated['medical_check_passed'] = true;
    $validated['eligibility_verified'] = true;

    // 5. Enregistrement
    $donation = donations::create($validated);

    // 6. Mise à jour du stock
    $stock = blood_stocks::firstOrCreate(
        ['blood_group' => $validated['blood_group']],
        [
            'quantity_units' => 0,
            'status' => 'ok',
            'expiry_date' => now()->addDays(42),
            'collection_date' => now(),
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

    return redirect()->route('agent.donations.index')->with('success', 'Don enregistré avec succès');
}


    /**
     * Générer un reçu PDF
     */
    // public function receipt($id)
    // {
    //     $donation = donations::with(['donor', 'agent'])->findOrFail($id);

    //     $pdf = Pdf::loadView('agent.donations.receipt-pdf', compact('donation'));

    //     return $pdf->download('recu-don-' . $donation->bag_number . '.pdf');
    // }

    /**
     * Valider un don
     */
    public function validateDonation($id)
{
    $donation = donations::findOrFail($id);

    if ($donation->status === 'validated') {
        return redirect()->back()->with('error', 'Ce don est déjà validé');
    }

    // Transaction pour valider et ajouter au stock
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
            'quantity_units' => 1,
            'bag_number' => $donation->bag_number,
            'collection_date' => $donation->donation_date,
            'expiry_date' => $expiryDate,
            'location' => $donation->location ?? 'Default Location',
            'status' => 'available',
            'donor_id' => $donation->donor_id,
        ]);

        DB::commit();

        return redirect()->route('agent.donations.show', $id)->with('success', 'Don validé et ajouté au stock avec succès');

    } catch (\Exception $e) {
        DB::rollBack();
        return redirect()->back()->with('error', 'Erreur lors de la validation : ' . $e->getMessage());
    }
}


    public function storeDonation(Request $request)
{
    // Validation des champs
    $validated = $request->validate([
        'donor_id'           => 'required|exists:users,id',
        'blood_group'        => 'required|string',
        'donation_type'      => 'required|string',
        'quantity_ml'        => 'required|numeric|min:1',
        'hemoglobin_level'   => 'nullable|numeric',
        'blood_pressure'     => 'nullable|string',
        'weight'             => 'nullable|numeric',
        'medical_notes'      => 'nullable|string',
        'status'             => 'required|string',
        'consent_given'      => 'nullable|boolean',
        'medical_check_passed' => 'nullable|boolean',
        'eligibility_verified' => 'nullable|boolean',
    ]);

    //  Ajout automatique de l'agent connecté
    $validated['agent_id'] = auth()->id();

    //  Booléens (checkbox)
    $validated['consent_given']        = $request->has('consent_given');
    $validated['medical_check_passed'] = $request->has('medical_check_passed');
    $validated['eligibility_verified'] = $request->has('eligibility_verified');

    //  Date du don
    $validated['donation_date'] = now();

    //  Enregistrement du don
    $donation = donations::create($validated);

    //  Mise à jour du stock
    $stock = blood_stocks::firstOrCreate(
        ['blood_group' => $validated['blood_group']],
        [
            'quantity_units' => 0,
            'status'         => 'ok',
            'expiry_date'    => now()->addDays(42), // exemple : 42 jours
        ]
    );

    // Ajouter la quantité
    $stock->quantity_units += $validated['quantity_ml'];

    // Mettre à jour le statut si stock faible ou élevé (optionnel)
    if ($stock->quantity_units <= 0) {
        $stock->status = 'empty';
    } elseif ($stock->quantity_units < 500) {
        $stock->status = 'critical';
    } else {
        $stock->status = 'ok';
    }

    $stock->save();

    // Redirection ou réponse JSON
    if ($request->ajax()) {
        return response()->json([
            'success' => true,
            'message' => 'Don enregistré avec succès',
            'donation' => $donation
        ]);
    }

    return redirect()->route('agent.dashboard')->with('success', 'Don enregistré avec succès');
}


    public function show($id)
    {
        $donation = \App\Models\Donations::findOrFail($id);
        return view('agent.donations.show', compact('donation'));
    }


    public function edit($id)
    {
        $donation = donations::findOrFail($id);
        return view('agent.donations.edit', compact('donation'));
    }

    public function destroy($id)
    {
        $donation = donations::findOrFail($id);
        $donation->delete();

        return redirect()->route('agent.donations.index')->with('success', 'Don supprimé avec succès.');
    }

    public function update(Request $request, $id)
{
    // Récupérer le don
    $donation = donations::findOrFail($id);

    // Validation des champs
    $validated = $request->validate([
        'quantity_ml' => 'required|integer|min:1',
        'weight' => 'required|numeric|min:1',
        'blood_pressure' => 'required|string|max:10',
        'hemoglobin_level' => 'required|numeric|min:1',
        'medical_notes' => 'nullable|string',
    ]);

    //  Mise à jour
    try {
        $donation->update($validated);

        return redirect()->route('agent.donations.show', $donation->id)
                         ->with('success', 'Don mis à jour avec succès.');
    } catch (\Exception $e) {
        return redirect()->back()
                         ->with('error', 'Erreur lors de la mise à jour : ' . $e->getMessage())
                         ->withInput();
    }
}

}
