<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Historique des dons - {{ $user->first_name }} {{ $user->last_name }}</title>
    <!-- Lien CDN Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container py-5">

    <!-- Titre -->
    <div class="mb-4">
        <h2 class="fw-bold">
            Historique des dons de {{ $user->first_name }} {{ $user->last_name }}
        </h2>
        <p class="text-muted">Liste des dons enregistrés pour cet utilisateur.</p>
    </div>

    <!-- Bouton retour -->
    <a href="{{ route('admin.users.index') }}" class="btn btn-secondary mb-3">
        ← Retour à la liste des utilisateurs
    </a>

    <!-- Tableau des dons -->
    @if($donations->isEmpty())
        <div class="alert alert-info">
            Aucun don trouvé pour cet utilisateur.
        </div>
    @else
        <table class="table table-bordered table-striped">
            <thead class="table-dark">
                <tr>
                    <th>#</th>
                    <th>Date du don</th>
                    <th>Type</th>
                    <th>Quantité</th>
                    <th>Statut</th>
                </tr>
            </thead>
            <tbody>
                @foreach($donations as $index => $donation)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $donation->donation_date }}</td>
                        <td>{{ $donation->type ?? 'N/A' }}</td>
                        <td>{{ $donation->quantity ?? 'N/A' }}</td>
                        <td>
                            <span class="badge 
                                @if($donation->status == 'completed') bg-success 
                                @elseif($donation->status == 'pending') bg-warning 
                                @else bg-secondary 
                                @endif">
                                {{ ucfirst($donation->status) }}
                            </span>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

</div>

<!-- JS Bootstrap -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
