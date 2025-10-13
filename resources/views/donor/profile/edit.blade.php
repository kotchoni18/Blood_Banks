@extends('layouts.donor')

@section('title', 'Modifier mon profil')

@section('content')
<div class="container">
    <div class="row">
        <!-- Sidebar (même que dans show.blade.php) -->
        <div class="col-lg-3 mb-4">
            <div class="card">
                <div class="card-body text-center">
                    <!-- Photo de profil avec option de changement -->
                    <div class="profile-avatar mb-3 position-relative">
                        @if($user->avatar)
                            <img src="{{ asset('storage/avatars/' . $user->avatar) }}" 
                                 alt="Photo de profil" 
                                 class="rounded-circle" 
                                 width="120" 
                                 height="120"
                                 style="object-fit: cover;"
                                 id="avatarPreview">
                        @else
                            <div class="bg-primary rounded-circle d-flex align-items-center justify-content-center text-white" 
                                 style="width: 120px; height: 120px; font-size: 48px;"
                                 id="avatarPreview">
                                {{ strtoupper(substr($user->name, 0, 1)) }}
                            </div>
                        @endif
                        
                        <!-- Bouton pour changer la photo -->
                        <button type="button" class="btn btn-primary btn-sm position-absolute bottom-0 end-0 rounded-circle" 
                                data-bs-toggle="modal" data-bs-target="#avatarModal">
                            <i class="bi bi-camera"></i>
                        </button>
                    </div>
                    
                    <h5 class="mb-1">{{ $user->name }}</h5>
                    <p class="text-muted mb-0">{{ $user->email }}</p>
                </div>
            </div>

            <!-- Menu Navigation -->
            <div class="card mt-3">
                <div class="list-group list-group-flush">
                    <a href="{{ route('donor.profile.show') }}" class="list-group-item list-group-item-action">
                        <i class="bi bi-person me-2"></i>Aperçu du profil
                    </a>
                    <a href="{{ route('donor.profile.edit') }}" class="list-group-item list-group-item-action active">
                        <i class="bi bi-pencil-square me-2"></i>Modifier le profil
                    </a>
                    <a href="" class="list-group-item list-group-item-action">
                        <i class="bi bi-heart-pulse me-2"></i>Historique médical
                    </a>
                    <a href="{{ route('donor.profile.notifications') }}" class="list-group-item list-group-item-action">
                        <i class="bi bi-bell me-2"></i>Notifications
                    </a>
                </div>
            </div>
        </div>

        <!-- Contenu principal -->
        <div class="col-lg-9">
            @if($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show">
                    <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <!-- Formulaire d'édition -->
            <form action="{{ route('donor.profile.update') }}" method="POST">
                @csrf
                @method('PUT')

                <!-- Informations de base -->
                <div class="card mb-4">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">
                            <i class="bi bi-person me-2"></i>Informations de Base
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="name" class="form-label">Nom complet <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                       id="name" name="name" value="{{ old('name', $user->name) }}" required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                                <input type="email" class="form-control @error('email') is-invalid @enderror" 
                                       id="email" name="email" value="{{ old('email', $user->email) }}" required>
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="phone" class="form-label">Téléphone</label>
                                <input type="tel" class="form-control @error('phone') is-invalid @enderror" 
                                       id="phone" name="phone" value="{{ old('phone', $user->phone) }}">
                                @error('phone')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="birth_date" class="form-label">Date de naissance</label>
                                <input type="date" class="form-control @error('birth_date') is-invalid @enderror" 
                                       id="birth_date" name="birth_date" 
                                       value="{{ old('birth_date', $user->birth_date?->format('Y-m-d')) }}">
                                @error('birth_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="gender" class="form-label">Genre <span class="text-danger">*</span></label>
                                <select class="form-select @error('gender') is-invalid @enderror" id="gender" name="gender" required>
                                    <option value="">-- Sélectionner --</option>
                                    <option value="M" {{ old('gender', $user->gender) == 'M' ? 'selected' : '' }}>Homme</option>
                                    <option value="F" {{ old('gender', $user->gender) == 'F' ? 'selected' : '' }}>Femme</option>
                                </select>
                                @error('gender')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="blood_type" class="form-label">Groupe sanguin</label>
                                <select class="form-select @error('blood_type') is-invalid @enderror" id="blood_type" name="blood_type">
                                    <option value="">-- Non déterminé --</option>
                                    <option value="A+" {{ old('blood_type', $user->blood_type) == 'A+' ? 'selected' : '' }}>A+</option>
                                    <option value="A-" {{ old('blood_type', $user->blood_type) == 'A-' ? 'selected' : '' }}>A-</option>
                                    <option value="B+" {{ old('blood_type', $user->blood_type) == 'B+' ? 'selected' : '' }}>B+</option>
                                    <option value="B-" {{ old('blood_type', $user->blood_type) == 'B-' ? 'selected' : '' }}>B-</option>
                                    <option value="AB+" {{ old('blood_type', $user->blood_type) == 'AB+' ? 'selected' : '' }}>AB+</option>
                                    <option value="AB-" {{ old('blood_type', $user->blood_type) == 'AB-' ? 'selected' : '' }}>AB-</option>
                                    <option value="O+" {{ old('blood_type', $user->blood_type) == 'O+' ? 'selected' : '' }}>O+</option>
                                    <option value="O-" {{ old('blood_type', $user->blood_type) == 'O-' ? 'selected' : '' }}>O-</option>
                                </select>
                                @error('blood_type')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Informations physiques -->
                <div class="card mb-4">
                    <div class="card-header bg-info text-white">
                        <h5 class="mb-0">
                            <i class="bi bi-body-text me-2"></i>Informations Physiques
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="weight" class="form-label">Poids (kg)</label>
                                <input type="number" class="form-control @error('weight') is-invalid @enderror" 
                                       id="weight" name="weight" value="{{ old('weight', $user->weight) }}" 
                                       min="50" max="200" step="0.1">
                                @error('weight')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">Poids minimum requis : 50 kg</div>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="height" class="form-label">Taille (cm)</label>
                                <input type="number" class="form-control @error('height') is-invalid @enderror" 
                                       id="height" name="height" value="{{ old('height', $user->height) }}" 
                                       min="140" max="220">
                                @error('height')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Adresse -->
                <div class="card mb-4">
                    <div class="card-header bg-secondary text-white">
                        <h5 class="mb-0">
                            <i class="bi bi-geo-alt me-2"></i>Adresse
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-8 mb-3">
                                <label for="address" class="form-label">Adresse complète</label>
                                <textarea class="form-control @error('address') is-invalid @enderror" 
                                          id="address" name="address" rows="2">{{ old('address', $user->address) }}</textarea>
                                @error('address')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-4 mb-3">
                                <label for="city" class="form-label">Ville</label>
                                <select class="form-select @error('city') is-invalid @enderror" id="city" name="city">
                                    <option value="">-- Sélectionner une ville --</option>
                                    <option value="Cotonou" {{ old('city', $user->city) == 'Cotonou' ? 'selected' : '' }}>Cotonou</option>
                                    <option value="Porto-Novo" {{ old('city', $user->city) == 'Porto-Novo' ? 'selected' : '' }}>Porto-Novo</option>
                                    <option value="Parakou" {{ old('city', $user->city) == 'Parakou' ? 'selected' : '' }}>Parakou</option>
                                    <option value="Bohicon" {{ old('city', $user->city) == 'Bohicon' ? 'selected' : '' }}>Bohicon</option>
                                    <option value="Kandi" {{ old('city', $user->city) == 'Kandi' ? 'selected' : '' }}>Kandi</option>
                                    <option value="Abomey" {{ old('city', $user->city) == 'Abomey' ? 'selected' : '' }}>Abomey</option>
                                    <option value="Natitingou" {{ old('city', $user->city) == 'Natitingou' ? 'selected' : '' }}>Natitingou</option>
                                    <option value="Djougou" {{ old('city', $user->city) == 'Djougou' ? 'selected' : '' }}>Djougou</option>
                                    <option value="Autre" {{ old('city', $user->city) == 'Autre' ? 'selected' : '' }}>Autre</option>
                                </select>
                                @error('city')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Contact d'urgence -->
                <div class="card mb-4">
                    <div class="card-header bg-warning text-dark">
                        <h5 class="mb-0">
                            <i class="bi bi-person-exclamation me-2"></i>Contact d'Urgence
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="emergency_contact_name" class="form-label">Nom du contact d'urgence</label>
                                <input type="text" class="form-control @error('emergency_contact_name') is-invalid @enderror" 
                                       id="emergency_contact_name" name="emergency_contact_name" 
                                       value="{{ old('emergency_contact_name', $user->emergency_contact_name) }}">
                                @error('emergency_contact_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="emergency_contact_phone" class="form-label">Téléphone d'urgence</label>
                                <input type="tel" class="form-control @error('emergency_contact_phone') is-invalid @enderror" 
                                       id="emergency_contact_phone" name="emergency_contact_phone" 
                                       value="{{ old('emergency_contact_phone', $user->emergency_contact_phone) }}">
                                @error('emergency_contact_phone')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Informations médicales -->
                <div class="card mb-4">
                    <div class="card-header bg-danger text-white">
                        <h5 class="mb-0">
                            <i class="bi bi-heart-pulse me-2"></i>Informations Médicales
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-warning">
                            <i class="bi bi-exclamation-triangle me-2"></i>
                            <strong>Important :</strong> Ces informations sont confidentielles et ne seront utilisées que par l'équipe médicale.
                        </div>
                        
                        <div class="mb-3">
                            <label for="medical_conditions" class="form-label">Conditions médicales particulières</label>
                            <textarea class="form-control @error('medical_conditions') is-invalid @enderror" 
                                      id="medical_conditions" name="medical_conditions" rows="3"
                                      placeholder="Ex: Diabète, hypertension, allergies...">{{ old('medical_conditions', $user->medical_conditions) }}</textarea>
                            @error('medical_conditions')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label for="medications" class="form-label">Médicaments actuels</label>
                            <textarea class="form-control @error('medications') is-invalid @enderror" 
                                      id="medications" name="medications" rows="3"
                                      placeholder="Listez tous les médicaments que vous prenez actuellement...">{{ old('medications', $user->medications) }}</textarea>
                            @error('medications')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Boutons d'action -->
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex gap-3">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="bi bi-check2 me-2"></i>Enregistrer les modifications
                            </button>
                            <a href="{{ route('donor.profile.show') }}" class="btn btn-outline-secondary btn-lg">
                                <i class="bi bi-arrow-left me-2"></i>Annuler
                            </a>
                        </div>
                    </div>
                </div>
            </form>

            <!-- Section changement de mot de passe -->
            <div class="card mt-4">
                <div class="card-header bg-dark text-white">
                    <h5 class="mb-0">
                        <i class="bi bi-shield-lock me-2"></i>Sécurité
                    </h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('donor.profile.update-password') }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label for="current_password" class="form-label">Mot de passe actuel</label>
                                <input type="password" class="form-control @error('current_password') is-invalid @enderror" 
                                       id="current_password" name="current_password">
                                @error('current_password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-4 mb-3">
                                <label for="password" class="form-label">Nouveau mot de passe</label>
                                <input type="password" class="form-control @error('password') is-invalid @enderror" 
                                       id="password" name="password">
                                @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-4 mb-3">
                                <label for="password_confirmation" class="form-label">Confirmer le nouveau mot de passe</label>
                                <input type="password" class="form-control" id="password_confirmation" name="password_confirmation">
                            </div>
                        </div>
                        
                        <button type="submit" class="btn btn-warning">
                            <i class="bi bi-key me-2"></i>Changer le mot de passe
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal pour changer la photo de profil -->
<div class="modal fade" id="avatarModal" tabindex="-1" aria-labelledby="avatarModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="avatarModalLabel">Changer la photo de profil</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('donor.profile.update-avatar') }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="modal-body text-center">
                    <div class="mb-3">
                        <img id="previewImage" src="#" alt="Aperçu" class="rounded-circle d-none" width="150" height="150" style="object-fit: cover;">
                    </div>
                    <div class="mb-3">
                        <input type="file" class="form-control" id="avatar" name="avatar" accept="image/*" required>
                        <div class="form-text">Formats acceptés : JPEG, PNG, JPG, GIF (max 2MB)</div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-primary">Enregistrer</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Aperçu de l'image avant upload
    document.getElementById('avatar').addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                const preview = document.getElementById('previewImage');
                preview.src = e.target.result;
                preview.classList.remove('d-none');
            };
            reader.readAsDataURL(file);
        }
    });
    
    // Validation du poids minimum
    document.getElementById('weight').addEventListener('input', function(e) {
        const weight = parseFloat(e.target.value);
        if (weight && weight < 50) {
            e.target.setCustomValidity('Le poids minimum requis est de 50 kg pour donner son sang.');
        } else {
            e.target.setCustomValidity('');
        }
    });
});
</script>

<style>
.profile-avatar {
    position: relative;
    display: inline-block;
}

.card:hover {
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    transition: box-shadow 0.3s ease;
}

.form-control:focus, .form-select:focus, .form-check-input:focus {
    border-color: #0d6efd;
    box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.25);
}

.alert {
    border-radius: 10px;
}
</style>
@endsection