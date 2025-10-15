<?php

namespace App\Http\Controllers;

use App\Models\UserNotification;
use Illuminate\Http\Request;

class UserNotificationController extends Controller
{
    // Liste des notifications pour l'utilisateur connecté
    public function index(Request $request)
    {
        $user = $request->user();
        $notifications = $user->notificationsInApp()->latest()->paginate(20);

        return view('donor.notifications.index', compact('notifications'));
    }

    // Marquer comme lu
    public function markAsRead($id, Request $request)
    {
        $user = $request->user();

        $notification = UserNotification::where('id', $id)->where('user_id', $user->id)->firstOrFail();
        $notification->is_read = true;
        $notification->save();

        return redirect()->back()->with('success', 'Notification marquée comme lue.');
    }
}
