<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des Stocks de Sang</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.10.0/font/bootstrap-icons.min.css" rel="stylesheet">
    <style>
        body {
            background: #f8f9fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .page-header {
            background: linear-gradient(135deg, #e74c3c 0%, #c0392b 100%);
            color: white;
            padding: 2rem 0;
            margin-bottom: 2rem;
        }
        .blood-card {
            background: white;
            border-radius: 15px;
            padding: 2rem;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            text-align: center;
            transition: transform 0.3s, box-shadow 0.3s;
            height: 100%;
        }
        .blood-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
        }
        .blood-icon {
            font-size: 4rem;
            margin-bottom: 1rem;
        }
        .blood-type {
            font-size: 2.5rem;
            font-weight: bold;
            margin-bottom: 0.5rem;
        }
        .stock-level {
            font-size: 3rem;
            font-weight: bold;
            margin: 1rem 0;
        }
        .stock-optimal {
            color: #27ae60;
        }
        .stock-low {
            color: #f39c12;
        }
        .stock-critical {
            color: #e74c3c;
        }
        .progress-custom {
            height: 25px;
            border-radius: 12px;
        }
        .alert-card {
            border-left: 5px solid;
            margin-bottom: 1rem;
        }
        .alert-card.critical {
            border-left-color: #e74c3c;
            background: #fee;
        }
        .alert-card.warning {
            border-left-color: #f39c12;
            background: #fff3cd;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="page-header">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <h2>
                        <i class="bi bi-droplet-fill me-2"></i>
                        Gestion des Stocks de Sang
                    </h2>
                    <p class="mb-0">Vue d'ensemble des disponibilités par groupe sanguin</p>
                </div>
                <div class="col-md-6 text-md-end">
                    <a href="{{ route('admin.dashboard') }}" class="btn btn-light me-2">
                        <i class="bi bi-arrow-left me-2"></i>
                        Retour
                    </a>
                    <a href="{{ route('admin.stocks.critical') }}" class="btn btn-danger">
                        <i class="bi bi-exclamation-triangle me-2"></i>
                        Stocks critiques
                    </a>
                    <a href="{{ route('admin.stocks.expiring') }}" class="btn btn-warning">
                        <i class="bi bi-clock me-2"></i>
                        Expirations proches
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="container pb-5">
        <!-- Messages -->
        @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            <i class="bi bi-check-circle-fill me-2"></i>
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        @endif

        @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show">
            <i class="bi bi-exclamation-triangle-fill me-2"></i>
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        @endif

        <!-- Alertes rapides -->
        <div class="row mb-4">
            @php
                $criticalCount = collect($stocks)->filter(fn($s) => $s['status'] === 'critical')->count();
                $lowCount = collect($stocks)->filter(fn($s) => $s['status'] === 'low')->count();
            @endphp

            @if($criticalCount > 0)
            <div class="col-md-6">
                <div class="alert-card critical p-3">
                    <h5 class="mb-1">
                        <i class="bi bi-exclamation-triangle-fill text-danger me-2"></i>
                        {{ $criticalCount }} groupe(s) en stock critique
                    </h5>
                    <p class="mb-0 text-muted small">Action urgente requise</p>
                </div>
            </div>
            @endif

            @if($lowCount > 0)
            <div class="col-md-6">
                <div class="alert-card warning p-3">
                    <h5 class="mb-1">
                        <i class="bi bi-exclamation-circle-fill text-warning me-2"></i>
                        {{ $lowCount }} groupe(s) en stock faible
                    </h5>
                    <p class="mb-0 text-muted small">Réapprovisionnement recommandé</p>
                </div>
            </div>
            @endif
        </div>

        <!-- Stocks par groupe sanguin -->
        <div class="row g-4 mb-4">
            @foreach($stocks as $stock)
            <div class="col-md-6 col-lg-3">
                <div class="blood-card">
                    <!-- Icône -->
                    <div class="blood-icon 
                        @if($stock['status'] === 'optimal') text-success
                        @elseif($stock['status'] === 'low') text-warning
                        @else text-danger
                        @endif">
                        <i class="bi bi-droplet-fill"></i>
                    </div>

                    <!-- Groupe sanguin -->
                    <div class="blood-type">{{ $stock['blood_group'] }}</div>

                    <!-- Quantité -->
                    <div class="stock-level 
                        @if($stock['status'] === 'optimal') stock-optimal
                        @elseif($stock['status'] === 'low') stock-low
                        @else stock-critical
                        @endif">
                        {{ $stock['quantity'] }} <small style="font-size: 1.5rem;">unités</small>
                    </div>

                    <!-- Barre de progression -->
                    <div class="progress progress-custom mb-3">
                        @php
                            $percentage = ($stock['optimal_level'] > 0) ? min(100, ($stock['quantity'] / $stock['optimal_level']) * 100) : 0;
                        @endphp

                        <div class="progress-bar 
                            @if($stock['status'] === 'optimal') bg-success
                            @elseif($stock['status'] === 'low') bg-warning
                            @else bg-danger
                            @endif" 
                            style="width: {{ $percentage }}%">
                            {{ round($percentage) }}%
                        </div>
                    </div>

                    <!-- Informations -->
                    <div class="text-muted small mb-2">
                        <div class="d-flex justify-content-between mb-1">
                            <span>Niveau optimal :</span>
                            <strong>{{ $stock['optimal_level'] }} unités</strong>
                        </div>
                        <div class="d-flex justify-content-between mb-1">
                            <span>Niveau critique :</span>
                            <strong>{{ $stock['critical_level'] }} unités</strong>
                        </div>
                        @if(isset($stock['expiring_soon']))
                        <div class="d-flex justify-content-between text-warning">
                            <span>Expire bientôt :</span>
                            <strong>{{ $stock['expiring_soon'] }} unités</strong>
                        </div>
                        @endif
                    </div>

                    <!-- Badge de statut -->
                    @if($stock['status'] === 'optimal')
                        <span class="badge bg-success">Stock optimal</span>
                    @elseif($stock['status'] === 'low')
                        <span class="badge bg-warning">Stock faible</span>
                    @else
                        <span class="badge bg-danger">Stock critique</span>
                    @endif

                    <!-- Actions -->
                    <div class="mt-3">
                        <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#detailModal{{ $stock['blood_group'] }}">
                            <i class="bi bi-eye me-1"></i>
                            Détails
                        </button>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        <!-- Statistiques globales -->
        <div class="row">
            <div class="col-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white">
                        <h5 class="mb-0">
                            <i class="bi bi-bar-chart-fill text-primary me-2"></i>
                            Statistiques globales
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row text-center">
                            <div class="col-md-3">
                                <div class="p-3">
                                    <div class="display-4 text-primary mb-2">
                                        {{ collect($stocks)->sum('quantity') }}
                                    </div>
                                    <p class="text-muted mb-0">Unités totales</p>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="p-3">
                                    <div class="display-4 text-success mb-2">
                                        {{ collect($stocks)->filter(fn($s) => $s['status'] === 'optimal')->count() }}
                                    </div>
                                    <p class="text-muted mb-0">Stocks optimaux</p>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="p-3">
                                    <div class="display-4 text-warning mb-2">
                                        {{ collect($stocks)->filter(fn($s) => $s['status'] === 'low')->count() }}
                                    </div>
                                    <p class="text-muted mb-0">Stocks faibles</p>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="p-3">
                                    <div class="display-4 text-danger mb-2">
                                        {{ collect($stocks)->filter(fn($s) => $s['status'] === 'critical')->count() }}
                                    </div>
                                    <p class="text-muted mb-0">Stocks critiques</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modals de détails (un par groupe sanguin) -->
    @foreach($stocks as $stock)
    <div class="modal fade" id="detailModal{{ $stock['blood_group'] }}" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="bi bi-droplet-fill text-danger me-2"></i>
                        Détails - Groupe {{ $stock['blood_group'] }}
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <table class="table">
                        <tr>
                            <th>Quantité actuelle</th>
                            <td><strong>{{ $stock['quantity'] }} unités</strong></td>
                        </tr>
                        <tr>
                            <th>Niveau optimal</th>
                            <td>{{ $stock['optimal_level'] }} unités</td>
                        </tr>
                        <tr>
                            <th>Niveau critique</th>
                            <td>{{ $stock['critical_level'] }} unités</td>
                        </tr>
                        <tr>
                            <th>Statut</th>
                            <td>
                                @if($stock['status'] === 'optimal')
                                    <span class="badge bg-success">Optimal</span>
                                @elseif($stock['status'] === 'low')
                                    <span class="badge bg-warning">Faible</span>
                                @else
                                    <span class="badge bg-danger">Critique</span>
                                @endif
                            </td>
                        </tr>
                        @if(isset($stock['last_collection']))
                        <tr>
                            <th>Dernière collecte</th>
                            <td>{{ $stock['last_collection'] }}</td>
                        </tr>
                        @endif
                    </table>
                </div>
            </div>
        </div>
    </div>
    @endforeach

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
</body>
</html>