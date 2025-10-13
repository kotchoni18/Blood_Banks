@extends('layouts.admin')

@section('title', 'Rapport - Stocks de Sang')

@section('page-title', 'Rapport - Stocks de Sang')
@section('page-subtitle', 'Analyse des stocks et tendances')

@section('content')
<div class="container-fluid">

    <!-- Boutons d'actions -->
    <div class="mb-4 d-flex justify-content-end no-print">
        <button onclick="window.print()" class="btn btn-primary me-2">
            <i class="bi bi-printer me-1"></i>Imprimer / PDF
        </button>
    </div>

    <!-- Statistiques globales -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card stat-card text-center p-3">
                <h5>Total unités disponibles</h5>
                <h2>{{ $stats['total_units'] }}</h2>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card stat-card text-center p-3" style="background: #f39c12;">
                <h5>Expirant bientôt (7j)</h5>
                <h2>{{ $stats['expiring_soon'] }}</h2>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card stat-card text-center p-3" style="background: #e74c3c;">
                <h5>Unités expirées</h5>
                <h2>{{ $stats['expired'] }}</h2>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card stat-card text-center p-3" style="background: #2ecc71;">
                <h5>Unités utilisées ce mois</h5>
                <h2>{{ $stats['used_this_month'] }}</h2>
            </div>
        </div>
    </div>

    <!-- Stocks par groupe sanguin -->
    <div class="card mb-4 p-3">
        <h5>Stocks par groupe sanguin</h5>
        <canvas id="bloodGroupChart" height="80"></canvas>
    </div>

    <!-- Stocks expirant bientôt -->
    <div class="card mb-4 p-3">
        <h5>Stocks expirant bientôt</h5>
        <div class="table-responsive">
            <table class="table table-sm table-hover">
                <thead>
                    <tr>
                        <th>Groupe</th>
                        <th>Numéro du sac</th>
                        <th>Quantité</th>
                        <th>Date d'expiration</th>
                        <th>Jours restants</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($expiringStocks as $stock)
                    <tr>
                        <td>{{ $stock['blood_group'] }}</td>
                        <td>{{ $stock['bag_number'] }}</td>
                        <td>{{ $stock['quantity'] }}</td>
                        <td>{{ $stock['expiry_date'] }}</td>
                        <td>{{ $stock['days_remaining'] }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center">Aucun stock expirant bientôt</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Historique d'utilisation -->
    <div class="card mb-4 p-3">
        <h5>Historique d'utilisation (30 derniers jours)</h5>
        <canvas id="usageHistoryChart" height="80"></canvas>
    </div>

    <!-- Stocks par localisation -->
    <div class="card mb-4 p-3">
        <h5>Stocks par localisation</h5>
        <div class="table-responsive">
            <table class="table table-sm table-hover">
                <thead>
                    <tr>
                        <th>Localisation</th>
                        <th>Nombre de sacs</th>
                        <th>Quantité totale</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($stocksByLocation as $loc)
                    <tr>
                        <td>{{ $loc->location }}</td>
                        <td>{{ $loc->total }}</td>
                        <td>{{ $loc->quantity }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- Taux de rotation et projection -->
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card p-3 text-center">
                <h5>Taux de rotation (Turnover Rate)</h5>
                <h2>{{ $turnoverRate }}%</h2>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card p-3 text-center">
                <h5>Jours de stock disponibles</h5>
                <h2>{{ $daysOfStock }}</h2>
                <small>Basé sur l'utilisation moyenne de {{ round($averageUsagePerDay,1) }} unités/jour</small>
            </div>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
    // Stocks par groupe sanguin
    const bloodGroupCtx = document.getElementById('bloodGroupChart').getContext('2d');
    new Chart(bloodGroupCtx, {
        type: 'bar',
        data: {
            labels: {!! json_encode(array_column($stocksByBloodGroup, 'blood_group')) !!},
            datasets: [{
                label: 'Quantité disponible',
                data: {!! json_encode(array_column($stocksByBloodGroup, 'quantity')) !!},
                backgroundColor: {!! json_encode(array_map(function($s){
                    if($s['status'] === 'critical') return '#e74c3c';
                    if($s['status'] === 'low') return '#f39c12';
                    return '#2ecc71';
                }, $stocksByBloodGroup)) !!}
            }]
        },
        options: { responsive: true, plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true } } }
    });

    // Historique d'utilisation
    const usageCtx = document.getElementById('usageHistoryChart').getContext('2d');
    new Chart(usageCtx, {
        type: 'line',
        data: {
            labels: {!! json_encode(array_column($usageHistory, 'date')) !!},
            datasets: [{
                label: 'Unités utilisées',
                data: {!! json_encode(array_column($usageHistory, 'quantity')) !!},
                borderColor: '#3498db',
                backgroundColor: 'rgba(52,152,219,0.2)',
                fill: true,
                tension: 0.3
            }]
        },
        options: { responsive: true, plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true } } }
    });
</script>
@endpush

@push('styles')
<style>
    @media print {
        .no-print { display: none !important; }
        body { background: white; }
        table { font-size: 12px; }
        canvas { max-width: 100% !important; }
    }
</style>
@endpush
