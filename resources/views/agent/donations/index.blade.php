@extends('layouts.agent')

@section('content')
<div class="page-header mb-4 d-flex justify-content-between align-items-center">
    <h2><i class="bi bi-droplet-half me-2"></i>Liste des Dons</h2>
    <a href="{{ route('agent.donations.create') }}" class="btn btn-danger">
        <i class="bi bi-plus-circle me-1"></i>Nouveau don
    </a>
</div>

<!-- Statistiques -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="card text-center shadow-sm">
            <div class="card-body">
                <h6>Total dons</h6>
                <h3>{{ $stats['total'] }}</h3>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-center shadow-sm">
            <div class="card-body">
                <h6>Dons du jour</h6>
                <h3>{{ $stats['today'] }}</h3>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-center shadow-sm">
            <div class="card-body">
                <h6>En attente</h6>
                <h3>{{ $stats['pending'] }}</h3>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-center shadow-sm">
            <div class="card-body">
                <h6>Validés</h6>
                <h3>{{ $stats['validated'] }}</h3>
            </div>
        </div>
    </div>
</div>

<!-- Tableau des dons -->
<div class="card shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>Donneur</th>
                        <th>Agent</th>
                        <th>Groupe sanguin</th>
                        <th>Type</th>
                        <th>Quantité (ml)</th>
                        <th>Status</th>
                        <th>Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
@forelse($donations as $donation)
    <tr>
        <td>{{ $loop->iteration }}</td>
        <td>{{ $donation->donor->full_name ?? 'N/A' }}</td>
        <td>{{ $donation->agent->full_name ?? 'N/A' }}</td>
        <td>{{ $donation->blood_group }}</td>
        <td>{{ ucfirst($donation->donation_type) }}</td>
        <td>{{ $donation->quantity_ml }}</td>
        <td>
            @if($donation->status=='pending')
                <span class="badge bg-warning">En attente</span>
            @else
                <span class="badge bg-success">Validé</span>
            @endif
        </td>
        <td>{{ $donation->donation_date->format('d/m/Y') }}</td>
        <td>
            <a href="{{ route('agent.donations.show', $donation->id) }}" class="btn btn-sm btn-info"><i class="bi bi-eye"></i></a>

            @if($donation->status=='pending')
                <a href="{{ route('agent.donations.edit', $donation->id) }}" class="btn btn-sm btn-warning"><i class="bi bi-pencil-square"></i></a>

                <form action="{{ route('agent.donations.destroy', $donation->id) }}" method="POST" class="d-inline-block" onsubmit="return confirm('Voulez-vous vraiment supprimer ce don ?');">
                    @csrf
                    @method('DELETE')
                    <button class="btn btn-sm btn-danger"><i class="bi bi-trash"></i></button>
                </form>
            @endif
        </td>
    </tr>
@empty
    <tr>
        <td colspan="9" class="text-center py-3">Aucun don trouvé.</td>
    </tr>
@endforelse
                </tbody>
            </table>
        </div>
        <div class="p-3">
            {{ $donations->links() }}
        </div>
    </div>
</div>
@endsection
