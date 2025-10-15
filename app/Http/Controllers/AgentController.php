<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\donations;
use App\Models\blood_stocks;
use App\Models\User;
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
    public function createDonation()
    {
        $donors = User::where('role', 'agent')->get();
        return view('agent.donations.create', compact('donors'));
    }
}