<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Stocks Critiques</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.10.0/font/bootstrap-icons.min.css" rel="stylesheet">
    <style>
        body {
            background: #f8f9fa;
        }
        .page-header {
            background: linear-gradient(135deg, #e74c3c 0%, #c0392b 100%);
            color: white;
            padding: 2rem 0;
            margin-bottom: 2rem;
        }
        .critical-alert {
            background: #fee;
            border-left: 5px solid #e74c3c;
            padding: 1.5rem;
            margin-bottom: 2rem;
            border-radius: 8px;
        }
        .stock-item {
            background: white;
            border-radius: 15px;
            padding: 1.5rem;
            margin-bottom: 1rem;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            border-left: 5px solid #e74c3c;
            transition: transform 0.3s;
        }
        .stock-item:hover {
            transform: translateX(5px);
        }
        .blood-badge {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 60px;
            height: 60px;
            background: linear-gradient(135deg, #e74c3c 0%, #c0392b 100%);
            color: white;
            border-radius: 50%;
            font-size: 1.5rem;
            font-weight: bold;
        }
        .urgency-high {
            animation: pulse 2s infinite;
        }
        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.5; }
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="page-header">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h2>
                        <i class="bi bi-exclamation-triangle-fill me-2"></i>
                        Stocks Critiques
                    </h2>
                    <p class="mb-0">Groupes sanguins nécessitant une action urgente</p>
                </div>
                <div class="col-md-4 text-md-end">
                    <a href="{{ route('admin.stocks.index') }}" class="btn btn-light">
                        <i class="bi bi-arrow-left me-2"></i>
                        Retour aux stocks
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="container pb-5">
        @if(count($stocks) > 0)
            <!-- Alerte globale -->
            <div class="critical-alert">
                <div class="d-flex align-items-center">
                    <div class="me-3">
                        <i class="bi bi-exclamation-triangle-fill display-3 text-danger"></i>
                    </div>
                    <div class="flex-grow-1">
                        <h4 class="mb-2 text-danger">
                            <strong>{{ count($stocks) }}</strong> groupe(s) sanguin(s) en niveau critique
                        </h4>
                        <p class="mb-0">
                            Ces stocks nécessitent un réapprovisionnement urgent. Contactez immédiatement les centres de collecte et lancez des campagnes de don.
                        </p>
                    </div>
                </div>
            </div>

            <!-- Liste des stocks critiques -->
            <div class="row">
                @foreach($stocks as $stock)
                <div class="col-lg-6">
                    <div class="stock-item {{ $stock['quantity'] == 0 ? 'urgency-high' : '' }}">
                        <div class="d-flex align-items-center">
                            <!-- Badge du groupe -->
                            <div class="blood-badge me-3">
                                {{ $stock['blood_group'] }}
                            </div>

                            <!-- Informations -->
                            <div class="flex-grow-1">
                                <h5 class="mb-1">
                                    Groupe {{ $stock['blood_group'] }}
                                    @if($stock['quantity'] == 0)
                                        <span class="badge bg-danger ms-2">STOCK VIDE</span>
                                    @else
                                        <span class="badge bg-warning ms-2">CRITIQUE</span>
                                    @endif
                                </h5>
                                
                                <div class="mb-2">
                                    <span class="text-muted">Quantité actuelle:</span>
                                    <strong class="text-danger fs-5 ms-2">{{ $stock['quantity'] }} unités</strong>
                                </div>

                                <div class="progress mb-2" style="height: 20px;">
                                    @php
                                        $percentage = ($stock['quantity'] / $stock['optimal_level']) * 100;
                                    @endphp
                                    <div class="progress-bar bg-danger" style="width: {{ $percentage }}%">
                                        {{ round($percentage) }}%
                                    </div>
                                </div>

                                <div class="row text-muted small">
                                    <div class="col-6">
                                        <i class="bi bi-check-circle me-1"></i>
                                        Optimal: {{ $stock['optimal_level'] }} unités
                                    </div>
                                    <div class="col-6">
                                        <i class="bi bi-exclamation-circle me-1"></i>
                                        Critique: {{ $stock['critical_level'] }} unités
                                    </div>
                                </div>

                                @if(isset($stock['deficit']))
                                <div class="mt-2">
                                    <span class="badge bg-danger">
                                        <i class="bi bi-arrow-down me-1"></i>
                                        Déficit: {{ $stock['deficit'] }} unités
                                    </span>
                                </div>
                                @endif
                            </div>

                            <!-- Actions -->
                            <div class="text-end">
                                <button class="btn btn-danger btn-sm mb-2" data-bs-toggle="modal" data-bs-target="#actionModal{{ $stock['blood_group'] }}">
                                    <i class="bi bi-megaphone me-1"></i>
                                    Lancer campagne
                                </button>
                                <br>
                                <button class="btn btn-outline-primary btn-sm">
                                    <i class="bi bi-telephone me-1"></i>
                                    Contacter centres
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Modal d'action -->
                <div class="modal fade" id="actionModal{{ $stock['blood_group'] }}" tabindex="-1">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header bg-danger text-white">
                                <h5 class="modal-title">
                                    <i class="bi bi-megaphone me-2"></i>
                                    Campagne urgente - Groupe {{ $stock['blood_group'] }}
                                </h5>
                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body">
                                <div class="alert alert-danger">
                                    <strong>Situation critique!</strong> 
                                    Le stock du groupe {{ $stock['blood_group'] }} est à {{ $stock['quantity'] }} unités.
                                </div>

                                <h6>Actions recommandées:</h6>
                                <ul>
                                    <li>Contacter les donneurs de groupe {{ $stock['blood_group'] }}</li>
                                    <li>Lancer une campagne SMS/Email</li>
                                    <li>Organiser une collecte d'urgence</li>
                                    <li>Alerter les centres de collecte partenaires</li>
                                </ul>

                                <form method="POST" action="#">
                                    @csrf
                                    <div class="mb-3">
                                        <label class="form-label">Message de la campagne</label>
                                        <textarea class="form-control" rows="4" required>URGENT: Nous avons un besoin critique de sang groupe {{ $stock['blood_group'] }}. Votre don peut sauver des vies. Prenez rendez-vous dès maintenant.</textarea>
                                    </div>

                                    <div class="form-check mb-3">
                                        <input class="form-check-input" type="checkbox" id="sendSMS{{ $stock['blood_group'] }}" checked>
                                        <label class="form-check-label" for="sendSMS{{ $stock['blood_group'] }}">
                                            Envoyer par SMS
                                        </label>
                                    </div>

                                    <div class="form-check mb-3">
                                        <input class="form-check-input" type="checkbox" id="sendEmail{{ $stock['blood_group'] }}" checked>
                                        <label class="form-check-label" for="sendEmail{{ $stock['blood_group'] }}">
                                            Envoyer par Email
                                        </label>
                                    </div>

                                    <button type="submit" class="btn btn-danger w-100">
                                        <i class="bi bi-send me-2"></i>
                                        Lancer la campagne
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>

            <!-- Statistiques -->
            <div class="row mt-4">
                <div class="col-12">
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-danger text-white">
                            <h5 class="mb-0">
                                <i class="bi bi-bar-chart-line me-2"></i>
                                Analyse de la situation
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row text-center">
                                <div class="col-md-4">
                                    <div class="p-3">
                                        <div class="display-4 text-danger mb-2">
                                            {{ collect($stocks)->where('quantity', 0)->count() }}
                                        </div>
                                        <p class="text-muted mb-0">Stocks vides</p>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="p-3">
                                        <div class="display-4 text-warning mb-2">
                                            {{ collect($stocks)->sum('quantity') }}
                                        </div>
                                        <p class="text-muted mb-0">Unités totales critiques</p>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="p-3">
                                        <div class="display-4 text-info mb-2">
                                            {{ collect($stocks)->sum('deficit') ?? 'N/A' }}
                                        </div>
                                        <p class="text-muted mb-0">Déficit total estimé</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        @else
            <!-- Aucun stock critique -->
            <div class="text-center py-5">
                <i class="bi bi-check-circle display-1 text-success mb-4"></i>
                <h3 class="text-success">Excellente nouvelle !</h3>
                <p class="text-muted">Aucun groupe sanguin n'est en stock critique actuellement.</p>
                <a href="{{ route('admin.stocks.index') }}" class="btn btn-primary mt-3">
                    Voir tous les stocks
                </a>
            </div>
        @endif
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
</body>
</html>
