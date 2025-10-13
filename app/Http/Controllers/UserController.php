<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\donations;
use App\Http\Requests\UserRequest;
use Illuminate\Http\Request;
// use App\Services\BloodStockService;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    // public function __construct()
    // {
        // $this->middleware('auth');
        // $this->middleware('admin');
    // }
public function index(Request $request)
{
    //  Préparer la requête filtrée
    $query = User::query();

    if ($request->filled('role')) {
        $query->where('role', $request->role);
    }

    if ($request->filled('status')) {
        $query->where('is_active', $request->status === 'active');
    }

    if ($request->filled('search')) {
        $search = $request->search;
        $query->where(function ($q) use ($search) {
            $q->where('first_name', 'LIKE', "%{$search}%")
                ->orWhere('last_name', 'LIKE', "%{$search}%")
                ->orWhere('email', 'LIKE', "%{$search}%");
        });
    }

    //  Pagination des résultats filtrés
    $users = $query->paginate(15);

    //  Statistiques globales (sans tenir compte des filtres)
    $totalUsers = User::count();
    $totalDonors = User::where('role', 'donor')->count();
    $totalAgents = User::where('role', 'agent')->count();
    $totalAdmins = User::where('role', 'admin')->count();

    //  Si AJAX → JSON
    if ($request->ajax()) {
        return response()->json([
            'users' => $users->items(),
            'pagination' => [
                'current_page' => $users->currentPage(),
                'last_page' => $users->lastPage(),
                'total' => $users->total()
            ]
        ]);
    }

    //  Affichage de la vue
    return view('admin.users.index', compact(
        'users',
        'totalUsers',
        'totalDonors',
        'totalAgents',
        'totalAdmins'
    ));
}


    public function create()
    {
        return view('admin.users.create');
    }

    public function store(UserRequest $request)
    {
        $data = $request->validated();
        $data['password'] = Hash::make($data['password']);

        $user = User::create($data);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Utilisateur créé avec succès',
                'user' => $user
            ]);
        }

        return redirect()->route('admin.users.index')
                        ->with('success', 'Utilisateur créé avec succès');
    }

    public function show(User $user)
    {
        $user->load(['donations', 'appointments']);
        
        if (request()->ajax()) {
            return response()->json($user);
        }

        return view('admin.users.show', compact('user'));
    }

    public function edit(User $user)
    {
        return view('admin.users.edit', compact('user'));
    }

    public function update(UserRequest $request, User $user)
    {
        $data = $request->validated();
        
        if (isset($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }

        $user->update($data);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Utilisateur modifié avec succès',
                'user' => $user
            ]);
        }

        return redirect()->route('admin.users.index')
                        ->with('success', 'Utilisateur modifié avec succès');
    }

    public function destroy(User $user)
    {
        $user->delete();

        if (request()->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Utilisateur supprimé avec succès'
            ]);
        }

        return redirect()->route('admin.users.index')
                        ->with('success', 'Utilisateur supprimé avec succès');
    }

    public function toggleStatus(User $user)
    {
        $user->update(['is_active' => !$user->is_active]);

        return response()->json([
            'success' => true,
            'message' => 'Statut modifié avec succès',
            'status' => $user->is_active
        ]);
    }

    public function userDonations(User $user)
{
    // Récupérer les donations avec pagination
    $donations = $user->donations()
                      ->with('campaign')       // Charger la campagne liée
                      ->latest()               // Trier par date décroissante
                      ->paginate(10);

    return view('admin.users.donations', compact('user', 'donations'));
}

}
