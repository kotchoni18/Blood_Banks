<?php

namespace App\Http\Controllers;

use App\Models\appointments;
use App\Models\donations;
use App\Models\User;
// use App\Models\blood_stocks;
use App\Models\campaigns;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class DonorController extends Controller
{

    public function login(Request $request)
    {
        // Validation des données
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        // Tentative de connexion
        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();

            // Redirection après connexion réussie
            return redirect()->intended(route('donor.dashboard'));
        }

        // Si la connexion échoue
        throw ValidationException::withMessages([
            'email' => ['Les informations de connexion sont incorrectes.'],
        ]);
    }

    public function dashboard()
    {        
        $user = Auth::user();
        // Récupérer le dernier don
        $lastDonation = donations::where('donor_id', $user->id)
            ->orderBy('donation_date', 'desc')
            ->first();
        
        // Calculer l'éligibilité pour le prochain don
        $canDonate = $this->canUserDonate($user, $lastDonation);
        $nextEligibleDate = $this->getNextEligibleDate($lastDonation);
        
        // Récupérer le prochain rendez-vous
        $nextAppointment = appointments::where('donor_id', $user->id)
            ->where('appointment_date', '>', now())
            ->orderBy('appointment_date', 'asc')
            ->with('campaign')
            ->first();
        
        // Récupérer les campagnes disponibles
        $availableCampaigns = campaigns::where('start_date', '<=', now())
            ->where('end_date', '>=', now())
            ->where('status', 'active')
            ->get()
            ->map(function ($campaign) {
                $campaign->progress_percentage = $campaign->target_donations > 0 
                    ? ($campaign->current_donations / $campaign->target_donations) * 100 
                    : 0;
                return $campaign;
            });
        
        return view('donor.dashboard', compact(
            'user',
            'canDonate',
            'nextEligibleDate',
            'lastDonation',
            'nextAppointment',
            'availableCampaigns',
           
        ));
    }

    public function index()
    {
        $donors = User::paginate(10);
        return view('donor.dashboard', compact('donors'));
    }

    // public function create()
    // {
        // return view('donor/create');
    // }

    public function store(Request $request)
    {
        $request->validate([
            'first_name'   => 'required|string|max:100',
            'last_name'    => 'required|string|max:100',
            'email'        => 'required|email|unique:donors,email',
            'password'     => 'required|string|min:8|confirmed',
            'role'         => 'required|string',
            'phone'        => 'nullable|string|max:20',
            'birth_date'   => 'nullable|date',
            'gender'       => 'nullable|in:M,F',
            'blood_group'  => 'nullable|string|max:5',
            'address'      => 'nullable|string|max:255',
        ]);

        User::create([
            'first_name'   => $request->first_name,
            'last_name'    => $request->last_name,
            'email'        => $request->email,
            'password'     => Hash::make($request->password),
            'role'         => $request->role,
            'phone'        => $request->phone,
            'birth_date'   => $request->birth_date,
            'gender'       => $request->gender,
            'blood_group'  => $request->blood_group,
            'address'      => $request->address,
            'is_active'    => true,
        ]);

        return redirect()->route('donor.dashboard')->with('success', 'Donneur ajouté avec succès.');
    }

    public function show($donor)
    {
         return view('donors.show', compact('donor'));
    }

    public function edit($donor)
    {
         return view('donors.edit', compact('donor'));
    }

    public function update(Request $request, $donor)
    {
        $request->validate([
             'first_name'   => 'required|string|max:100',
             'last_name'    => 'required|string|max:100',
             'email'        => 'required|email|unique:donors,email,' . $donor->id,
             'role'         => 'required|string',
             'phone'        => 'nullable|string|max:20',
             'birth_date'   => 'nullable|date',
             'gender'       => 'nullable|in:M,F',
             'blood_group'  => 'nullable|string|max:5',
             'address'      => 'nullable|string|max:255',
         ]);

         $donor->update($request->except('password'));

         if ($request->filled('password')) {
             $donor->update(['password' => Hash::make($request->password)]);
         }

        return redirect()->route('donors.index')->with('success', 'Donneur mis à jour avec succès.');
    }

     public function destroy($donor)
     {
        $donor->delete();
         return redirect()->route('donors.index')->with('success', 'Donneur supprimé avec succès.');
    }


    /**
     * Vérifier si l'utilisateur peut faire un don
     */
    private function canUserDonate($user, $lastDonation)
    {
        // Si pas de don précédent, l'utilisateur peut donner
        if (!$lastDonation) {
            return true;
        }
        
        // Vérifier l'intervalle minimum (56 jours pour les hommes, 84 jours pour les femmes)
        $minInterval = $user->gender === 'male' ? 56 : 84;
        $daysSinceLastDonation = $lastDonation->donation_date->diffInDays(now());
        
        return $daysSinceLastDonation >= $minInterval;
    }
    
    /**
     * Calculer la prochaine date d'éligibilité
     */
    private function getNextEligibleDate($lastDonation)
    {
        if (!$lastDonation) {
            return null;
        }
        
        $user = Auth::user();
        $minInterval = $user->gender === 'male' ? 56 : 84;
        
        return $lastDonation->donation_date->addDays($minInterval);
    }


    public function appointments()
    {
        $user = Auth::user();

        
        if (!$user) {
            return redirect()->route('donor-login')->withErrors('Vous devez être connecté pour voir vos rendez-vous.');
        }
        // Rendez-vous à venir
        $upcomingAppointments = appointments::with('campaign')
            ->where('donor_id', $user->id)
            ->where('appointment_date', '>=', now())
            ->orderBy('appointment_date', 'asc')
            ->get();

        // Rendez-vous passés
        $pastAppointments = appointments::with('campaign')
            ->where('donor_id', $user->id)
            ->where('appointment_date', '<', now())
            ->orderBy('appointment_date', 'desc')
            ->get();

        return view('donor.appointments', compact('upcomingAppointments', 'pastAppointments'));
    }

    public function donations()
    {
        // Récupérer les dons du donneur connecté
        $donations = donations::where('donor_id', auth()->id())->latest()->paginate(10);

        // Retourner la vue
        return view('donor.donations.index', compact('donations'));
    }

    public function campaigns()
    {
        // Exemple : récupérer les campagnes actives ou toutes
        $campaigns = \App\Models\campaigns::latest()->paginate(10);

        return view('donor.campaigns', compact('campaigns'));
    }
}