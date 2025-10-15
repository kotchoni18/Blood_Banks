@extends('layouts.admin') 

@section('title', 'Stocks critiques')

@section('content')
<div class="container">
    <h3>Stocks critiques</h3>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <table class="table table-hover">
        <thead>
            <tr>
                <th>Groupe</th>
                <th>Quantité</th>
                <th>Statut</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            @forelse($critical as $s)
                <tr>
                    <td>{{ $s->blood_group }}</td>
                    <td>{{ number_format($s->quantity_units) }}</td>
                    <td><span class="badge bg-danger">Critique</span></td>
                    <td>
                        <!-- Button: ouvre modal pour composer la notification -->
                        <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#notifyModal{{ $s->blood_group|md5 }}">
                            Notifier donneurs
                        </button>

                        <!-- Modal -->
                        <div class="modal fade" id="notifyModal{{ md5($s->blood_group) }}" tabindex="-1">
                            <div class="modal-dialog">
                                <form method="POST" action="{{ route('admin.notifications.notifyGroup') }}">
                                    @csrf
                                    <input type="hidden" name="blood_group" value="{{ $s->blood_group }}">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title">Notifier les donneurs ({{ $s->blood_group }})</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body">
                                            <div class="mb-3">
                                                <label class="form-label">Titre</label>
                                                <input type="text" name="title" class="form-control" value="Besoin urgent de sang {{ $s->blood_group }}" required>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Message</label>
                                                <textarea name="message" class="form-control" rows="5" required>Le stock du groupe {{ $s->blood_group }} est critique. Nous avons besoin de dons. Merci de vous rendre au centre.</textarea>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                                            <button type="submit" class="btn btn-primary">Envoyer</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>

                    </td>
                </tr>
            @empty
                <tr><td colspan="4">Aucun stock critique</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
