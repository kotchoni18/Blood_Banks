@extends('layouts.agent')

@section('title', 'Tableau de bord')

@section('content')
<div class="container-fluid py-4">

    {{--  1. STATS EN HAUT --}}
    <div class="row mb-4">
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="stat-card p-4 text-center">
                <i class="bi bi-heart-fill stat-icon text-danger"></i>
                <div class="display-6 fw-bold text-danger">{{ $todayDonations }}</div>
                <div class="text-muted">Dons Aujourd'hui</div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="stat-card p-4 text-center">
                <i class="bi bi-calendar-week stat-icon text-success"></i>
                <div class="display-6 fw-bold text-success">{{ $weeklyDonations }}</div>
                <div class="text-muted">Dons cette Semaine</div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="stat-card p-4 text-center">
                <i class="bi bi-droplet-fill stat-icon text-info"></i>
                <div class="display-6 fw-bold text-info">{{ $totalStock }}</div>
                <div class="text-muted">Total Stock (Unités)</div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="stat-card p-4 text-center">
                <i class="bi bi-graph-up-arrow stat-icon text-warning"></i>
                <div class="display-6 fw-bold text-warning">{{ $totalDonations }}</div>
                <div class="text-muted">Total Dons</div>
            </div>
        </div>
    </div>

    {{--  2. SECTION PRINCIPALE --}}
    <div class="row">
        {{--  FORMULAIRE (GROS + BOUTON ENREGISTRER) --}}
        <div class="col-lg-8 mb-4">
            <div class="card shadow-sm">
                <div class="card-header bg-danger text-white">
                    <h5 class="mb-0"><i class="bi bi-clipboard-heart me-2"></i> Enregistrer un Don</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('agent.donations.store') }}" method="POST">
                        @csrf

                        {{-- Exemple de champs --}}
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Prénom du donneur *</label>
                                <input type="text" name="donor_first_name" class="form-control" required>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Nom du donneur *</label>
                                <input type="text" name="donor_last_name" class="form-control" required>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Groupe sanguin *</label>
                                <select name="blood_group" class="form-select" required>
                                    <option value="">Sélectionner</option>
                                    @foreach(['A+','A-','B+','B-','AB+','AB-','O+','O-'] as $group)
                                        <option value="{{ $group }}">{{ $group }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Quantité (ml) *</label>
                                <input type="number" name="quantity_ml" class="form-control" min="50" required>
                            </div>
                        </div>

                        {{--  BOUTON ENREGISTRER --}}
                        <div class="text-end">
                            <button type="submit" class="btn btn-primary px-4">
                                <i class="bi bi-check-circle me-2"></i> Enregistrer le don
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{--  DONNÉES RÉCENTES (ASIDE) --}}
        <div class="col-lg-4">
            <div class="card shadow-sm">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0"><i class="bi bi-clock-history me-2"></i> Dons récents</h5>
                </div>
                <div class="card-body">
                    @forelse($recentDonations as $don)
                        <div class="d-flex justify-content-between mb-3 border-bottom pb-2">
                            <div>
                                <strong>{{ $don->donor->full_name }}</strong><br>
                                <small class="text-muted">{{ $don->blood_group }} • {{ $don->quantity_ml }} ml</small>
                            </div>
                            <span class="badge bg-secondary">{{ $don->donation_date->format('d/m/Y') }}</span>
                        </div>
                    @empty
                        <p class="text-muted mb-0">Aucun don récent.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
