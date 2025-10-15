@extends('layouts.agent')

@section('content')
<div class="page-header mb-4 d-flex justify-content-between align-items-center">
    <h2><i class="bi bi-pencil-square me-2"></i>Modifier le Don</h2>
    <a href="{{ route('agent.donations.show', $donation->id) }}" class="btn btn-secondary">
        <i class="bi bi-arrow-left-circle me-1"></i>Retour
    </a>
</div>

<div class="card shadow-sm">
    <div class="card-body">
        <form action="{{ route('agent.donations.update', $donation->id) }}" method="POST">
    @csrf
    @method('PUT')

    <div class="row mb-3">
        <div class="col-md-4">
            <label for="quantity_ml" class="form-label">Quantité (ml)</label>
            <input type="number" name="quantity_ml" id="quantity_ml" class="form-control @error('quantity_ml') is-invalid @enderror" value="{{ old('quantity_ml', $donation->quantity_ml) }}">
            @error('quantity_ml') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>
        <div class="col-md-4">
            <label for="weight" class="form-label">Poids (kg)</label>
            <input type="number" name="weight" id="weight" class="form-control @error('weight') is-invalid @enderror" value="{{ old('weight', $donation->weight) }}">
            @error('weight') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>
        <div class="col-md-4">
            <label for="blood_pressure" class="form-label">Tension (mmHg)</label>
            <input type="text" name="blood_pressure" id="blood_pressure" class="form-control @error('blood_pressure') is-invalid @enderror" value="{{ old('blood_pressure', $donation->blood_pressure) }}">
            @error('blood_pressure') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>
    </div>

    <div class="row mb-3">
        <div class="col-md-4">
            <label for="hemoglobin_level" class="form-label">Hémoglobine (g/dL)</label>
            <input type="number" step="0.1" name="hemoglobin_level" id="hemoglobin_level" class="form-control @error('hemoglobin_level') is-invalid @enderror" value="{{ old('hemoglobin_level', $donation->hemoglobin_level) }}">
            @error('hemoglobin_level') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>
    </div>

    <div class="mb-3">
        <label for="medical_notes" class="form-label">Notes médicales</label>
        <textarea name="medical_notes" id="medical_notes" class="form-control">{{ old('medical_notes', $donation->medical_notes) }}</textarea>
    </div>

    <button type="submit" class="btn btn-danger"><i class="bi bi-check2-circle me-1"></i>Mettre à jour</button>
    <a href="{{ route('agent.donations.show', $donation->id) }}" class="btn btn-secondary ms-2">Annuler</a>
</form>

    </div>
</div>
@endsection
