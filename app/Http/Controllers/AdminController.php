<?php

namespace App\Http\Controllers;


use App\Models\User;
use App\Models\donations;
use App\Models\blood_stocks;
use Illuminate\Support\Facades\Auth;
//use Illuminate\Validation\ValidationException;
use App\Services\BloodStockService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

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


        /**
     * Rapport des utilisateurs
     */
public function usersReport(Request $request)
{
    // Filtres par défaut
    $startDate = $request->input('start_date', now()->subMonths(6)->format('Y-m-d'));
    $endDate = $request->input('end_date', now()->format('Y-m-d'));
    $role = $request->input('role', 'all');

    // Statistiques globales
   $stats = [
    'total_users' => User::count(),
    'total_donors' => User::where('role', 'donor')->count(),
    'active_donors' => User::where('role', 'donor')->where('is_active', 1)->count(),
    'total_agents' => User::where('role', 'agent')->count(),
    'total_admins' => User::where('role', 'admin')->count(),
    'new_this_month' => User::whereMonth('created_at', now()->month)
                            ->whereYear('created_at', now()->year)
                            ->count(),
];


    // Inscriptions par mois (12 derniers mois)
    $registrationsByMonth = [];
    for ($i = 11; $i >= 0; $i--) {
        $date = now()->subMonths($i);
        $count = User::whereYear('created_at', $date->year)
                    ->whereMonth('created_at', $date->month)
                    ->count();

        $registrationsByMonth[] = [
            'month' => $date->format('M Y'),
            'count' => $count,
        ];
    }

    // Utilisateurs par rôle
    $usersByRole = [
        'donors' => User::where('role', 'donor')->count(),
        'agents' => User::where('role', 'agent')->count(),
        'admins' => User::where('role', 'admin')->count(),
    ];

    // Donneurs par groupe sanguin
    $donorsByBloodGroup = User::where('role', 'donor')
        ->select(DB::raw('COALESCE(blood_group,"Non spécifié") as blood_group'), DB::raw('COUNT(*) as total'))
        ->groupBy('blood_group')
        ->orderBy('blood_group')
        ->pluck('total', 'blood_group')
        ->toArray();

    // Liste des utilisateurs avec filtres
    $usersQuery = User::query();

    if ($role !== 'all') {
        $usersQuery->where('role', $role);
    }

    $usersQuery->whereBetween('created_at', [
        Carbon::parse($startDate)->startOfDay(),
        Carbon::parse($endDate)->endOfDay()
    ]);

    $users = $usersQuery
        ->orderBy('created_at', 'desc')
        ->paginate(20)
        ->appends($request->all());

    // Donneurs les plus actifs (top 10)
    $topDonors = User::where('role', 'donor')
        ->withCount('donations')
        ->orderBy('donations_count', 'desc')
        ->limit(10)
        ->get();

    // Taux de conversion (donneurs actifs vs totaux)
    $conversionRate = $stats['total_donors'] > 0 
        ? round(($stats['active_donors'] / $stats['total_donors']) * 100, 1) 
        : 0;

    return view('admin.reports.users', compact(
        'stats',
        'registrationsByMonth',
        'usersByRole',
        'donorsByBloodGroup',
        'users',
        'topDonors',
        'conversionRate',
        'startDate',
        'endDate',
        'role'
    ));
}

    /**
     * Rapport des dons
     */
public function donationsReport(Request $request) 
{
    // Filtres
    $startDate = $request->input('start_date', now()->subMonths(3)->format('Y-m-d'));
    $endDate = $request->input('end_date', now()->format('Y-m-d'));
    $bloodGroup = $request->input('blood_group', 'all');
    $status = $request->input('status', 'all');

    // Statistiques globales
    $stats = [
        'total_donations' => donations::count(),
        'total_quantity' => donations::sum('quantity_ml'),
        'donations_period' => donations::whereBetween('donation_date', [$startDate, $endDate])->count(),
        'validated' => donations::where('status', 'validated')->count(),
        'pending' => donations::where('status', 'pending')->count(),
        'average_quantity' => round(donations::avg('quantity_ml'), 0),
        'today_donations' => donations::whereDate('donation_date', today())->count(),
    ];

    // Dons par mois (12 derniers mois)
    $donationsByMonth = [];
    for ($i = 11; $i >= 0; $i--) {
        $date = now()->subMonths($i);
        $count = donations::whereYear('donation_date', $date->year)
                          ->whereMonth('donation_date', $date->month)
                          ->count();
        $quantity = donations::whereYear('donation_date', $date->year)
                             ->whereMonth('donation_date', $date->month)
                             ->sum('quantity_ml');
        
        $donationsByMonth[] = [
            'month' => $date->format('M Y'),
            'count' => $count,
            'quantity' => $quantity,
        ];
    }

    // Dons par groupe sanguin
    $donationsByBloodGroup = donations::select('blood_group', DB::raw('count(*) as total'), DB::raw('sum(quantity_ml) as quantity'))
        ->groupBy('blood_group')
        ->orderBy('blood_group')
        ->get()
        ->map(function($item) {
            return [
                'blood_group' => $item->blood_group,
                'total' => $item->total,
                'quantity' => $item->quantity,
                'percentage' => 0, // Calculé après
            ];
        });

    $totalDonations = $donationsByBloodGroup->sum('total');
    $donationsByBloodGroup = $donationsByBloodGroup->map(function($item) use ($totalDonations) {
        $item['percentage'] = $totalDonations > 0 ? round(($item['total'] / $totalDonations) * 100, 1) : 0;
        return $item;
    });

    // Dons par type
    $donationsByType = donations::select('donation_type', DB::raw('count(*) as total'))
        ->groupBy('donation_type')
        ->get()
        ->pluck('total', 'donation_type')
        ->toArray();

    // Agents les plus actifs
    $topAgents = User::where('role', 'agent')
        ->withCount(['agentDonations'])
        ->orderBy('agent_donations_count', 'desc')
        ->limit(10)
        ->get();

    // Liste des dons avec filtres
    $donationsQuery = donations::with(['donor', 'agent'])
        ->whereBetween('donation_date', [$startDate, $endDate]);

    if ($bloodGroup !== 'all') {
        $donationsQuery->where('blood_group', $bloodGroup);
    }

    if ($status !== 'all') {
        $donationsQuery->where('status', $status);
    }

    $donations = $donationsQuery->orderBy('donation_date', 'desc')->paginate(20);

    // Taux de validation
    $validationRate = $stats['total_donations'] > 0 
        ? round(($stats['validated'] / $stats['total_donations']) * 100, 1) 
        : 0;

    // Moyenne de dons par jour
    $daysDiff = Carbon::parse($startDate)->diffInDays(Carbon::parse($endDate)) ?: 1;
    $averagePerDay = round($stats['donations_period'] / $daysDiff, 1);

    return view('admin.reports.donations', compact(
        'stats',
        'donationsByMonth',
        'donationsByBloodGroup',
        'donationsByType',
        'topAgents',
        'donations',
        'validationRate',
        'averagePerDay',
        'startDate',
        'endDate',
        'bloodGroup',
        'status'
    ));
}


    /**
     * Rapport des stocks
     */
  public function stocksReport(Request $request)
{
    // Filtres
    $viewType = $request->input('view', 'current'); // current, expiring, expired

    // Statistiques globales
    $stats = [
        'total_units' => blood_stocks::where('status', 'available')
                              ->where('expiry_date', '>', now())
                              ->sum('quantity_units'),
        'expiring_soon' => blood_stocks::where('status', 'available')
                                  ->whereBetween('expiry_date', [now(), now()->addDays(7)])
                                  ->sum('quantity_units'),
        'expired' => blood_stocks::where('status', 'available')
                            ->where('expiry_date', '<=', now())
                            ->sum('quantity_units'),
        'used_this_month' => blood_stocks::where('status', 'used')
                                    ->whereMonth('updated_at', now()->month)
                                    ->sum('quantity_units'),
        'critical_groups' => 0, // Calculé après
        'optimal_groups' => 0,  // Calculé après
    ];

    // Stocks par groupe sanguin
    $bloodGroups = ['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'];
    $stocksByBloodGroup = [];

    foreach ($bloodGroups as $group) {
        $quantity = blood_stocks::where('blood_group', $group)
                             ->where('status', 'available')
                             ->where('expiry_date', '>', now())
                             ->sum('quantity_units');

        $optimalLevel = $this->getOptimalLevel($group);
        $criticalLevel = $this->getCriticalLevel($group);

        $status = 'optimal';
        if ($quantity <= $criticalLevel) {
            $status = 'critical';
            $stats['critical_groups']++;
        } elseif ($quantity < $optimalLevel * 0.5) {
            $status = 'low';
        } else {
            $stats['optimal_groups']++;
        }

        $stocksByBloodGroup[] = [
            'blood_group' => $group,
            'quantity' => $quantity,
            'optimal_level' => $optimalLevel,
            'critical_level' => $criticalLevel,
            'status' => $status,
            'percentage' => $optimalLevel > 0 ? round(($quantity / $optimalLevel) * 100, 1) : 0,
        ];
    }

    // Stocks expirant bientôt (7 prochains jours)
    $expiringStocks = blood_stocks::where('status', 'available')
        ->whereBetween('expiry_date', [now(), now()->addDays(7)])
        ->orderBy('expiry_date')
        ->get()
        ->map(function($stock) {
            return [
                'blood_group' => $stock->blood_group,
                'bag_number' => $stock->bag_number ?? '-',
                'quantity' => $stock->quantity_units,
                'expiry_date' => $stock->expiry_date->format('d/m/Y'),
                'days_remaining' => now()->diffInDays($stock->expiry_date),
            ];
        });

    // Historique d'utilisation (30 derniers jours)
    $usageHistory = [];
    for ($i = 29; $i >= 0; $i--) {
        $date = now()->subDays($i);
        $used = blood_stocks::where('status', 'used')
                         ->whereDate('updated_at', $date)
                         ->sum('quantity_units');
        
        $usageHistory[] = [
            'date' => $date->format('d/m'),
            'quantity' => $used,
        ];
    }

    // Taux de rotation (turnover rate)
    $totalCollected = donations::whereMonth('donation_date', now()->month)->count();
    $totalUsed = $stats['used_this_month'];
    $turnoverRate = $totalCollected > 0 ? round(($totalUsed / $totalCollected) * 100, 1) : 0;

    // Stocks par localisation
    $stocksByLocation = blood_stocks::where('status', 'available')
        ->where('expiry_date', '>', now())
        ->select('location', DB::raw('count(*) as total'), DB::raw('sum(quantity_units) as quantity'))
        ->groupBy('location')
        ->get();

    // Projection des besoins (basée sur l'utilisation moyenne)
    $averageUsagePerDay = $stats['used_this_month'] / 30;
    $daysOfStock = $stats['total_units'] > 0 ? round($stats['total_units'] / $averageUsagePerDay, 1) : 0;

    return view('admin.reports.stocks', compact(
        'stats',
        'stocksByBloodGroup',
        'expiringStocks',
        'usageHistory',
        'turnoverRate',
        'stocksByLocation',
        'averageUsagePerDay',
        'daysOfStock',
        'viewType'
    ));
}


    /**
     * Niveau optimal par groupe sanguin
     */
    private function getOptimalLevel($bloodGroup)
    {
        $levels = [
            'O+' => 50,
            'A+' => 40,
            'B+' => 30,
            'AB+' => 20,
            'O-' => 30,
            'A-' => 25,
            'B-' => 20,
            'AB-' => 15,
        ];

        return $levels[$bloodGroup] ?? 30;
    }

    /**
     * Niveau critique par groupe sanguin
     */
    private function getCriticalLevel($bloodGroup)
    {
        return (int) ($this->getOptimalLevel($bloodGroup) * 0.2);
    }

}