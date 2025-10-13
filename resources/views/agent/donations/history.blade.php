@extends('layouts.agent')

@section('title', 'Historique des dons')

@section('content')
<div class="container py-4">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0"><i class="bi bi-clock-history me-2"></i> Historique des dons</h5>
            <a href="{{ route('agent.dashboard') }}" class="btn btn-sm btn-outline-secondary">← Tableau de bord</a>
        </div>

        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Donneur</th>
                            <th>Groupe</th>
                            <th>Type</th>
                            <th>Quantité</th>
                            <th>Date</th>
                            <th>Statut</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($donations as $don)
                            <tr>
                                <td>{{ $don->id }}</td>
                                <td>{{ optional($don->donor)->first_name ?? 'Anonyme' }} {{ optional($don->donor)->last_name ?? '' }}</td>
                                <td>{{ $don->blood_group ?? '-' }}</td>
                                <td>{{ $don->donation_type ?? '-' }}</td>
                                <td>{{ $don->quantity_ml }}</td>
                                <td>{{ \Carbon\Carbon::parse($don->donation_date)->format('d/m/Y H:i') }}</td>
                                <td>
                                    @if($don->status === 'completed') <span class="badge bg-success">Complété</span>
                                    @elseif($don->status === 'pending') <span class="badge bg-warning text-dark">En attente</span>
                                    @else <span class="badge bg-danger">Échoué</span>
                                    @endif
                                </td>
                                <td>
                                    <a class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#donModal{{ $don->id }}">Voir</a>
                                </td>
                            </tr>

                            {{-- Modal (détails) --}}
                            <div class="modal fade" id="donModal{{ $don->id }}" tabindex="-1" aria-hidden="true">
                              <div class="modal-dialog modal-lg modal-dialog-centered">
                                <div class="modal-content">
                                  <div class="modal-header">
                                    <h5 class="modal-title">Détails du don #{{ $don->id }}</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                  </div>
                                  <div class="modal-body">
                                    {{-- copie le markup de détail du don déjà fourni plus haut --}}
                                    <div class="row">
                                        <div class="col-md-6">
                                            <h6>Donneur</h6>
                                            <p>
                                              <strong>{{ optional($don->donor)->first_name ?? '' }} {{ optional($don->donor)->last_name ?? '' }}</strong><br>
                                              {{ optional($don->donor)->phone ?? '—' }}<br>
                                              {{ optional($don->donor)->email ?? '—' }}
                                            </p>
                                        </div>
                                        <div class="col-md-6">
                                            <h6>Informations du don</h6>
                                            <p>
                                                <strong>Groupe : </strong> {{ $don->blood_group ?? '—' }} <br>
                                                <strong>Type : </strong> {{ $don->donation_type ?? '—' }} <br>
                                                <strong>Quantité : </strong> {{ $don->quantity_ml }} ml <br>
                                                <strong>Date : </strong> {{ \Carbon\Carbon::parse($don->donation_date)->format('d/m/Y H:i') }} <br>
                                                <strong>Statut : </strong> {{ $don->status }} <br>
                                            </p>
                                        </div>
                                    </div>

                                    <hr>

                                    <div class="row">
                                        <div class="col-md-4"><strong>Hémoglobine</strong><p>{{ $don->hemoglobin_level ?? '—' }}</p></div>
                                        <div class="col-md-4"><strong>Tension</strong><p>{{ $don->blood_pressure ?? '—' }}</p></div>
                                        <div class="col-md-4"><strong>Poids</strong><p>{{ $don->weight ?? '—' }} kg</p></div>
                                    </div>

                                    <div class="mt-3"><h6>Notes médicales</h6><p>{{ $don->medical_notes ?? 'Aucune' }}</p></div>

                                    <div class="row mt-2">
                                        <div class="col-md-4"><strong>Consentement</strong><p>{{ $don->consent_given ? 'Oui' : 'Non' }}</p></div>
                                        <div class="col-md-4"><strong>Contrôle médical</strong><p>{{ $don->medical_check_passed ? 'OK' : 'Non' }}</p></div>
                                        <div class="col-md-4"><strong>Éligibilité</strong><p>{{ $don->eligibility_verified ? 'Vérifiée' : 'Non' }}</p></div>
                                    </div>
                                  </div>
                                  <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
                                  </div>
                                </div>
                              </div>
                            </div>

                        @empty
                            <tr><td colspan="8" class="text-center">Aucun don trouvé.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-3">{{ $donations->links() }}</div>
        </div>
    </div>
</div>
@endsection
