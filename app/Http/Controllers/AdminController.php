<?php

namespace App\Http\Controllers;


use App\Models\User;
use App\Models\donations;
use App\Models\blood_stocks;
use Illuminate\Support\Facades\Auth;
//use Illuminate\Validation\ValidationException;
use App\Services\BloodStockService;
use Illuminate\Http\Request;
class AdminController extends Controller
{
    protected $bloodStockService;

   public function __construct()
{
    $this->middleware(['auth', 'role:admin']); // applique le middleware ici

    // ⚡ instanciation correcte du service
    $this->bloodStockService = new BloodStockService();
}

   public function login(Request $request)
{
    // Validation
    $credentials = $request->validate([
        'email' => ['required', 'email'],
        'password' => ['required'], 
    ]);

    // Tentative de connexion
    if (Auth::attempt($credentials, $request->boolean('remember'))) {
        $request->session()->regenerate();

        $user = Auth::user();

        return redirect()->route('admin.dashboard');
    }
}

    /**
     * Dashboard administrateur
     */
    public function dashboard()
    {
        
        // Statistiques principales
        $stats = [
            'total_users' => User::count(),
            'total_donors' => User::donors()->count(),
            'total_agents' => User::agents()->count(),
            'active_users' => User::where('is_active', true)->count(),
            'total_donations' => donations::count(),
            'donations_today' => donations::today()->count(),
            'donations_this_week' => donations::thisWeek()->count(),
            'donations_this_month' => donations::whereMonth('donation_date', now()->month)->count(),
            'total_stock' => $this->bloodStockService->getTotalStock(),
            'critical_stocks' => $this->bloodStockService->getCriticalStocks()->count(),
        ];

        // Données pour les graphiques
        $bloodGroups = ['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'];
        $stockData = [];
        
        foreach ($bloodGroups as $group) {
            $stockData[$group] = blood_stocks::where('blood_group', $group)
                                          ->sum('quantity_units');
        }

        // Dons récents
        $recentDonations = donations::with(['donor', 'agent'])
                                  ->latest('donation_date')
                                  ->take(10)
                                  ->get();

        // Alertes critiques
        $criticalStocks = $this->bloodStockService->getCriticalStocks();
        $expiringStocks = $this->bloodStockService->getExpiringStocks();

        // Utilisateurs récents
        $recentUsers = User::latest()
                          ->take(5)
                          ->get();

        return view('admin.dashboard', compact(
            'stats', 
            'stockData', 
            'recentDonations', 
            'criticalStocks', 
            'expiringStocks',
            'recentUsers'
        ));
    }

    /**
     * Données pour les graphiques (AJAX)
     */
    public function getChartData(Request $request)
    {
        $type = $request->get('type', 'monthly');
        
        switch ($type) {
            case 'monthly':
                // Dons par mois de l'année courante
                $data = donations::selectRaw('MONTH(donation_date) as month, COUNT(*) as count')
                               ->whereYear('donation_date', date('Y'))
                               ->groupBy('month')
                               ->pluck('count', 'month')
                               ->toArray();
                
                // Remplir les mois manquants avec 0
                for ($i = 1; $i <= 12; $i++) {
                    if (!isset($data[$i])) {
                        $data[$i] = 0;
                    }
                }
                ksort($data);
                break;
                
            case 'blood_groups':
                // Répartition des dons par groupe sanguin
                $data = donations::selectRaw('blood_group, COUNT(*) as count')
                               ->groupBy('blood_group')
                               ->pluck('count', 'blood_group')
                               ->toArray();
                break;
                
            case 'stock_levels':
                // Niveaux de stock actuels
                $data = blood_stocks::selectRaw('blood_group, SUM(quantity_units) as total')
                                 ->groupBy('blood_group')
                                 ->pluck('total', 'blood_group')
                                 ->toArray();
                break;
                
            case 'weekly':
                // Dons des 7 derniers jours
                $data = [];
                for ($i = 6; $i >= 0; $i--) {
                    $date = now()->subDays($i);
                    $data[$date->format('D')] = donations::whereDate('donation_date', $date)->count();
                }
                break;
                
            default:
                $data = [];
        }
        
        return response()->json($data);
    }

    /**
     * Statistiques générales (AJAX)
     */
    public function getStats()
    {
        return response()->json([
            'users' => [
                'total' => User::count(),
                'active' => User::where('is_active', true)->count(),
                'donors' => User::donors()->count(),
                'agents' => User::agents()->count(),
            ],
            'donations' => [
                'total' => donations::count(),
                'today' => donations::today()->count(),
                'this_week' => donations::thisWeek()->count(),
                'this_month' => donations::whereMonth('donation_date', now()->month)->count(),
            ],
            'stock' => [
                'total' => blood_stocks::sum('quantity_units'),
                'critical' => blood_stocks::where('status', 'critical')->count(),
                'expiring_soon' => blood_stocks::expiringSoon()->count(),
            ]
        ]);
    }
}