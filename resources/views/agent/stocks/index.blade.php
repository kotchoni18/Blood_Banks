@extends('layouts.agent')

@section('title', 'Stocks sanguins')

@section('content')
<div class="container py-4">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0"><i class="bi bi-droplet me-2"></i> Stocks sanguins</h5>
            <a href="{{ route('agent.dashboard') }}" class="btn btn-sm btn-outline-secondary">← Tableau de bord</a>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead>
                        <tr>
                            <th>Groupe</th>
                            <th>Quantité (ml)</th>
                            <th>Statut</th>
                            <th>Expiration</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($stocks as $s)
                            <tr>
                                <td>{{ $s->blood_group }}</td>
                                <td>{{ number_format($s->quantity_units) }}</td>
                                <td>
                                    @if($s->status === 'critical') <span class="badge bg-danger">Critique</span>
                                    @elseif($s->status === 'empty') <span class="badge bg-secondary">Vide</span>
                                    @else <span class="badge bg-success">OK</span>
                                    @endif
                                </td>
                                <td>{{ $s->expiry_date ? \Carbon\Carbon::parse($s->expiry_date)->format('d/m/Y') : 'N/A' }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="text-center">Aucun stock enregistré</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
