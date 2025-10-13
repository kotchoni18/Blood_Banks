@extends('layouts.admin')

@section('title', 'Tableau de Bord')
@section('page-title', 'Tableau de Bord')
@section('page-subtitle', 'Vue d\'ensemble du système')

@section('content')
<!-- Statistics Cards -->
<div class="row mb-4">
    <div class="col-lg-3 col-md-6 mb-3">
        <div class="card stat-card">
            <div class="card-body text-center">
                <i class="bi bi-people display-4 mb-3"></i>
                <h3>{{ number_format($stats['total_users']) }}</h3>
                <p>Utilisateurs Total</p>
            </div>
        </div>
    </div>
    
    <div class="col-lg-3 col-md-6 mb-3">
        <div class="card stat-card">
            <div class="card-body text-center">
                <i class="bi bi-droplet display-4 mb-3"></i>
                 <h3 class="fw-bold mb-1">{{ number_format($stats['total_stock']) }}</h3>
                <p>Poches en Stock</p>
            </div>
        </div>
    </div>
    
    <div class="col-lg-3 col-md-6 mb-3">
        <div class="card stat-card">
            <div class="card-body text-center">
                <i class="bi bi-exclamation-triangle display-4 mb-3"></i>
                    <h3 class="fw-bold mb-1">{{ $stats['critical_stocks'] }}</h3>
                <p>Stocks Critiques</p>
            </div>
        </div>
    </div>
    
    <div class="col-lg-3 col-md-6 mb-3">
        <div class="card stat-card">
            <div class="card-body text-center">
                <i class="bi bi-activity display-4 mb-3"></i>
                <h3>{{ $stats['donations_today'] }}</h3>
                <p>Dons Aujourd'hui</p>
            </div>
        </div>
    </div>
</div>

<!-- Blood Groups Overview -->
<div class="card mb-4">
    <div class="card-header bg-primary text-white">
        <h5 class="mb-0">
            <i class="bi bi-droplet-fill me-2"></i>
            Stock par Groupe Sanguin
        </h5>
    </div>
    <div class="card-body">
        <div class="row">
            @php $bloodGroups = ['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-']; @endphp
            @foreach($bloodGroups as $group)
                @php 
                    $quantity = $stockData[$group] ?? 0;
                    $status = $quantity >= 200 ? 'Bon' : ($quantity >= 100 ? 'Faible' : 'Critique');
                @endphp
                <div class="col-lg-3 col-md-6 mb-3">
                    <div class="text-center">
                        <div class="rounded-circle bg-danger text-white d-flex align-items-center justify-content-center mx-auto mb-2" 
                             style="width: 80px; height: 80px; font-size: 1.2rem; font-weight: bold;">
                            {{ $group }}
                        </div>
                        <h6>{{ $quantity }} poches</h6>
                        <span class="badge bg-{{ $status === 'Bon' ? 'success' : ($status === 'Faible' ? 'warning' : 'danger') }}">
                            {{ $status }}
                        </span>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>

<!-- Charts -->
<div class="row">
    <div class="col-lg-8 mb-4">
        <div class="card">
            <div class="card-header bg-info text-white">
                <h6 class="mb-0">Évolution des Dons</h6>
            </div>
            <div class="card-body">
                <canvas id="donationsChart" height="100"></canvas>
            </div>
        </div>
    </div>
    
    <div class="col-lg-4 mb-4">
        <div class="card">
            <div class="card-header bg-success text-white">
                <h6 class="mb-0">Répartition par Groupe</h6>
            </div>
            <div class="card-body">
                <canvas id="groupChart"></canvas>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Chart des dons
    const donationsCtx = document.getElementById('donationsChart').getContext('2d');
    
    fetch('{{ route("admin.chart-data") }}?type=monthly')
        .then(response => response.json())
        .then(data => {
            new Chart(donationsCtx, {
                type: 'line',
                data: {
                    labels: ['Jan', 'Fév', 'Mar', 'Avr', 'Mai', 'Jun'],
                    datasets: [{
                        label: 'Nombre de dons',
                        data: Object.values(data),
                        borderColor: '#667eea',
                        backgroundColor: 'rgba(102, 126, 234, 0.1)',
                        tension: 0.4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false
                }
            });
        });

    // Chart groupes sanguins
    const groupCtx = document.getElementById('groupChart').getContext('2d');
    
    fetch('{{ route("admin.chart-data") }}?type=blood_groups')
        .then(response => response.json())
        .then(data => {
            new Chart(groupCtx, {
                type: 'doughnut',
                data: {
                    labels: Object.keys(data),
                    datasets: [{
                        data: Object.values(data),
                        backgroundColor: ['#ff6b6b', '#ee5a24', '#4834d4', '#686de0', '#130f40', '#30336b', '#eb4d4b', '#c0392b']
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false
                }
            });
        });
});
</script>
@endpush