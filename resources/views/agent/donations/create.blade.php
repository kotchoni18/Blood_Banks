@extends('layouts.agent')

@section('content')
<div class="page-header mb-4 d-flex justify-content-between align-items-center">
    <h2><i class="bi bi-plus-circle me-2"></i> Nouveau Don</h2>
    <a href="{{ route('agent.donations.index') }}" class="btn btn-secondary">
        <i class="bi bi-arrow-left-circle me-1"></i>Retour à la liste
    </a>
</div>

<div class="card shadow-sm">
    <div class="card-body">
        <form action="{{ route('agent.donations.store') }}" method="POST">
            @csrf
            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="donor_id" class="form-label">Donneur</label>
                    <select name="donor_id" id="donor_id" class="form-select @error('donor_id') is-invalid @enderror">
                        <option value="">-- Sélectionner un donneur --</option>
                        @foreach($donors as $donor)
                            <option value="{{ $donor->id }}" {{ old('donor_id') == $donor->id ? 'selected' : '' }}>
                                {{ $donor->full_name }} ({{ $donor->blood_group }})
                            </option>
                        @endforeach
                    </select>
                    @error('donor_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="col-md-6">
                    <label for="donation_date" class="form-label">Date du don</label>
                    <input type="date" name="donation_date" id="donation_date" class="form-control @error('donation_date') is-invalid @enderror" value="{{ old('donation_date', now()->format('Y-m-d')) }}">
                    @error('donation_date') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-4">
                    <label for="donation_type" class="form-label">Type de don</label>
                    <select name="donation_type" id="donation_type" class="form-select @error('donation_type') is-invalid @enderror">
                        <option value="">-- Sélectionner --</option>
                        <option value="complete" {{ old('donation_type')=='complete' ? 'selected':'' }}>Sang complet</option>
                        <option value="plasma" {{ old('donation_type')=='plasma' ? 'selected':'' }}>Plasma</option>
                        <option value="plaquettes" {{ old('donation_type')=='plaquettes' ? 'selected':'' }}>Plaquettes</option>
                    </select>
                    @error('donation_type') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="col-md-4">
                    <label for="quantity_ml" class="form-label">Quantité (ml)</label>
                    <input type="number" name="quantity_ml" id="quantity_ml" class="form-control @error('quantity_ml') is-invalid @enderror" value="{{ old('quantity_ml') }}">
                    @error('quantity_ml') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="col-md-4">
                    <label for="bag_number" class="form-label">Numéro de poche</label>
                    <input type="text" name="bag_number" id="bag_number" class="form-control @error('bag_number') is-invalid @enderror" value="{{ old('bag_number') }}">
                    @error('bag_number') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-4">
                    <label for="weight" class="form-label">Poids (kg)</label>
                    <input type="number" name="weight" id="weight" class="form-control @error('weight') is-invalid @enderror" value="{{ old('weight') }}">
                    @error('weight') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="col-md-4">
                    <label for="blood_pressure" class="form-label">Tension (mmHg)</label>
                    <input type="text" name="blood_pressure" id="blood_pressure" class="form-control @error('blood_pressure') is-invalid @enderror" value="{{ old('blood_pressure') }}">
                    @error('blood_pressure') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="col-md-4">
                    <label for="hemoglobin_level" class="form-label">Hémoglobine (g/dL)</label>
                    <input type="number" step="0.1" name="hemoglobin_level" id="hemoglobin_level" class="form-control @error('hemoglobin_level') is-invalid @enderror" value="{{ old('hemoglobin_level') }}">
                    @error('hemoglobin_level') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
            </div>

            <div class="mb-3">
                <label for="notes" class="form-label">Notes médicales</label>
                <textarea name="notes" id="notes" class="form-control">{{ old('notes') }}</textarea>
            </div>

            <button type="submit" class="btn btn-danger"><i class="bi bi-check2-circle me-1"></i>Enregistrer</button>
            <a href="{{ route('agent.donations.index') }}" class="btn btn-secondary ms-2">Annuler</a>
        </form>
    </div>
</div>
@endsection
