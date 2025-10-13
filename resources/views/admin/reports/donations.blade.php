@extends('layouts.admin')

@section('page-title', 'Rapport - Dons')
@section('page-subtitle', 'Statistiques et analyses des dons')

@section('content')
<div class="container-fluid">

    <!-- Filtres -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('admin.reports.donations') }}">
                <div class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label">Date début</label>
                        <input type="date" name="start_date" class="form-control" value="{{ $startDate }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Date fin</label>
                        <input type="date" name="end_date" class="form-control" value="{{ $endDate }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Groupe sanguin</label>
                        <select name="blood_group" class="form-select">
                            <option value="all" {{ $bloodGroup=='all'?'selected':'' }}>Tous</option>
                            @foreach(['A+','A-','B+','B-','AB+','AB-','O+','O-'] as $group)
                                <option value="{{ $group }}" {{ $bloodGroup==$group?'selected':'' }}>{{ $group }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Statut</label>
                        <select name="status" class="form-select">
                            <option value="all" {{ $status=='all'?'selected':'' }}>Tous</option>
                            <option value="validated" {{ $status=='validated'?'selected':'' }}>Validés</option>
                            <option value="pending" {{ $status=='pending'?'selected':'' }}>En attente</option>
                        </select>
                    </div>
                    <div class="col-md-12 mt-3">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-funnel me-1"></i>Filtrer
                        </button>
                        <button type="button" class="btn btn-outline-secondary" onclick="window.print()">
                            <i class="bi bi-printer me-1"></i>Imprimer
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Statistiques globales -->
    <div class="row mb-4 g-4">
        <div class="col-md-3">
            <div class="card stat-card text-center">
                <h5>Total Dons</h5>
                <h3>{{ $stats['total_donations'] }}</h3>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card stat-card text-center">
                <h5>Total Quantité (ml)</h5>
                <h3>{{ $stats['total_quantity'] }}</h3>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card stat-card text-center">
                <h5>Dons validés</h5>
                <h3>{{ $stats['validated'] }}</h3>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card stat-card text-center">
                <h5>Dons en attente</h5>
                <h3>{{ $stats['pending'] }}</h3>
            </div>
        </div>
    </div>

    <!-- Graphiques -->
    <div class="row g-4 mb-4">
        <div class="col-lg-6">
            <div class="card p-3">
                <h6>Dons par mois (12 derniers mois)</h6>
                <canvas id="donationsByMonthChart"></canvas>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="card p-3">
                <h6>Dons par groupe sanguin</h6>
                <canvas id="donationsByBloodGroupChart"></canvas>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="card p-3">
                <h6>Dons par type</h6>
                <canvas id="donationsByTypeChart"></canvas>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="card p-3">
                <h6>Top 10 Agents</h6>
                <ul class="list-group list-group-flush">
                    @foreach($topAgents as $agent)
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        {{ $agent->full_name }}
                        <span class="badge bg-primary">{{ $agent->agent_donations_count }}</span>
                    </li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>

    <!-- Liste des dons -->
    <div class="card mb-4">
        <div class="card-header">
            <h6>Liste des dons ({{ $donations->total() }})</h6>
        </div>
        <div class="card-body table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Donneur</th>
                        <th>Agent</th>
                        <th>Groupe</th>
                        <th>Type</th>
                        <th>Quantité (ml)</th>
                        <th>Date</th>
                        <th>Statut</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($donations as $donation)
                    <tr>
                        <td>{{ $donation->donor->full_name ?? 'N/A' }}</td>
                        <td>{{ $donation->agent->full_name ?? 'N/A' }}</td>
                        <td>{{ $donation->blood_group }}</td>
                        <td>{{ $donation->donation_type }}</td>
                        <td>{{ $donation->quantity_ml }}</td>
                        <td>{{ $donation->donation_date->format('d/m/Y') }}</td>
                        <td>
                            @if($donation->status=='validated')
                                <span class="badge bg-success">Validé</span>
                            @else
                                <span class="badge bg-warning">En attente</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>

            <div class="mt-3">
                {{ $donations->links() }}
            </div>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
    // Dons par mois
    const ctxMonth = document.getElementById('donationsByMonthChart').getContext('2d');
    new Chart(ctxMonth, {
        type: 'line',
        data: {
            labels: {!! json_encode(collect($donationsByMonth)->pluck('month')) !!},
            datasets: [{
                label: 'Nombre de dons',
                data: {!! json_encode(collect($donationsByMonth)->pluck('count')) !!},
                borderColor: '#667eea',
                backgroundColor: 'rgba(102, 126, 234, 0.1)',
                tension: 0.4,
                fill: true
            },{
                label: 'Quantité (ml)',
                data: {!! json_encode(collect($donationsByMonth)->pluck('quantity')) !!},
                borderColor: '#e74c3c',
                backgroundColor: 'rgba(231, 76, 60, 0.1)',
                tension: 0.4,
                fill: true
            }]
        },
        options: { responsive: true, plugins: { legend: { position: 'top' } } }
    });

    // Dons par groupe sanguin
    const ctxGroup = document.getElementById('donationsByBloodGroupChart').getContext('2d');
    new Chart(ctxGroup, {
        type: 'doughnut',
        data: {
            labels: {!! json_encode(collect($donationsByBloodGroup)->pluck('blood_group')) !!},
            datasets: [{
                data: {!! json_encode(collect($donationsByBloodGroup)->pluck('total')) !!},
                backgroundColor: ['#e74c3c','#c0392b','#3498db','#2980b9','#f1c40f','#f39c12','#2ecc71','#27ae60']
            }]
        },
        options: { responsive: true }
    });

    // Dons par type
    const ctxType = document.getElementById('donationsByTypeChart').getContext('2d');
    new Chart(ctxType, {
        type: 'bar',
        data: {
            labels: {!! json_encode(array_keys($donationsByType)) !!},
            datasets: [{
                label: 'Nombre de dons',
                data: {!! json_encode(array_values($donationsByType)) !!},
                backgroundColor: '#3498db'
            }]
        },
        options: { responsive: true, plugins: { legend: { display: false } } }
    });
</script>
@endpush
