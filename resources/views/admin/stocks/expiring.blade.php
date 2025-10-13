<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Stocks Expirant Bientôt</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.10.0/font/bootstrap-icons.min.css" rel="stylesheet">
    <style>
        body {
            background: #f8f9fa;
        }
        .page-header {
            background: linear-gradient(135deg, #f39c12 0%, #e67e22 100%);
            color: white;
            padding: 2rem 0;
            margin-bottom: 2rem;
        }
        .expiring-card {
            background: white;
            border-radius: 15px;
            padding: 1.5rem;
            margin-bottom: 1rem;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            border-left: 5px solid #f39c12;
            transition: all 0.3s;
        }
        .expiring-card:hover {
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
            transform: translateY(-2px);
        }
        .expiring-urgent {
            border-left-color: #e74c3c;
            background: #fff5f5;
        }
        .expiring-soon {
            border-left-color: #f39c12;
        }
        .days-badge {
            font-size: 2rem;
            font-weight: bold;
            padding: 1rem;
            border-radius: 10px;
            min-width: 80px;
            text-align: center;
        }
        .days-urgent {
            background: #fee;
            color: #e74c3c;
        }
        .days-warning {
            background: #fff3cd;
            color: #f39c12;
        }
        .timeline {
            position: relative;
            padding-left: 30px;
        }
        .timeline::before {
            content: '';
            position: absolute;
            left: 8px;
            top: 0;
            bottom: 0;
            width: 2px;
            background: #dee2e6;
        }
        .timeline-item {
            position: relative;
            padding-bottom: 1rem;
        }
        .timeline-item::before {
            content: '';
            position: absolute;
            left: -25px;
            top: 5px;
            width: 12px;
            height: 12px;
            border-radius: 50%;
            background: #f39c12;
            border: 3px solid white;
            box-shadow: 0 0 0 2px #f39c12;
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
                        <i class="bi bi-clock-history me-2"></i>
                        Stocks Expirant Bientôt
                    </h2>
                    <p class="mb-0">Poches de sang arrivant à expiration dans les prochains jours</p>
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
            <!-- Alerte d'information -->
            <div class="alert alert-warning alert-dismissible fade show">
                <div class="d-flex align-items-center">
                    <i class="bi bi-exclamation-triangle-fill display-4 me-3"></i>
                    <div>
                        <h5 class="alert-heading mb-2">
                            <strong>{{ collect($stocks)->sum('quantity_units') }}</strong> unité(s) de sang expire(nt) dans les 7 prochains jours
                        </h5>
                        <p class="mb-0">
                            Planifiez l'utilisation ou le retrait de ces poches avant expiration pour éviter le gaspillage.
                        </p>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>

            <!-- Filtres -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <h6 class="mb-2">Filtrer par urgence :</h6>
                            <div class="btn-group" role="group">
                                <input type="radio" class="btn-check" name="filter" id="filterAll" checked>
                                <label class="btn btn-outline-secondary" for="filterAll">
                                    Tous ({{ count($stocks) }})
                                </label>

                                <input type="radio" class="btn-check" name="filter" id="filterUrgent">
                                <label class="btn btn-outline-danger" for="filterUrgent">
                                    Urgent ≤ 3 jours
                                </label>

                                <input type="radio" class="btn-check" name="filter" id="filterSoon">
                                <label class="btn btn-outline-warning" for="filterSoon">
                                    Bientôt 4-7 jours
                                </label>
                            </div>
                        </div>
                        <div class="col-md-4 text-md-end">
                            <button class="btn btn-primary" onclick="window.print()">
                                <i class="bi bi-printer me-2"></i>
                                Imprimer le rapport
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Liste des stocks expirants -->
            @foreach($stocks as $stock)
            <div class="expiring-card {{ $stock['days_until_expiry'] <= 3 ? 'expiring-urgent' : 'expiring-soon' }}" 
                 data-days="{{ $stock['days_until_expiry'] }}">
                <div class="row align-items-center">
                    <!-- Badge de jours restants -->
                    <div class="col-auto">
                        <div class="days-badge {{ $stock['days_until_expiry'] <= 3 ? 'days-urgent' : 'days-warning' }}">
                            <div>{{ $stock['days_until_expiry'] }}</div>
                            <small style="font-size: 0.9rem;">jour(s)</small>
                        </div>
                    </div>

                    <!-- Informations -->
                    <div class="col">
                        <div class="d-flex align-items-start justify-content-between mb-2">
                            <div>
                                <h5 class="mb-1">
                                    <span class="badge bg-danger me-2">{{ $stock['blood_group'] }}</span>
                                    {{ $stock['quantity_units'] }} unité(s) de sang
                                    @if($stock['days_until_expiry'] <= 2)
                                        <span class="badge bg-danger ms-2">URGENT</span>
                                    @endif
                                </h5>
                                <p class="text-muted mb-0">
                                    <i class="bi bi-calendar-x me-1"></i>
                                    <strong>Expire le :</strong> {{ $stock['expiry_date'] }}
                                    ({{ $stock['expiry_date_human'] ?? 'bientôt' }})
                                </p>
                            </div>
                        </div>

                        <!-- Détails supplémentaires -->
                        <div class="row text-muted small mt-2">
                            @if(isset($stock['bag_number']))
                            <div class="col-md-4">
                                <i class="bi bi-hash me-1"></i>
                                <strong>N° Poche:</strong> {{ $stock['bag_number'] }}
                            </div>
                            @endif
                            @if(isset($stock['collection_date']))
                            <div class="col-md-4">
                                <i class="bi bi-calendar-check me-1"></i>
                                <strong>Collecté le:</strong> {{ $stock['collection_date'] }}
                            </div>
                            @endif
                            @if(isset($stock['location']))
                            <div class="col-md-4">
                                <i class="bi bi-geo-alt me-1"></i>
                                <strong>Emplacement:</strong> {{ $stock['location'] }}
                            </div>
                            @endif
                        </div>

                        <!-- Barre de progression temporelle -->
                        <div class="mt-3">
                            @php
                                $totalDays = 42; // Durée de vie du sang (42 jours)
                                $remaining = ($stock['days_until_expiry'] / $totalDays) * 100;
                            @endphp
                            <div class="progress" style="height: 8px;">
                                <div class="progress-bar {{ $stock['days_until_expiry'] <= 3 ? 'bg-danger' : 'bg-warning' }}" 
                                     style="width: {{ $remaining }}%"></div>
                            </div>
                            <small class="text-muted">
                                {{ round($remaining) }}% de durée de vie restante
                            </small>
                        </div>

                        <!-- Actions -->
                        <div class="mt-3">
                            <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#actionModal{{ $stock['id'] ?? $loop->index }}">
                                <i class="bi bi-check-circle me-1"></i>
                                Marquer comme utilisé
                            </button>
                            <button class="btn btn-sm btn-outline-danger" data-bs-toggle="modal" data-bs-target="#removeModal{{ $stock['id'] ?? $loop->index }}">
                                <i class="bi bi-trash me-1"></i>
                                Retirer du stock
                            </button>
                            <button class="btn btn-sm btn-outline-secondary">
                                <i class="bi bi-printer me-1"></i>
                                Étiquette
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Modal pour marquer comme utilisé -->
            <div class="modal fade" id="actionModal{{ $stock['id'] ?? $loop->index }}" tabindex="-1">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Marquer comme utilisé</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <form>
                                <div class="mb-3">
                                    <label class="form-label">Patient / Hôpital</label>
                                    <input type="text" class="form-control" placeholder="Nom du patient ou établissement">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Date d'utilisation</label>
                                    <input type="date" class="form-control" value="{{ date('Y-m-d') }}">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Notes</label>
                                    <textarea class="form-control" rows="2"></textarea>
                                </div>
                                <button type="submit" class="btn btn-primary w-100">
                                    Confirmer l'utilisation
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Modal pour retirer du stock -->
            <div class="modal fade" id="removeModal{{ $stock['id'] ?? $loop->index }}" tabindex="-1">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header bg-danger text-white">
                            <h5 class="modal-title">Retirer du stock</h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <div class="alert alert-warning">
                                <i class="bi bi-exclamation-triangle me-2"></i>
                                Vous êtes sur le point de retirer cette poche du stock.
                            </div>
                            <form>
                                <div class="mb-3">
                                    <label class="form-label">Raison du retrait *</label>
                                    <select class="form-select" required>
                                        <option value="">Sélectionner...</option>
                                        <option value="expired">Expirée</option>
                                        <option value="damaged">Endommagée</option>
                                        <option value="contaminated">Contaminée</option>
                                        <option value="other">Autre</option>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Commentaires</label>
                                    <textarea class="form-control" rows="2"></textarea>
                                </div>
                                <button type="submit" class="btn btn-danger w-100">
                                    <i class="bi bi-trash me-2"></i>
                                    Confirmer le retrait
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach

            <!-- Timeline de planification -->
            <div class="row mt-4">
                <div class="col-12">
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-warning text-white">
                            <h5 class="mb-0">
                                <i class="bi bi-calendar-week me-2"></i>
                                Planification des 7 prochains jours
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="timeline">
                                @for($day = 0; $day <= 7; $day++)
                                    @php
                                        $date = now()->addDays($day);
                                        $expiringToday = collect($stocks)->filter(fn($s) => $s['days_until_expiry'] == $day);
                                    @endphp
                                    @if($expiringToday->count() > 0)
                                    <div class="timeline-item">
                                        <strong>{{ $date->isSameDay(now()) ? "Aujourd'hui" : $date->format('d/m/Y') }}</strong>
                                        <span class="text-muted">(J+{{ $day }})</span>
                                        <div class="ms-3 mt-1">
                                            <span class="badge bg-{{ $day <= 3 ? 'danger' : 'warning' }}">
                                                {{ $expiringToday->sum('quantity_units') }} unité(s)
                                            </span>
                                            @foreach($expiringToday->groupBy('blood_group') as $group => $items)
                                                <span class="badge bg-secondary ms-1">
                                                    {{ $group }}: {{ $items->sum('quantity_units') }}
                                                </span>
                                            @endforeach
                                        </div>
                                    </div>
                                    @endif
                                @endfor
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Statistiques -->
            <div class="row mt-4">
                <div class="col-md-4">
                    <div class="card border-0 shadow-sm text-center">
                        <div class="card-body">
                            <i class="bi bi-exclamation-triangle-fill display-4 text-danger mb-3"></i>
                            <h3>{{ collect($stocks)->filter(fn($s) => $s['days_until_expiry'] <= 3)->sum('quantity_units') }}</h3>
                            <p class="text-muted mb-0">Unités urgentes (≤3j)</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card border-0 shadow-sm text-center">
                        <div class="card-body">
                            <i class="bi bi-clock display-4 text-warning mb-3"></i>
                            <h3>{{ collect($stocks)->filter(fn($s) => $s['days_until_expiry'] > 3)->sum('quantity_units') }}</h3>
                            <p class="text-muted mb-0">Unités à surveiller (4-7j)</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card border-0 shadow-sm text-center">
                        <div class="card-body">
                            <i class="bi bi-droplet-fill display-4 text-primary mb-3"></i>
                            <h3>{{ collect($stocks)->sum('quantity_units') }}</h3>
                            <p class="text-muted mb-0">Total expirant</p>
                        </div>
                    </div>
                </div>
            </div>

        @else
            <!-- Aucun stock expirant -->
            <div class="text-center py-5">
                <i class="bi bi-check-circle display-1 text-success mb-4"></i>
                <h3 class="text-success">Excellente gestion !</h3>
                <p class="text-muted">Aucune poche de sang n'expire dans les 7 prochains jours.</p>
                <a href="{{ route('admin.stocks.index') }}" class="btn btn-primary mt-3">
                    Voir tous les stocks
                </a>
            </div>
        @endif
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    <script>
        // Filtrage par urgence
        document.querySelectorAll('input[name="filter"]').forEach(radio => {
            radio.addEventListener('change', function() {
                const cards = document.querySelectorAll('.expiring-card');
                
                cards.forEach(card => {
                    const days = parseInt(card.dataset.days);
                    
                    if (this.id === 'filterAll') {
                        card.style.display = 'block';
                    } else if (this.id === 'filterUrgent') {
                        card.style.display = days <= 3 ? 'block' : 'none';
                    } else if (this.id === 'filterSoon') {
                        card.style.display = days > 3 ? 'block' : 'none';
                    }
                });
            });
        });
    </script>
</body>
</html>