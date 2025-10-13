<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules\Password;
use App\Models\donations;
use App\Models\appointments;
use App\Models\User;

class ProfileController extends Controller
{
    /**
     * Afficher le profil du donneur
     */
    public function show()
    {
        $user = Auth::user();
        
        // Statistiques du donneur
        $stats = $this->getUserStats($user);
        
        // Dernières activités
        $recentDonations = donations::where('donor_id', $user->id)
            ->with('campaign')
            ->latest('donation_date')
            ->limit(3)
            ->get();
        
        $upcomingAppointments = appointments::where('donor_id', $user->id)
            ->where('appointment_date', '>', now())
            ->with('campaign')
            ->orderBy('appointment_date')
            ->limit(3)
            ->get();
        
        return view('donor.profile.show', compact('user', 'stats', 'recentDonations', 'upcomingAppointments'));
    }

    /**
     * Afficher le formulaire d'édition du profil
     */
    public function edit()
    {
        $user = Auth::user();
        return view('donor.profile.edit', compact('user'));
    }

    /**
     * Mettre à jour les informations personnelles
     */
    public function update(Request $request)
    {
        $user = Auth::user();
        
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'phone' => 'nullable|string|max:20',
            'birth_date' => 'nullable|date|before:today',
            'gender' => 'required|in:M,F',
            'blood_type' => 'nullable|in:A+,A-,B+,B-,AB+,AB-,O+,O-',
            'weight' => 'nullable|numeric|min:50|max:200',
            'height' => 'nullable|numeric|min:140|max:220',
            'address' => 'nullable|string|max:500',
            'city' => 'nullable|string|max:100',
            'emergency_contact_name' => 'nullable|string|max:255',
            'emergency_contact_phone' => 'nullable|string|max:20',
            'medical_conditions' => 'nullable|string|max:1000',
            'medications' => 'nullable|string|max:1000',
        ]);

        $user->update($validatedData);

        return redirect()->route('donor.profile.show')->with('success', 'Profil mis à jour avec succès !');
    }

    /**
     * Mettre à jour le mot de passe
     */
    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required|current_password',
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);

        Auth::user()->update([
            'password' => Hash::make($request->password),
        ]);

        return back()->with('success', 'Mot de passe mis à jour avec succès !');
    }

    /**
     * Mettre à jour la photo de profil
     */
    public function updateAvatar(Request $request)
    {
        $request->validate([
            'avatar' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        $user = Auth::user();

        // Supprimer l'ancienne photo si elle existe
        if ($user->avatar && Storage::exists('public/avatars/' . $user->avatar)) {
            Storage::delete('public/avatars/' . $user->avatar);
        }

        // Sauvegarder la nouvelle photo
        $fileName = time() . '_' . $user->id . '.' . $request->avatar->extension();
        $request->avatar->storeAs('public/avatars', $fileName);

        $user->update(['avatar' => $fileName]);

        return back()->with('success', 'Photo de profil mise à jour avec succès !');
    }

    /**
     * Afficher l'historique médical
     */
    public function medicalHistory()
    {
        $user = Auth::user();
        
        $donations = donations::where('donor_id', $user->id)
            ->with('campaign')
            ->orderBy('donation_date', 'desc')
            ->paginate(10);

        $stats = $this->getUserStats($user);

        return view('donor.profile.medical-history', compact('donations', 'stats'));
    }

    /**
     * Gérer les préférences de notification
     */
    public function notifications()
    {
        $user = Auth::user();
        return view('donor.profile.notifications', compact('user'));
    }

    /**
     * Mettre à jour les préférences de notification
     */
    public function updateNotifications(Request $request)
    {
        $validatedData = $request->validate([
            'email_notifications' => 'boolean',
            'sms_notifications' => 'boolean',
            'reminder_notifications' => 'boolean',
            'campaign_notifications' => 'boolean',
        ]);

        Auth::user()->update($validatedData);

        return back()->with('success', 'Préférences de notification mises à jour !');
    }

    /**
     * Supprimer le compte
     */
    public function destroy(Request $request)
    {
        $request->validate([
            'password' => 'required|current_password',
        ]);

        $user = Auth::user();

        // Supprimer la photo de profil si elle existe
        if ($user->avatar && Storage::exists('public/avatars/' . $user->avatar)) {
            Storage::delete('public/avatars/' . $user->avatar);
        }

        // Anonymiser les données au lieu de supprimer
        $user->update([
            'name' => 'Utilisateur supprimé',
            'email' => 'deleted_' . $user->id . '@deleted.com',
            'phone' => null,
            'avatar' => null,
            'deleted_at' => now(),
        ]);

        Auth::logout();

        return redirect()->route('home')
            ->with('success', 'Votre compte a été supprimé avec succès.');
    }

    /**
     * Calculer les statistiques de l'utilisateur
     */
    private function getUserStats($user)
    {
        $totalDonations = donations::where('donor_id', $user->id)
            ->where('status', 'completed')
            ->count();

        $totalVolume = donations::where('donor_id', $user->id)
            ->where('status', 'completed');
            

        $lastDonation = donations::where('donor_id', $user->id)
            ->where('status', 'completed')
            ->latest('donation_date')
            ->first();

        $nextEligibleDate = null;
        if ($lastDonation) {
            $interval = ($user->gender === 'male') ? 56 : 84;
            $nextEligibleDate = $lastDonation->donation_date->addDays($interval);
        }

        $donationsThisYear = donations::where('donor_id', $user->id)
            ->where('status', 'completed')
            ->whereYear('donation_date', now()->year)
            ->count();

        return (object) [
            'total_donations' => $totalDonations,
            'total_volume' => $totalVolume,
            'last_donation' => $lastDonation,
            'next_eligible_date' => $nextEligibleDate,
            'donations_this_year' => $donationsThisYear,
            'can_donate' => $nextEligibleDate ? $nextEligibleDate <= now() : true,
        ];
    }

    /**
     * Exporter les données personnelles (RGPD)
     */
    public function exportData()
    {
        $user = Auth::user();
        
        $donations = donations::where('donor_id', $user->id)->get();
        $appointments = appointments::where('donor_id', $user->id)->get();

        $data = [
            'user_info' => $user->toArray(),
            'donations' => $donations->toArray(),
            'appointments' => $appointments->toArray(),
            'exported_at' => now()->toISOString(),
        ];

        $filename = 'mes_donnees_' . $user->id . '_' . now()->format('Y_m_d') . '.json';

        return response()->json($data)
            ->header('Content-Disposition', 'attachment; filename="' . $filename . '"')
            ->header('Content-Type', 'application/json');
    }
}