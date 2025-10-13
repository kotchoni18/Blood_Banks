@extends('layouts.donor')

@section('title', 'Mon Profil')

@section('content')
<div class="container">
    <div class="row">
        <!-- Sidebar Navigation -->
        <div class="col-lg-3 mb-4">
            <div class="card">
                <div class="card-body text-center">
                    <!-- Photo de profil -->
                    <div class="profile-avatar mb-3">
                        @if($user->avatar)
                            <img src="{{ asset('storage/avatars/' . $user->avatar) }}" 
                                 alt="Photo de profil" 
                                 class="rounded-circle" 
                                 width="120" 
                                 height="120"
                                 style="object-fit: cover;">
                        @else
                            <div class="bg-primary rounded-circle d-flex align-items-center justify-content-center text-white" 
                                 style="width: 120px; height: 120px; font-size: 48px;">
                                {{ strtoupper(substr($user->name, 0, 1)) }}
                            </div>
                        @endif
                    </div>
                    
                    <h5 class="mb-1">{{ $user->name }}</h5>
                    <p class="text-muted mb-0">{{ $user->email }}</p>
                    @if($user->blood_type)
                        <span class="badge bg-danger mt-2">Groupe {{ $user->blood_type }}</span>
                    @endif
                </div>
            </div>

            <!-- Menu Navigation -->
            <div class="card mt-3">
                <div class="list-group list-group-flush">
                    <a href="{{ route('donor.profile.show') }}" class="list-group-item list-group-item-action active">
                        <i class="bi bi-person me-2"></i>Aperçu du profil
                    </a>

                </div>
            </div>
        </div>

        <!-- Contenu principal -->
        <div class="col-lg-9">
            <!-- Alertes de succès -->
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show">
                    <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <!-- Statistiques de don -->
            <div class="row mb-4">
                <div class="col-md-3 mb-3">
                    <div class="card text-center h-100">
                        <div class="card-body">
                            <i class="bi bi-droplet display-4 text-danger mb-2"></i>
                            <h4 class="text-primary">{{ $stats->total_donations }}</h4>
                            <p class="text-muted mb-0">Dons Total</p>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-3 mb-3">
                    <div class="card text-center h-100">
                        <div class="card-body">
                            <i class="bi bi-heart display-4 text-info mb-2"></i>
                            <h4 class="text-primary"></h4>
                            <p class="text-muted mb-0">Volume Total</p>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-3 mb-3">
                    <div class="card text-center h-100">
                        <div class="card-body">
                            <i class="bi bi-calendar-check display-4 text-success mb-2"></i>
                            <h4 class="text-primary">{{ $stats->donations_this_year }}</h4>
                            <p class="text-muted mb-0">Cette Année</p>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-3 mb-3">
                    <div class="card text-center h-100">
                        <div class="card-body">
                            @if($stats->can_donate)
                                <i class="bi bi-check-circle display-4 text-success mb-2"></i>
                                <h5 class="text-success">Éligible</h5>
                                <p class="text-muted mb-0">Peut donner</p>
                            @else
                                <i class="bi bi-clock display-4 text-warning mb-2"></i>
                                <h6 class="text-warning">{{ $stats->next_eligible_date->format('d/m/Y') }}</h6>
                                <p class="text-muted mb-0">Prochaine éligibilité</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Informations personnelles -->
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">
                        <i class="bi bi-person-vcard me-2"></i>Informations Personnelles
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>Nom :</strong> {{ $user->name }}</p>
                            <p><strong>Email :</strong> {{ $user->email }}</p>
                            <p><strong>Téléphone :</strong> {{ $user->phone ?? 'Non renseigné' }}</p>
                            <p><strong>Genre :</strong> 
                                {{ $user->gender == 'male' ? 'Homme' : ($user->gender == 'female' ? 'Femme' : 'Non renseigné') }}
                            </p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Date de naissance :</strong> 
                                {{ $user->birth_date ? $user->birth_date->format('d/m/Y') : 'Non renseignée' }}
                            </p>
                            <p><strong>Groupe sanguin :</strong> {{ $user->blood_type ?? 'Non déterminé' }}</p>
                            <p><strong>Poids :</strong> {{ $user->weight ? $user->weight . ' kg' : 'Non renseigné' }}</p>
                            <p><strong>Ville :</strong> {{ $user->city ?? 'Non renseignée' }}</p>
                        </div>
                    </div>
                    
                    @if($user->address)
                        <hr>
                        <p><strong>Adresse :</strong> {{ $user->address }}</p>
                    @endif
                    
                    @if($user->emergency_contact_name)
                        <hr>
                        <div class="alert alert-info">
                            <strong>Contact d'urgence :</strong> {{ $user->emergency_contact_name }}
                            @if($user->emergency_contact_phone)
                                - {{ $user->emergency_contact_phone }}
                            @endif
                        </div>
                    @endif
                </div>
            </div>

            <!-- Dernières activités -->
            <div class="row">
                <!-- Derniers dons -->
                <div class="col-lg-6 mb-4">
                    <div class="card h-100">
                        <div class="card-header bg-light">
                            <h6 class="mb-0">
                                <i class="bi bi-droplet me-2"></i>Derniers Dons
                            </h6>
                        </div>
                        <div class="card-body">
                            @forelse($recentDonations as $donation)
                                <div class="d-flex align-items-center mb-3">
                                    <div class="bg-danger rounded-circle p-2 me-3">
                                        <i class="bi bi-droplet text-white"></i>
                                    </div>
                                    <div class="flex-grow-1">
                                        <h6 class="mb-1">{{ $donation->donation_type }}</h6>
                                        <small class="text-muted">
                                            {{ $donation->donation_date->format('d/m/Y') }}
                                            @if($donation->campaign)
                                                - {{ $donation->campaign->title }}
                                            @endif
                                        </small>
                                    </div>
                                    <span class="badge bg-success">{{ $donation->volume }}ml</span>
                                </div>
                            @empty
                                <p class="text-muted text-center">Aucun don enregistré</p>
                            @endforelse
                            
                            @if($recentDonations->count() > 0)
                                <div class="text-center mt-3">
                                    <a href="{{ route('donor.profile.medical-history') }}" class="btn btn-outline-primary btn-sm">
                                        Voir tout l'historique
                                    </a>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Prochains rendez-vous -->
                <div class="col-lg-6 mb-4">
                    <div class="card h-100">
                        <div class="card-header bg-light">
                            <h6 class="mb-0">
                                <i class="bi bi-calendar-event me-2"></i>Prochains Rendez-vous
                            </h6>
                        </div>
                        <div class="card-body">
                            @forelse($upcomingAppointments as $appointment)
                                <div class="d-flex align-items-center mb-3">
                                    <div class="bg-primary rounded-circle p-2 me-3">
                                        <i class="bi bi-calendar text-white"></i>
                                    </div>
                                    <div class="flex-grow-1">
                                        <h6 class="mb-1">
                                            {{ $appointment->campaign->title ?? 'Rendez-vous standard' }}
                                        </h6>
                                        <small class="text-muted">
                                            {{ $appointment->appointment_date->format('d/m/Y H:i') }}
                                        </small>
                                    </div>
                                    <span class="badge bg-info">{{ $appointment->status }}</span>
                                </div>
                            @empty
                                <p class="text-muted text-center">Aucun rendez-vous planifié</p>
                                <div class="text-center">
                                    <a href="" class="btn btn-primary btn-sm">
                                        Prendre rendez-vous
                                    </a>
                                </div>
                            @endforelse
                            
                            @if($upcomingAppointments->count() > 0)
                                <div class="text-center mt-3">
                                    <a href="{{ route('donor.appointments') }}" class="btn btn-outline-primary btn-sm">
                                        Voir tous les rendez-vous
                                    </a>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Actions rapides -->
            <div class="card">
                <div class="card-header bg-light">
                    <h6 class="mb-0">
                        <i class="bi bi-lightning me-2"></i>Actions Rapides
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4 mb-2">
                            <a href="{{ route('donor.profile.edit') }}" class="btn btn-outline-primary w-100">
                                <i class="bi bi-pencil-square me-1"></i>Modifier le profil
                            </a>
                        </div>
                        <div class="col-md-4 mb-2">
                            <a href="" class="btn btn-outline-success w-100">
                                <i class="bi bi-calendar-plus me-1"></i>Prendre RDV
                            </a>
                        </div>
                        <div class="col-md-4 mb-2">
                            <a href="" class="btn btn-outline-info w-100">
                                <i class="bi bi-download me-1"></i>Exporter données
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.profile-avatar img, .profile-avatar div {
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
}

.card:hover {
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    transition: box-shadow 0.3s ease;
}

.list-group-item-action.active {
    background-color: #0d6efd;
    border-color: #0d6efd;
}

.badge {
    font-size: 0.75em;
}
</style>
@endsection