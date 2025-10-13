@extends('layouts.donor')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2><i class="bi bi-calendar-event me-2"></i>Mes Rendez-vous</h2>
                <button class="btn btn-primary">
                    <i class="bi bi-plus-circle me-1"></i>Nouveau Rendez-vous
                </button>
            </div>

            <div class="row">
                @forelse($upcomingAppointments as $appointment)
                    <div class="col-lg-6 mb-4">
                        <div class="card h-100 shadow-sm">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-start mb-3">
                                    <h5 class="card-title text-primary">
                                        {{ $appointment->campaign->title ?? $appointment->campaign_title ?? 'Rendez-vous standard' }}
                                    </h5>
                                    <span class="badge bg-{{ $appointment->status == 'scheduled' ? 'success' : ($appointment->status == 'completed' ? 'info' : 'warning') }}">
                                        @switch($appointment->status)
                                            @case('scheduled') Planifié @break
                                            @case('completed') Complété @break
                                            @case('cancelled') Annulé @break
                                            @case('no_show') Absent @break
                                            @default {{ $appointment->status }}
                                        @endswitch
                                    </span>
                                </div>
                                
                                <div class="appointment-details">
                                    <div class="d-flex align-items-center mb-2">
                                        <i class="bi bi-calendar3 text-info me-3"></i>
                                        <span class="fw-bold">{{ $appointment->appointment_date->format('d/m/Y') }}</span>
                                    </div>
                                    
                                    <div class="d-flex align-items-center mb-2">
                                        <i class="bi bi-clock text-info me-3"></i>
                                        <span>{{ $appointment->appointment_date->format('H:i') }}</span>
                                    </div>
                                    
                                    <div class="d-flex align-items-center mb-3">
                                        <i class="bi bi-geo-alt text-info me-3"></i>
                                        <span>{{ $appointment->campaign->location ?? $appointment->location ?? 'Lieu à préciser' }}</span>
                                    </div>

                                    @if($appointment->notes)
                                        <div class="d-flex align-items-start mb-3">
                                            <i class="bi bi-sticky text-info me-3"></i>
                                            <span class="text-muted">{{ $appointment->notes }}</span>
                                        </div>
                                    @endif
                                </div>
                                
                                <div class="card-actions d-flex gap-2">
                                    @if($appointment->status == 'scheduled')
                                        <button class="btn btn-outline-primary btn-sm">
                                            <i class="bi bi-pencil me-1"></i>Modifier
                                        </button>
                                        <button class="btn btn-outline-danger btn-sm">
                                            <i class="bi bi-x-circle me-1"></i>Annuler
                                        </button>
                                    @endif
                                    <button class="btn btn-outline-info btn-sm">
                                        <i class="bi bi-info-circle me-1"></i>Détails
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body text-center py-5">
                                <i class="bi bi-calendar-x display-1 text-muted mb-3"></i>
                                <h4 class="text-muted">Aucun rendez-vous planifié</h4>
                                <p class="text-muted mb-4">Vous n'avez pas encore de rendez-vous pour vos dons.</p>
                                <button class="btn btn-primary">
                                    <i class="bi bi-plus-circle me-2"></i>Planifier mon premier rendez-vous
                                </button>
                            </div>
                        </div>
                    </div>
                @endforelse
            </div>

            <!-- Historique des anciens rendez-vous -->
            <div class="card mt-5">
                <div class="card-header bg-light">
                    <h5 class="mb-0">
                        <i class="bi bi-clock-history me-2"></i>Historique des Rendez-vous
                    </h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Campagne</th>
                                    <th>Lieu</th>
                                    <th>Statut</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($pastAppointments as $appointment)
                                    <tr>
                                        <td>{{ $appointment->appointment_date->format('d/m/Y') }}</td>
                                        <td>{{ $appointment->campaign->title ?? $appointment->campaign_title ?? 'Rendez-vous standard' }}</td>
                                        <td>{{ $appointment->campaign->location ?? $appointment->location ?? 'N/A' }}</td>
                                        <td>
                                            <span class="badge bg-{{ $appointment->status == 'completed' ? 'success' : ($appointment->status == 'cancelled' ? 'danger' : 'warning') }}">
                                                @switch($appointment->status)
                                                    @case('completed') Complété @break
                                                    @case('cancelled') Annulé @break
                                                    @case('no_show') Absent @break
                                                    @default {{ $appointment->status }}
                                                @endswitch
                                            </span>
                                        </td>
                                        <td>
                                            <button class="btn btn-sm btn-outline-info">
                                                <i class="bi bi-eye"></i>
                                            </button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center text-muted">
                                            Aucun rendez-vous dans l'historique
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.appointment-details i {
    width: 20px;
}

.card:hover {
    transform: translateY(-2px);
    transition: transform 0.2s ease;
}

.badge {
    font-size: 0.75em;
}
</style>
@endsection