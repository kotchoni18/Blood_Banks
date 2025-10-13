<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\donations;
use App\Models\blood_stocks;
// use App\Models\User;
use App\Services\BloodStockService; use Illuminate\Http\Request;

class AgentController extends Controller
{
    protected $bloodStockService;

    public function __construct(BloodStockService $bloodStockService)
    {
        $this->bloodStockService = $bloodStockService;
    }

    

    /**
     * Dashboard agent médical
     */
    public function dashboard()
    {
        $agent = auth()->user();
        
        // Statistiques de l'agent
        $todayDonations = donations::where('agent_id', $agent->id)
                                 ->today()
                                 ->count();

        $weeklyDonations = donations::where('agent_id', $agent->id)
                                  ->thisWeek()
                                  ->count();

        $monthlyDonations = donations::where('agent_id', $agent->id)
                                   ->whereMonth('donation_date', now()->month)
                                   ->count();

        $totalDonations = donations::where('agent_id', $agent->id)->count();

        // Statistiques générales
        $totalStock = $this->bloodStockService->getTotalStock();
        $criticalStocks = $this->bloodStockService->getCriticalStocks()->count();

        // Données des groupes sanguins
        $bloodGroups = ['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'];
        $stockData = [];
        
        foreach ($bloodGroups as $group) {
            $stock = blood_stocks::where('blood_group', $group)->first();
            $stockData[$group] = [
                'quantity' => $stock ? $stock->quantity_units : 0,
                'status' => $stock ? $stock->status : 'empty',
                'expiry' => $stock ? $stock->expiry_date->format('Y-m-d') : null
            ];
        }

        // Dons récents de l'agent
        $recentDonations = donations::with('donor')
                                  ->where('agent_id', $agent->id)
                                  ->latest('donation_date')
                                  ->take(5)
                                  ->get();

        return view('agent.dashboard', compact(
            'todayDonations',
            'weeklyDonations',
            'monthlyDonations',
            'totalDonations',
            'totalStock',
            'criticalStocks',
            'stockData',
            'recentDonations'
        ));
    }

    /**
     * Données de stock en temps réel (AJAX)
     */
    public function getStockData()
    {
        $bloodGroups = ['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'];
        $stockData = [];
        
        foreach ($bloodGroups as $group) {
            $stock = blood_stocks::where('blood_group', $group)->first();
            
            $stockData[$group] = [
                'quantity' => $stock ? $stock->quantity_units : 0,
                'status' => $stock ? $stock->status : 'empty',
                'expiry' => $stock ? $stock->expiry_date->format('d/m/Y') : null,
                'location' => $stock ? $stock->location : 'N/A',
                'days_until_expiry' => $stock ? $stock->days_until_expiry : null
            ];
        }

        return response()->json($stockData);
    }

    /**
     * Statistiques de l'agent (AJAX)
     */
    public function getAgentStats()
    {
        $agent = auth()->user();
        
        return response()->json([
            'donations' => [
                'today' => donations::where('agent_id', $agent->id)->today()->count(),
                'week' => donations::where('agent_id', $agent->id)->thisWeek()->count(),
                'month' => donations::where('agent_id', $agent->id)->whereMonth('donation_date', now()->month)->count(),
                'total' => donations::where('agent_id', $agent->id)->count(),
            ],
            'blood_groups' => donations::where('agent_id', $agent->id)
                                     ->selectRaw('blood_group, COUNT(*) as count')
                                     ->groupBy('blood_group')
                                     ->pluck('count', 'blood_group'),
            'recent_activity' => donations::with('donor')
                                        ->where('agent_id', $agent->id)
                                        ->latest()
                                        ->take(5)
                                        ->get()
        ]);
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


    public function stocks()
    {
        $stocks = blood_stocks::all();

        return view('agent.stocks.index', compact('stocks'));
    }


    public function history()
    {
        $agent = auth()->user();
        $donations =    donations::with('donor')->where('agent_id', $agent->id)->latest('donation_date')->paginate(10);
        return view('agent.donations.history', compact('donations'));
    }


    public function eligibilityCheck()
    {
        return view('agent.eligibility-check');
    }

        /**
     * Recherche rapide de donneur (AJAX)
     */
    public function quickDonorSearch(Request $request)
    {
        $search = $request->input('search');

        $donors = User::donors()
            ->active()
            ->where(function($query) use ($search) {
                $query->where('first_name', 'like', "%{$search}%")
                      ->orWhere('last_name', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%")
                      ->orWhere('phone', 'like', "%{$search}%");
            })
            ->limit(10)
            ->get()
            ->map(function($donor) {
                return [
                    'id' => $donor->id,
                    'name' => $donor->first_name . ' ' . $donor->last_name,
                    'email' => $donor->email,
                    'phone' => $donor->phone,
                    'blood_group' => $donor->blood_group,
                    'last_donation' => $donor->last_donation_date ? $donor->last_donation_date->format('d/m/Y') : null,
                    'donation_count' => $donor->donation_count,
                    'is_eligible' => $this->isDonorEligible($donor),
                    'next_eligible_date' => $this->getNextEligibleDate($donor),
                ];
            });

        return response()->json($donors);
    }

}