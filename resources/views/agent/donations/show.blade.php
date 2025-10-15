@extends('layouts.agent')

@section('content')
<div class="page-header mb-4 d-flex justify-content-between align-items-center">
    <h2><i class="bi bi-eye me-2"></i>Détails du Don</h2>
    <a href="{{ route('agent.donations.index') }}" class="btn btn-secondary">
        <i class="bi bi-arrow-left-circle me-1"></i>Retour à la liste
    </a>
</div>

<div class="card shadow-sm">
    <div class="card-body">
        <h5>Donneur : {{ $donation->donor->full_name ?? 'N/A' }} ({{ $donation->blood_group }})</h5>
        <h5>Agent : {{ $donation->agent->full_name ?? 'N/A' }}</h5>
        <p><strong>Date :</strong> {{ $donation->donation_date->format('d/m/Y') }}</p>
        <p><strong>Type :</strong> {{ ucfirst($donation->donation_type) }}</p>
        <p><strong>Quantité :</strong> {{ $donation->quantity_ml }} ml</p>
        <p><strong>Poids :</strong> {{ $donation->weight }} kg</p>
        <p><strong>Tension :</strong> {{ $donation->blood_pressure }}</p>
        <p><strong>Hémoglobine :</strong> {{ $donation->hemoglobin_level }} g/dL</p>
        <p><strong>Température :</strong> {{ $donation->temperature ?? 'N/A' }} °C</p>
        <p><strong>Status :</strong>
            @if($donation->status === 'pending')
                <span class="badge bg-warning">En attente</span>
            @else
                <span class="badge bg-success">Validé</span>
            @endif
        </p>
        <p><strong>Notes :</strong> {{ $donation->notes ?? '-' }}</p>

        <div class="mt-3">
            @if($donation->status === 'pending')
                <a href="{{ route('agent.donations.edit', $donation->id) }}" class="btn btn-warning"><i class="bi bi-pencil-square me-1"></i>Modifier</a>
                <form action="{{ route('agent.donations.destroy', $donation->id) }}" method="POST" class="d-inline-block" onsubmit="return confirm('Voulez-vous vraiment supprimer ce don ?');">
                    @csrf
                    @method('DELETE')
                    <button class="btn btn-danger"><i class="bi bi-trash me-1"></i>Supprimer</button>
                </form>
                <form action="{{ route('agent.donations.validate', $donation->id) }}" method="POST" class="d-inline-block">
                    @csrf
                    <button class="btn btn-success"><i class="bi bi-check-circle me-1"></i>Valider</button>
                </form>
            @endif
            {{-- <a href="{{ route('agent.donations.receipt', $donation->id) }}" class="btn btn-info"><i class="bi bi-file-earmark-pdf me-1"></i>Reçu PDF</a> --}}
        </div>
    </div>
</div>
@endsection
