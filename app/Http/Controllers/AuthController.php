<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    /**
     * Afficher le formulaire de connexion
     */
   /*  public function showLogin(Request $request)
    {
        // Si utilisateur déjà connecté, redirection vers son dashboard
        if (Auth::check()) {
            return $this->redirectToDashboard();
        }

        // Déterminer le type d'interface demandé
        $type = $request->get('type', 'general');
        
        switch ($type) {
            case 'donor':
                return redirect()->route('donor.login');
                break;
            case 'agent':
                return redirect()->route('agent.login');
                break;
            case 'admin':
                return redirect()->route('admin.login');
                break;
            default:
                return redirect()->route('donor.login');
                break;
        }
    } */


    public function showLogin()
    {
        if (Auth::check()) {
            return $this->redirectToDashboard();
        }
        
        return view('auth.welcome-login');
    }

    public function logins(Request $request)
    {
        return $this->processLogin($request);
    }

     /* Traitement générique des connexions
     */
    private function processLogin(Request $request, $expectedRole = null)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ], [
            'email.required' => 'L\'email est obligatoire.',
            'email.email' => 'Format d\'email invalide.',
            'password.required' => 'Le mot de passe est obligatoire.',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            throw ValidationException::withMessages([
                'email' => 'Aucun compte trouvé avec cet email.',
            ]);
        }

        if (!$user->is_active) {
            throw ValidationException::withMessages([
                'email' => 'Compte désactivé. Contactez l\'administrateur.',
            ]);
        }

        // Vérification du rôle si spécifié
        if ($expectedRole && $user->role !== $expectedRole) {
            $roleNames = [
                'admin' => 'l\'interface administrateur',
                'agent' => 'l\'interface agent médical',
                'donor' => 'l\'espace donneur'
            ];
            
            throw ValidationException::withMessages([
                'email' => 'Vous n\'avez pas accès à ' . ($roleNames[$expectedRole] ?? 'cette interface') . '.',
            ]);
        }

        if (Auth::attempt($request->only('email', 'password'), $request->boolean('remember'))) {
            $request->session()->regenerate();
            Auth::user()->update(['last_login_at' => now()]);
            
            return $this->redirectToDashboard()
                        ->with('success', $this->getWelcomeMessage(Auth::user()));
        }

        throw ValidationException::withMessages([
            'email' => 'Identifiants incorrects.',
        ]);
    }

    /**
    * Afficher le formulaire d'inscription
    */
    public function showRegister()
    {
        if (Auth::check()) {
            return $this->redirectToDashboard();
        }
        
        return view('auth.register');
    }

    // Formulaire d'inscription agent (admin uniquement)
    public function showAgentRegister()
    {
        return view('auth.register-agent');
    }

    // Formulaire d'inscription admin (admin uniquement)
    public function showAdminRegister()
    {
        return view('auth.register-admin');
    }

    public function donorLogin(){
        // Si utilisateur déjà connecté, redirection vers son dashboard
        
            return view("auth.donor-login");
         
    }
    public function agentLogin(){
        // Si utilisateur déjà connecté, redirection vers son dashboard
        
            return view("auth.admin-login");
        
    }

    public function adminLogin(){
        // Si utilisateur déjà connecté, redirection vers son dashboard
        
            return view("auth.admin-login");
        
    }
    /**
     * Traiter la connexion
     */
    public function login(Request $request)
    {
        // Validation des données
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ], [
            'email.required' => 'L\'email est obligatoire.',
            'email.email' => 'Format d\'email invalide.',
            'password.required' => 'Le mot de passe est obligatoire.',
        ]);

        // Récupérer l'utilisateur
        $user = User::where('email', $request->email)->first();
        var_dump($user);
        // Vérifications préliminaires
        if (!$user) {
            throw ValidationException::withMessages([
                'email' => 'Aucun compte trouvé avec cet email.',
            ]);
        }

        if (!$user->is_active) {
            throw ValidationException::withMessages([
                'email' => 'Votre compte est désactivé. Contactez l\'administrateur.',
            ]);
        }

        // Vérification du rôle si spécifié
        $expectedRole = $request->get('role');
        if ($expectedRole && $user->role !== $expectedRole) {
            $roleNames = [
                'admin' => 'administrateur',
                'agent' => 'agent médical', 
                'donor' => 'donor'
            ]; 
            
            throw ValidationException::withMessages([
                'email' => 'Vous n\'avez pas les permissions d\'accès à l\'interface ' . ($roleNames[$expectedRole] ?? $expectedRole) . '.',
            ]);
        }

        // Tentative de connexion
        if (Auth::attempt($request->only('email', 'password'), $request->boolean('remember'))) {
            $request->session()->regenerate();
            
            // Mise à jour dernière connexion
            Auth::user()->update(['last_login_at' => now()]);
            
            // Message personnalisé
            $message = $this->getWelcomeMessage(Auth::user());
            
            return $this->redirectToDashboard()->with('success', $message);
        }

        throw ValidationException::withMessages([
            'email' => 'Les identifiants fournis sont incorrects.',
        ]);
    }

    /**
     * Connexion rapide pour démonstration
     */
    public function quickLogin(Request $request, $role = null)
    {
        // Si pas de rôle dans l'URL, essayer de le récupérer des paramètres
        if (!$role) {
            $role = $request->get('role');
        }

        $credentials = match($role) {
            'admin' => ['email' => 'admin@bloodbank.com', 'password' => 'password'],
            'agent' => ['email' => 'marie.dubois@bloodbank.com', 'password' => 'password'],
            'donor' => ['email' => 'jean.martin@email.com', 'password' => 'password'],
            default => null
        };

        if ($credentials && Auth::attempt($credentials)) {
            $request->session()->regenerate();
            Auth::user()->update(['last_login_at' => now()]);
            
            $message = 'Connexion de démonstration réussie ! Bienvenue ' . Auth::user()->first_name;
            return $this->redirectToDashboard()->with('success', $message);
        }

        return redirect()->route('home')->with('error', 'Impossible de se connecter en mode démonstration.');
    }



    /**
     * Déconnexion
     */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        return redirect()->route('home')->with('success', 'Vous avez été déconnecté avec succès.');
    }


    /**
    * Déconnexion
    */
    /*
    public function logout(Request $request)
    {
        $userRole = Auth::user() ? Auth::user()->role : null;
        
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        // Redirection selon le rôle précédent
        $redirectType = match($userRole) {
            'admin' => 'admin',
            'agent' => 'agent', 
            'donor' => 'donor',
            default => null
        };

        $route = $redirectType ? route('home', ['type' => $redirectType]) : route('home');
        
        return redirect($route)->with('success', 'Vous avez été déconnecté avec succès.');
    } */

    /**
     * Inscription d'un ADMIN (admin uniquement)
     */
    public function registerAdmin(Request $request)
    {
        // Vérifier que l'utilisateur connecté est admin
        if (!Auth::check() || !Auth::user()->isAdmin()) {
            abort(403, 'Accès non autorisé');
        }

        $validator = Validator::make($request->all(), [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'phone' => 'nullable|string|max:20',
            'super_admin' => 'nullable|boolean',
            'password' => 'required|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            $user = User::create([
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'email' => $request->email,
                'phone' => $request->phone,
                'super_admin' => $request->super_admin ?? false,
                'password' => Hash::make($request->password),
                'role' => 'admin',
                'is_active' => true,
            ]);

            return redirect()->route('admin.users.index')
                ->with('success', 'Administrateur créé avec succès');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Erreur lors de la création de l\'administrateur')
                ->withInput();
        }
    }

   /**
     * Inscription d'un DONNEUR (accès public)
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'phone' => 'required|string|max:20',
            'birth_date' => 'required|date|before:today',
            'gender' => 'required|in:M,F,O',
            'blood_group' => 'required|in:A+,A-,B+,B-,AB+,AB-,O+,O-',
            'password' => 'required|string|min:8|confirmed',
        ], [
            'first_name.required' => 'Le prénom est obligatoire',
            'last_name.required' => 'Le nom est obligatoire',
            'email.required' => 'L\'email est obligatoire',
            'email.unique' => 'Cet email est déjà utilisé',
            'phone.required' => 'Le téléphone est obligatoire',
            'birth_date.required' => 'La date de naissance est obligatoire',
            'birth_date.before' => 'La date de naissance doit être antérieure à aujourd\'hui',
            'gender.required' => 'Le genre est obligatoire',
            'blood_group.required' => 'Le groupe sanguin est obligatoire',
            'password.required' => 'Le mot de passe est obligatoire',
            'password.min' => 'Le mot de passe doit contenir au moins 8 caractères',
            'password.confirmed' => 'Les mots de passe ne correspondent pas',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            $user = User::create([
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'email' => $request->email,
                'phone' => $request->phone,
                'birth_date' => $request->birth_date,
                'gender' => $request->gender,
                'blood_group' => $request->blood_group,
                'password' => Hash::make($request->password),
                'role' => 'donor',
                'is_active' => true,
                'donation_count' => 0,
            ]);

            // Connexion automatique après inscription
            Auth::login($user);

            return redirect()->route('donor.dashboard')
                ->with('success', 'Inscription réussie ! Bienvenue ' . $user->first_name);

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Une erreur est survenue lors de l\'inscription. Veuillez réessayer.')
                ->withInput();
        }
    }

    /**
     * Redirection vers le bon dashboard
     */
    private function redirectToDashboard()
    {
        $user = Auth::user();
        
        return match($user->role) {
            'admin' => redirect()->route('admin.dashboard'),
            'agent' => redirect()->route('agent.dashboard'),
            'donor' => redirect()->route('donor.dashboard'),
            default => redirect()->route('home'),
        };
    }

    /**
     * Message de bienvenue personnalisé
     */
    private function getWelcomeMessage(User $user)
    {
        return match($user->role) {
            'admin' => "Bienvenue dans l'interface d'administration, {$user->first_name} ! Accès complet au système.",
            'agent' => "Bienvenue dans votre espace médical, Dr. {$user->last_name} ! Prêt pour les consultations.",
            'donor' => "Bienvenue {$user->first_name} ! Merci pour votre précieux engagement.",
            default => "Bienvenue {$user->first_name} !"
        };
    }
}

