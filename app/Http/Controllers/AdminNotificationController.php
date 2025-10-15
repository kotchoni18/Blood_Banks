<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\blood_stocks;
use App\Models\UserNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminNotificationController extends Controller
{
    // Affiche la liste des stocks et permet de notifier par groupe
    public function criticalStocks()
    {
        // récupérer stocks et considérer "critical" selon ton champ status ou seuil
        $critical = blood_stocks::where('status', 'critical')->get();

        // Exemple: chaque record a blood_group, quantity_units
        return view('admin.notifications.critical_stocks', compact('critical'));
    }

    // Envoie notification aux donneurs d'un groupe (manuel)
    public function notifyGroup(Request $request)
    {
        $request->validate([
            'blood_group' => 'required|string',
            'title' => 'required|string|max:255',
            'message' => 'required|string',
        ]);

        $group = $request->blood_group;
        $title = $request->title;
        $message = $request->message;

        // Récupérer les donneurs du groupe (active donors)
        $donorsQuery = User::where('role', 'donor')
                            ->where('blood_group', $group)
                            ->where('is_active', true);

        $donorCount = $donorsQuery->count();

        if ($donorCount === 0) {
            return redirect()->back()->with('error', "Aucun donneur actif trouvé pour le groupe {$group}.");
        }

        DB::beginTransaction();
        try {
            // Créer notification pour chaque donneur
            $donors = $donorsQuery->get(['id']);
            $now = now();
            $insert = [];
            foreach ($donors as $d) {
                $insert[] = [
                    'user_id' => $d->id,
                    'title' => $title,
                    'message' => $message,
                    'is_read' => false,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }

            // Insert bulk
            UserNotification::insert($insert);

            DB::commit();

            return redirect()->back()->with('success', "Notification envoyée à {$donorCount} donneur(s) du groupe {$group}.");
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('notifyGroup error: '.$e->getMessage());
            return redirect()->back()->with('error', 'Erreur lors de l’envoi des notifications : '.$e->getMessage());
        }
    }
}
