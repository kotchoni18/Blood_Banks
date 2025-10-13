@extends('layouts.donor')

@section('title', 'Mon Espace Donneur')

@section('content')
<!-- Hero Section -->
<section class="hero-section">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-8">
                <h1 class="display-4 fw-bold mb-4">
                    Bienvenue, {{ $user->first_name }} !
                </h1>
                <p class="lead">
                    Merci pour votre générosité. Votre groupe sanguin <strong>{{ $donor->blood_group ?? 'Non renseigné' }}</strong> 
                    peut aider à sauver des vies.
                </p>
            </div>
           <!--  <div class="col-lg-4 text-center">
                <div class="stat-card">
    
                    <div>Dons effectués</div>
                </div>
            </div>-->
        </div>
    </div>
</section>

<div class="container">
    <!-- Status Cards-->
    <div class="row mb-4">
        <div class="col-lg-4 mb-3">
            <div class="card eligibility-card {{ $canDonate ? '' : 'not-eligible' }}">
                <div class="card-body text-center">
                    <i class="bi bi-{{ $canDonate ? 'check-circle' : 'clock' }} display-4 mb-3 text-{{ $canDonate ? 'success' : 'warning' }}"></i>
                    <h5>Éligibilité au Don</h5>
                    @if($canDonate)
                        <p class="text-success fw-bold">Vous pouvez donner !</p>
                        <button class="btn btn-success">Prendre Rendez-vous</button>
                    @else
                        <p class="text-warning">Prochaine éligibilité :</p>
                        <p class="fw-bold">{{ $nextEligibleDate ? $nextEligibleDate->format('d/m/Y') : 'Non déterminé' }}</p>
                    @endif
                </div>
            </div>
        </div>
        
        <div class="col-lg-4 mb-3">
            <div class="card">
                <div class="card-body text-center">
                    <i class="bi bi-droplet display-4 mb-3 text-danger"></i>
                    <h5>Dernier Don</h5>
                    @if($lastDonation)
                        <p class="fw-bold">{{ $lastDonation->donation_date->format('d/m/Y') }}</p>
                        <small class="text-muted">{{ $lastDonation->donation_type }}</small>
                    @else
                        <p class="text-muted">Aucun don enregistré</p>
                    @endif
                </div>
            </div>
        </div>
        
        <div class="col-lg-4 mb-3">
            <div class="card">
                <div class="card-body text-center">
                    <i class="bi bi-calendar-event display-4 mb-3 text-info"></i>
                    <h5>Prochain Rendez-vous</h5>
                    @if($nextAppointment)
                        <p class="fw-bold">{{ $nextAppointment->appointment_date->format('d/m/Y H:i') }}</p>
                        <small class="text-muted">{{ $nextAppointment->campaign->title ?? 'Rendez-vous standard' }}</small>
                    @else
                        <p class="text-muted">Aucun rendez-vous planifié</p>
                        <button class="btn btn-outline-info btn-sm">Planifier</button>
                    @endif
                </div>
            </div>
        </div>
    </div> 

    <!-- Available Campaigns--> 
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">
                        <i class="bi bi-megaphone me-2"></i>
                        Campagnes de Don Disponibles
                    </h5>
                </div>
                <div class="card-body">
                    @forelse($availableCampaigns as $campaign)
                        <div class="campaign-card mb-3 p-3 border rounded">
                            <div class="row align-items-center">
                                <div class="col-md-8">
                                    <h6 class="fw-bold">{{ $campaign->title }}</h6>
                                    <p class="text-muted mb-2">{{ $campaign->description }}</p>
                                    <small class="text-info">
                                        <i class="bi bi-geo-alt me-1"></i>{{ $campaign->location }}
                                        <i class="bi bi-calendar ms-3 me-1"></i>{{ $campaign->start_date->format('d/m/Y') }} - {{ $campaign->end_date->format('d/m/Y') }}
                                    </small>
                                </div>
                                <div class="col-md-4 text-md-end">
                                    <div class="mb-2">
                                        <div class="progress mb-1">
                                            <div class="progress-bar" style="width:200px"></div>
                                        </div>
                                        <small class="text-muted">{{ $campaign->current_donations }}/{{ $campaign->target_donations }} dons</small>
                                    </div>
                                    @if($canDonate)
                                        <button class="btn btn-primary btn-sm">S'inscrire</button>
                                    @else
                                        <button class="btn btn-secondary btn-sm" disabled>Non éligible</button>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-4">
                            <i class="bi bi-calendar-x display-4 text-muted"></i>
                            <p class="text-muted mt-2">Aucune campagne disponible actuellement</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
@endsection