@extends('layouts.admin')

@section('page-title', 'Rapport - Utilisateurs')
@section('page-subtitle', 'Statistiques et analyses des utilisateurs')

@section('content')
    <!-- Filtres -->
    <div class="card border-0 shadow-sm mb-4 no-print">
        <div class="card-body">
            <form method="GET" action="{{ route('admin.reports.users') }}">
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
                        <label class="form-label">Rôle</label>
                        <select name="role" class="form-select">
                            <option value="all" {{ $role == 'all' ? 'selected' : '' }}>Tous</option>
                            <option value="donor" {{ $role == 'donor' ? 'selected' : '' }}>Donneurs</option>
                            <option value="agent" {{ $role == 'agent' ? 'selected' : '' }}>Agents</option>
                            <option value="admin" {{ $role == 'admin' ? 'selected' : '' }}>Admins</option>
                        </select>
                    </div>
                    <div class="col-md-3 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="bi bi-funnel me-1"></i>Filtrer
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Statistiques globales -->
    <div class="row g-4 mb-4">
        <div class="col-md-4">
            <div class="card stat-card text-center p-3">
                <i class="bi bi-people display-4 mb-2"></i>
                <h2 class="mb-1">{{ $stats['total_users'] ?? 0 }}</h2>
                <p class="mb-0">Utilisateurs Total</p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card stat-card text-center p-3">
                <i class="bi bi-heart-pulse display-4 mb-2"></i>
                <h2 class="mb-1">{{ $stats['total_donors'] ?? 0 }}</h2>
                <p class="mb-0">Donneurs Inscrits</p>
                <small class="text-light">{{ $stats['active_donors'] ?? 0 }} actifs</small>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card stat-card text-center p-3">
                <i class="bi bi-calendar-plus display-4 mb-2"></i>
                <h2 class="mb-1">{{ $stats['new_this_month'] ?? 0 }}</h2>
                <p class="mb-0">Nouveaux ce mois</p>
            </div>
        </div>
    </div>

    <!-- Graphiques -->
    <div class="row">
        <!-- Inscriptions par mois -->
        <div class="col-lg-8 mb-4">
            <div class="card p-3">
                <h5><i class="bi bi-graph-up text-primary me-2"></i>Évolution des inscriptions (12 derniers mois)</h5>
                <canvas id="registrationsChart" height="80"></canvas>
            </div>
        </div>

        <!-- Utilisateurs par rôle -->
        <div class="col-lg-4 mb-4">
            <div class="card p-3">
                <h5><i class="bi bi-pie-chart text-primary me-2"></i>Répartition par rôle</h5>
                <canvas id="rolesChart"></canvas>
                <div class="mt-3">
                    <div class="d-flex justify-content-between mb-2">
                        <span>Taux de conversion:</span>
                        <strong>{{ $conversionRate ?? 0 }}%</strong>
                    </div>
                    <div class="progress" style="height: 10px;">
                        <div class="progress-bar bg-success" style="width: {{ $conversionRate ?? 0 }}%"></div>
                    </div>
                    <small class="text-muted">Donneurs actifs / Total donneurs</small>
                </div>
            </div>
        </div>

        <!-- Donneurs par groupe sanguin -->
        <div class="col-lg-6 mb-4">
            <div class="card p-3">
                <h5><i class="bi bi-droplet text-danger me-2"></i>Donneurs par groupe sanguin</h5>
                <canvas id="bloodGroupChart"></canvas>
            </div>
        </div>

        <!-- Top 10 donneurs -->
        <div class="col-lg-6 mb-4">
            <div class="card p-3">
                <h5><i class="bi bi-trophy text-warning me-2"></i>Top 10 Donneurs les plus actifs</h5>
                <div class="table-responsive">
                    <table class="table table-sm mb-0">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Nom</th>
                                <th>Groupe</th>
                                <th class="text-center">Dons</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($topDonors as $index => $donor)
                                <tr>
                                    <td>
                                        @if($index < 3)
                                            <i class="bi bi-trophy-fill text-warning"></i>
                                        @else
                                            {{ $index + 1 }}
                                        @endif
                                    </td>
                                    <td>{{ $donor->first_name }} {{ $donor->last_name }}</td>
                                    <td><span class="badge bg-danger">{{ $donor->blood_group }}</span></td>
                                    <td class="text-center"><strong>{{ $donor->donation_count }}</strong></td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center">Aucun donneur trouvé</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Liste détaillée -->
    <div class="card mb-4">
        <div class="card-header">
            <h5>Liste des utilisateurs ({{ $users->total() ?? 0 }})</h5>
        </div>
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Nom</th>
                        <th>Email</th>
                        <th>Rôle</th>
                        <th>Inscrit le</th>
                        <th>Statut</th>
                        <th class="no-print">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $user)
                        <tr>
                            <td>
                                {{ $user->first_name }} {{ $user->last_name }}
                                @if($user->role === 'donor' && $user->blood_group)
                                    <br><small class="text-danger">{{ $user->blood_group }}</small>
                                @endif
                            </td>
                            <td>{{ $user->email }}</td>
                            <td>
                                @if($user->role === 'admin')
                                    <span class="badge bg-warning">Admin</span>
                                @elseif($user->role === 'agent')
                                    <span class="badge bg-info">Agent</span>
                                @else
                                    <span class="badge bg-danger">Donneur</span>
                                @endif
                            </td>
                            <td>{{ $user->created_at->format('d/m/Y') }}</td>
                            <td>
                                @if($user->is_active)
                                    <span class="badge bg-success">Actif</span>
                                @else
                                    <span class="badge bg-secondary">Inactif</span>
                                @endif
                            </td>
                            <td class="no-print">
                                <a href="" class="btn btn-sm btn-outline-primary">
                                    <i class="bi bi-eye"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center">Aucun utilisateur trouvé</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Pagination -->
    <div class="mt-4">
        {{ $users->links() }}
    </div>
@endsection

@push('scripts')
<script>
    // Graphique des inscriptions
    const registrationsCtx = document.getElementById('registrationsChart')?.getContext('2d');
    if (registrationsCtx) {
        new Chart(registrationsCtx, {
            type: 'line',
            data: {
                labels: @json(collect($registrationsByMonth)->pluck('month')),
                datasets: [{
                    label: 'Inscriptions',
                    data: @json(collect($registrationsByMonth)->pluck('count')),
                    borderColor: '#667eea',
                    backgroundColor: 'rgba(102,126,234,0.1)',
                    tension: 0.4,
                    fill: true
                }]
            },
            options: { responsive: true, plugins: { legend: { display: false } } }
        });
    }

    // Graphique par rôle
    const rolesCtx = document.getElementById('rolesChart')?.getContext('2d');
    if (rolesCtx) {
        new Chart(rolesCtx, {
            type: 'doughnut',
            data: {
                labels: ['Donneurs', 'Agents', 'Admins'],
                datasets: [{
                    data: [{{ $usersByRole['donors'] ?? 0 }}, {{ $usersByRole['agents'] ?? 0 }}, {{ $usersByRole['admins'] ?? 0 }}],
                    backgroundColor: ['#e74c3c','#3498db','#f39c12']
                }]
            },
            options: { responsive: true }
        });
    }

    // Graphique par groupe sanguin
    const bloodGroupCtx = document.getElementById('bloodGroupChart')?.getContext('2d');
    if (bloodGroupCtx) {
        new Chart(bloodGroupCtx, {
            type: 'bar',
            data: {
                labels: @json(array_keys($donorsByBloodGroup ?? [])),
                datasets: [{
                    label: 'Donneurs',
                    data: @json(array_values($donorsByBloodGroup ?? [])),
                    backgroundColor: '#e74c3c'
                }]
            },
            options: { responsive: true, plugins: { legend: { display: false } } }
        });
    }
</script>
@endpush
