<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Liste des Utilisateurs</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.10.0/font/bootstrap-icons.min.css" rel="stylesheet">
    <style>
        body {
            background: #f8f9fa;
        }
        .page-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 2rem 0;
            margin-bottom: 2rem;
        }
        .user-card {
            transition: transform 0.3s, box-shadow 0.3s;
        }
        .user-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
        }
        .role-badge {
            font-size: 0.85rem;
            padding: 0.4rem 0.8rem;
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
                        <i class="bi bi-people-fill me-2"></i>
                        Gestion des Utilisateurs
                    </h2>
                    <p class="mb-0">Liste complète de tous les utilisateurs du système</p>
                </div>
                
                <div class="col-md-4 text-md-end">
                    <a href="{{ route('admin.dashboard') }}" class="btn btn-light me-2">
                        <i class="bi bi-arrow-left me-2"></i>
                        Retour
                    </a>

                    <a href="{{ route('admin.users.create') }}" class="btn btn-light btn-lg">
                        <i class="bi bi-plus-circle me-2"></i>
                        Nouvel utilisateur
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

        <!-- Statistiques -->
        <div class="row g-4 mb-4">
            <div class="col-md-3">
                <div class="card border-0 shadow-sm text-center">
                    <div class="card-body">
                        <i class="bi bi-people display-4 text-primary mb-2"></i>
                        <h3>{{ $users->total() }}</h3>
                        <p class="text-muted mb-0">Total Utilisateurs</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-0 shadow-sm text-center">
                    <div class="card-body">
                        <i class="bi bi-heart-pulse display-4 text-danger mb-2"></i>
                        <h3>{{ $totalDonors }}</h3>
                        <p class="text-muted mb-0">Donneurs</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-0 shadow-sm text-center">
                    <div class="card-body">
                        <i class="bi bi-person-badge display-4 text-info mb-2"></i>
                        <h3>{{ $totalAgents }}</h3>
                        <p class="text-muted mb-0">Agents</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-0 shadow-sm text-center">
                    <div class="card-body">
                        <i class="bi bi-shield display-4 text-warning mb-2"></i>
                       <h3>{{ $totalAdmins }}</h3>
                        <p class="text-muted mb-0">Admins</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filtres et recherche --> 
<div class="card border-0 shadow-sm mb-4">
    <div class="card-body">
        <form method="GET" action="{{ route('admin.users.index') }}">
            <div class="row g-3">

                <div class="col-md-4">
                    <input type="text" 
                           name="search" 
                           class="form-control" 
                           placeholder="Rechercher par nom, email..."
                           value="{{ request('search') }}">
                </div>

                <div class="col-md-3">
                    <select name="role" class="form-select">
                        <option value="">Tous les rôles</option>
                        <option value="donor" {{ request('role') == 'donor' ? 'selected' : '' }}>Donneur</option>
                        <option value="agent" {{ request('role') == 'agent' ? 'selected' : '' }}>Agent</option>
                        <option value="admin" {{ request('role') == 'admin' ? 'selected' : '' }}>Admin</option>
                    </select>
                </div>

                <!--Nouveau filtre : Groupe sanguin -->
                <div class="col-md-3">
                    <select name="blood_group" class="form-select">
                        <option value="">Tous les groupes sanguins</option>
                        <option value="A+" {{ request('blood_group') == 'A+' ? 'selected' : '' }}>A+</option>
                        <option value="A-" {{ request('blood_group') == 'A-' ? 'selected' : '' }}>A-</option>
                        <option value="B+" {{ request('blood_group') == 'B+' ? 'selected' : '' }}>B+</option>
                        <option value="B-" {{ request('blood_group') == 'B-' ? 'selected' : '' }}>B-</option>
                        <option value="AB+" {{ request('blood_group') == 'AB+' ? 'selected' : '' }}>AB+</option>
                        <option value="AB-" {{ request('blood_group') == 'AB-' ? 'selected' : '' }}>AB-</option>
                        <option value="O+" {{ request('blood_group') == 'O+' ? 'selected' : '' }}>O+</option>
                        <option value="O-" {{ request('blood_group') == 'O-' ? 'selected' : '' }}>O-</option>
                    </select>
                </div>

                <div class="col-md-2">
                    <select name="status" class="form-select">
                        <option value="">Tous les statuts</option>
                        <option value="1" {{ request('status') == '1' ? 'selected' : '' }}>Actif</option>
                        <option value="0" {{ request('status') == '0' ? 'selected' : '' }}>Inactif</option>
                    </select>
                </div>

                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="bi bi-search me-1"></i> Filtrer
                    </button>
                </div>

            </div>
        </form>
    </div>
</div>

        <!-- Liste des utilisateurs -->
        <div class="card border-0 shadow-sm">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>ID</th>
                                <th>Utilisateur</th>
                                <th>Email</th>
                                <th>Rôle</th>
                                <th>Statut</th>
                                <th>Inscrit le</th>
                                <th class="text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($users as $user)
                            <tr class="user-card">
                                <td><strong>#{{ $user->id }}</strong></td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="avatar bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-2" 
                                             style="width: 40px; height: 40px;">
                                            {{ substr($user->first_name, 0, 1) }}{{ substr($user->last_name, 0, 1) }}
                                        </div>
                                        <div>
                                            <strong>{{ $user->first_name }} {{ $user->last_name }}</strong>
                                            @if($user->role === 'donor' && $user->blood_group)
                                                <br><small class="text-danger">{{ $user->blood_group }}</small>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td>{{ $user->email }}</td>
                                <td>
                                    @if($user->role === 'admin')
                                        <span class="badge bg-warning role-badge">
                                            <i class="bi bi-shield-fill me-1"></i> Admin
                                        </span>
                                    @elseif($user->role === 'agent')
                                        <span class="badge bg-info role-badge">
                                            <i class="bi bi-person-badge me-1"></i> Agent
                                        </span>
                                    @else
                                        <span class="badge bg-danger role-badge">
                                            <i class="bi bi-heart-pulse me-1"></i> Donneur
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    @if($user->is_active)
                                        <span class="badge bg-success">Actif</span>
                                    @else
                                        <span class="badge bg-secondary">Inactif</span>
                                    @endif
                                </td>
                                <td>
                                    <small class="text-muted">
                                        {{ $user->created_at->format('d/m/Y') }}
                                    </small>
                                </td>
                                <td class="text-center">
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('admin.users.donations', $user) }}" 
                                           class="btn btn-sm btn-outline-primary"
                                           title="Voir">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        <a href="" 
                                           class="btn btn-sm btn-outline-warning"
                                           title="Modifier">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <button type="button" 
                                                class="btn btn-sm btn-outline-danger"
                                                data-bs-toggle="modal"
                                                data-bs-target="#deleteModal{{ $user->id }}"
                                                title="Supprimer">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>

                            <!-- Modal de suppression -->
                            <div class="modal fade" id="deleteModal{{ $user->id }}" tabindex="-1">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header bg-danger text-white">
                                            <h5 class="modal-title">Confirmer la suppression</h5>
                                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body">
                                            <p>Êtes-vous sûr de vouloir supprimer l'utilisateur :</p>
                                            <p class="mb-0"><strong>{{ $user->first_name }} {{ $user->last_name }}</strong></p>
                                            <p class="text-muted small">Cette action est irréversible.</p>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                                            <form method="POST" action="{{ route('admin.users.destroy', $user->id) }}" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-delete btn-danger">Supprimer</button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @empty
                            <tr>
                                <td colspan="7" class="text-center py-5">
                                    <i class="bi bi-inbox display-4 text-muted"></i>
                                    <p class="text-muted mt-3">Aucun utilisateur trouvé</p>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Pagination -->
        <div class="mt-4">
            {{ $users->links() }}
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
</body>
</html>