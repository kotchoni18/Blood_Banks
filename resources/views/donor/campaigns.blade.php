@extends('layouts.donor')

@section('title', 'Campagnes de don')

@section('content')
<div class="container py-5">
    <h2 class="mb-4">
        <i class="bi bi-megaphone me-2 text-danger"></i>
        Campagnes de don
    </h2>

    @if($campaigns->isEmpty())
        <div class="alert alert-info">
            Aucune campagne disponible pour le moment.
        </div>
    @else
        <div class="card p-4">
            <ul class="list-group">
                @foreach($campaigns as $campaign)
                    <li class="list-group-item">
                        <strong>{{ $campaign->title }}</strong><br>
                        {{ $campaign->description }}<br>
                        <small>
                            Du {{ \Carbon\Carbon::parse($campaign->start_date)->format('d/m/Y') }}
                            au {{ \Carbon\Carbon::parse($campaign->end_date)->format('d/m/Y') }}
                        </small>
                    </li>
                @endforeach
            </ul>

            <div class="mt-3">
                {{ $campaigns->links() }}
            </div>
        </div>
    @endif
</div>
@endsection
