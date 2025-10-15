@extends('layouts.donor') 

@section('title', 'Notifications')

@section('content')
<div class="container py-4">
    <h3>Notifications</h3>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="list-group">
        @forelse($notifications as $n)
            <div class="list-group-item d-flex justify-content-between align-items-start {{ $n->is_read ? '' : 'bg-light' }}">
                <div>
                    <h6 class="mb-1">{{ $n->title }}</h6>
                    <p class="mb-1">{{ $n->message }}</p>
                    <small class="text-muted">{{ $n->created_at->diffForHumans() }}</small>
                </div>
                <div class="text-end">
                    @if(!$n->is_read)
                        <form action="{{ route('notifications.read', $n->id) }}" method="POST">
                            @csrf
                            <button class="btn btn-sm btn-outline-primary">Marquer comme lu</button>
                        </form>
                    @else
                        <span class="badge bg-success">Lu</span>
                    @endif
                </div>
            </div>
        @empty
            <div class="p-4 text-muted">Aucune notification.</div>
        @endforelse
    </div>

    <div class="mt-3">
        {{ $notifications->links() }}
    </div>
</div>
@endsection
