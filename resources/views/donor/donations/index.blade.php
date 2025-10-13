@extends('layouts.donor') {{-- utilise exactement ton layout --}}

@section('title', 'Mes Dons')

@section('content')
<div class="container py-5">
    <h2 class="mb-4">
        <i class="bi bi-droplet me-2 text-danger"></i>
        Historique de mes dons
    </h2>

    @if($donations->isEmpty())
        <div class="alert alert-info">
            <i class="bi bi-info-circle me-2"></i>
            Vous n'avez effectué aucun don pour le moment.
        </div>
    @else
        <div class="card p-4">
            <div class="table-responsive">
                <table class="table table-bordered align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Date</th>
                            <th>Type de don</th>
                            <th>Groupe sanguin</th>
                            <th>Quantité</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($donations as $don)
                            <tr>
                                <td>{{ $don->created_at->format('d/m/Y') }}</td>
                                <td>
                                    <span class="donation-badge">
                                        {{ ucfirst($don->donation_type) }}
                                    </span>
                                </td>
                                <td>{{ $don->blood_group }}</td>
                                <td>
                                    {{ $don->quantity_units ?? $don->quantity_ml }} 
                                    {{ $don->quantity_units ? 'unités' : 'ml' }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            <div class="mt-3">
                {{ $donations->links() }}
            </div>
        </div>
    @endif
</div>
@endsection
