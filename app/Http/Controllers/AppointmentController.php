<?php

namespace App\Http\Controllers;

use App\Models\appointments;
use App\Models\User;
use Illuminate\Http\Request;

class AppointmentController extends Controller
{
    // public function index()
    // {
        // $appointments = appointments::with('user')->latest()->get();
        // return view('appointments.index', compact('appointments'));
    // }

    // public function create()
    // {
        // $donors = User::where('role', 'donor')->get();
        // return view('appointments.create', compact('donors'));
    // }

    public function store(Request $request)
    {
        $request->validate([
            'donor_id' => 'required|exists:users,id',
            'campaign_id' => 'required|exists:campaigns,id' ,
            'appointment_date' => 'required|date',
            'status' => 'in:pending,confirmed,completed,cancelled',
            'notes' => 'required|string|max:255',
        ]);

        appointments::create($request->all());

        return redirect()->route('appointments.index')->with('success', 'Rendez-vous créé avec succès.');
    }

    public function show(appointments $appointment)
    {
        return view('appointments.show', compact('appointment'));
    }

    public function edit(appointments $appointment)
    {
        $donors = User::where('role', 'donor')->get();
        return view('appointments.edit', compact('appointment', 'donors'));
    }

    public function update(Request $request, appointments $appointment)
    {
        $request->validate([
            'donor_id' => 'required|exists:users,id',
            'campaign_id' => 'required|exists:campaigns,id' ,
            'appointment_date' => 'required|date',
            'status' => 'in:pending,confirmed,completed,cancelled',
            'notes' => 'required|string|max:255',
        ]);

        $appointment->update($request->all());

        return redirect()->route('appointments.index')->with('success', 'Rendez-vous mis à jour avec succès.');
    }

    public function destroy(appointments $appointment)
    {
        $appointment->delete();

        return redirect()->route('appointments.index')->with('success', 'Rendez-vous supprimé avec succès.');
    }
}