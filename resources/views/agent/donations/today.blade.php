@extends('layouts.agent')

@section('content')
<div class="page-header mb-4">
    <h2><i class="bi bi-calendar-day me-2"></i>Dons du jour ({{ date('d/m/Y') }})</h2>
</div>

<div class="row mb-4">
    <div class="col-md-4">
        <div class="card text-center shadow-sm">
            <div class="card-body">
                <h6>Total dons</h6>
                <h3>{{ $stats['count'] }}</h3>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card text-center shadow-sm">
            <div class="card-body">
                <h6>Volume total</h6>
                <h3>{{ $stats['volume'] }} ml</h3>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card text-center shadow-sm">
            <div class="card-body">
                <h6>Dons par groupe sanguin</h6>
                <ul class="list-unstyled mb-0">
                    @foreach($stats['by_blood_group'] as $group => $count)
                        <li>{{ $group }} : {{ $count }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
</div>

<div class="card shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>Donneur</th>
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
                            <td>{{ ucfirst($donation->donation_type) }}</td>
                            <td>{{ $donation->quantity_ml }}</td>
                            <td>
                                @if($donation->status === 'pending')
                                    <span class="badge bg-warning">En attente</span>
                                @else
                                    <span class="badge bg-success">Validé</span>
                                @endif
                            </td>
                            <td>{{ $donation->donation_date->format('d/m/Y') }}</td>
                            <td>
                                <a href="{{ route('agent.donations.show', $donation->id) }}" class="btn btn-sm btn-info" title="Voir">
                                    <i class="bi bi-eye"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-3">Aucun don enregistré aujourd'hui.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
