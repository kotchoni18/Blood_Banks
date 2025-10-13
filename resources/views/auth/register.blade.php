<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inscription - Banque de Sang</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.10.0/font/bootstrap-icons.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #e74c3c 0%, #c0392b 100%);
            min-height: 100vh;
            padding: 2rem 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .register-card {
            background: white;
            border-radius: 20px;
            box-shadow: 0 15px 35px rgba(0,0,0,0.1);
            overflow: hidden;
            margin-bottom: 2rem;
        }
        .register-header {
            background: linear-gradient(135deg, #e74c3c 0%, #c0392b 100%);
            color: white;
            padding: 2rem;
            text-align: center;
        }
        .form-control, .form-select {
            border-radius: 10px;
            border: 2px solid #e9ecef;
            padding: 12px 15px;
        }
        .form-control:focus, .form-select:focus {
            border-color: #e74c3c;
            box-shadow: 0 0 0 0.2rem rgba(231, 76, 60, 0.25);
        }
        .btn-primary {
            background: linear-gradient(135deg, #e74c3c 0%, #c0392b 100%);
            border: none;
            border-radius: 25px;
            padding: 12px 30px;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(231, 76, 60, 0.3);
        }
        .section-title {
            color: #e74c3c;
            font-weight: 600;
            margin-bottom: 1rem;
            padding-bottom: 0.5rem;
            border-bottom: 2px solid #e9ecef;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-7">
                <div class="register-card">
                    <div class="register-header">
                        <i class="bi bi-person-plus display-4 mb-3"></i>
                        <h3>Inscription Donneur</h3>
                        <p class="mb-0">Rejoignez notre communauté de donneurs de sang</p>
                    </div>
                    
                    <div class="p-4">
                        <!-- Messages d'erreur globaux -->
                        @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="bi bi-exclamation-triangle-fill me-2"></i>
                            {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                        @endif

                        @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="bi bi-check-circle-fill me-2"></i>
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                        @endif

                        <form method="POST" action="{{ route('register.post') }}">
                            @csrf
                            
                            <!-- Informations personnelles -->
                            <h6 class="section-title">
                                <i class="bi bi-person-circle me-2"></i>Informations personnelles
                            </h6>
                            
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="first_name" class="form-label">Prénom *</label>
                                    <input type="text" 
                                           class="form-control @error('first_name') is-invalid @enderror" 
                                           id="first_name" 
                                           name="first_name" 
                                           value="{{ old('first_name') }}" 
                                           placeholder="Votre prénom"
                                           required>
                                    @error('first_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <label for="last_name" class="form-label">Nom *</label>
                                    <input type="text" 
                                           class="form-control @error('last_name') is-invalid @enderror" 
                                           id="last_name" 
                                           name="last_name" 
                                           value="{{ old('last_name') }}" 
                                           placeholder="Votre nom"
                                           required>
                                    @error('last_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="email" class="form-label">Email *</label>
                                    <input type="email" 
                                           class="form-control @error('email') is-invalid @enderror" 
                                           id="email" 
                                           name="email" 
                                           value="{{ old('email') }}" 
                                           placeholder="exemple@email.com"
                                           required>
                                    @error('email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <label for="phone" class="form-label">Téléphone *</label>
                                    <input type="tel" 
                                           class="form-control @error('phone') is-invalid @enderror" 
                                           id="phone" 
                                           name="phone" 
                                           value="{{ old('phone') }}" 
                                           placeholder="+229 XX XX XX XX"
                                           required>
                                    @error('phone')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="birth_date" class="form-label">Date de naissance *</label>
                                    <input type="date" 
                                           class="form-control @error('birth_date') is-invalid @enderror" 
                                           id="birth_date" 
                                           name="birth_date" 
                                           value="{{ old('birth_date') }}" 
                                           max="{{ date('Y-m-d', strtotime('-18 years')) }}"
                                           required>
                                    <small class="text-muted">Vous devez avoir au moins 18 ans</small>
                                    @error('birth_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <label for="gender" class="form-label">Genre *</label>
                                    <select class="form-select @error('gender') is-invalid @enderror" 
                                            id="gender" 
                                            name="gender" 
                                            required>
                                        <option value="">Sélectionner</option>
                                        <option value="M" {{ old('gender') == 'M' ? 'selected' : '' }}>Masculin</option>
                                        <option value="F" {{ old('gender') == 'F' ? 'selected' : '' }}>Féminin</option>
                                        <option value="O" {{ old('gender') == 'O' ? 'selected' : '' }}>Autre</option>
                                    </select>
                                    @error('gender')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <!-- Informations médicales -->
                            <h6 class="section-title mt-4">
                                <i class="bi bi-heart-pulse me-2"></i>Informations médicales
                            </h6>
                            
                            <div class="mb-3">
                                <label for="blood_group" class="form-label">Groupe sanguin *</label>
                                <select class="form-select @error('blood_group') is-invalid @enderror" 
                                        id="blood_group" 
                                        name="blood_group" 
                                        required>
                                    <option value="">Sélectionner votre groupe sanguin</option>
                                    <option value="A+" {{ old('blood_group') == 'A+' ? 'selected' : '' }}>A+ (Rhésus positif)</option>
                                    <option value="A-" {{ old('blood_group') == 'A-' ? 'selected' : '' }}>A- (Rhésus négatif)</option>
                                    <option value="B+" {{ old('blood_group') == 'B+' ? 'selected' : '' }}>B+ (Rhésus positif)</option>
                                    <option value="B-" {{ old('blood_group') == 'B-' ? 'selected' : '' }}>B- (Rhésus négatif)</option>
                                    <option value="AB+" {{ old('blood_group') == 'AB+' ? 'selected' : '' }}>AB+ (Rhésus positif)</option>
                                    <option value="AB-" {{ old('blood_group') == 'AB-' ? 'selected' : '' }}>AB- (Rhésus négatif)</option>
                                    <option value="O+" {{ old('blood_group') == 'O+' ? 'selected' : '' }}>O+ (Rhésus positif)</option>
                                    <option value="O-" {{ old('blood_group') == 'O-' ? 'selected' : '' }}>O- (Rhésus négatif)</option>
                                </select>
                                <small class="text-muted">
                                    <i class="bi bi-info-circle me-1"></i>
                                    Si vous ne connaissez pas votre groupe sanguin, contactez un centre médical
                                </small>
                                @error('blood_group')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <!-- Adresse (optionnelle) -->
                            <h6 class="section-title mt-4">
                                <i class="bi bi-geo-alt me-2"></i>Adresse (optionnel)
                            </h6>
                            
                            <div class="mb-3">
                                <label for="address" class="form-label">Adresse complète</label>
                                <textarea class="form-control @error('address') is-invalid @enderror" 
                                          id="address" 
                                          name="address" 
                                          rows="2"
                                          placeholder="Rue, quartier, commune">{{ old('address') }}</textarea>
                                @error('address')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="mb-3">
                                <label for="city" class="form-label">Ville</label>
                                <input type="text" 
                                       class="form-control @error('city') is-invalid @enderror" 
                                       id="city" 
                                       name="city" 
                                       value="{{ old('city') }}" 
                                       placeholder="Ex: Cotonou, Porto-Novo...">
                                @error('city')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <!-- Mot de passe -->
                            <h6 class="section-title mt-4">
                                <i class="bi bi-shield-lock me-2"></i>Sécurité
                            </h6>
                            
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="password" class="form-label">Mot de passe *</label>
                                    <input type="password" 
                                           class="form-control @error('password') is-invalid @enderror" 
                                           id="password" 
                                           name="password" 
                                           placeholder="Minimum 8 caractères"
                                           required>
                                    <small class="text-muted">Au moins 8 caractères</small>
                                    @error('password')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <label for="password_confirmation" class="form-label">Confirmer le mot de passe *</label>
                                    <input type="password" 
                                           class="form-control" 
                                           id="password_confirmation" 
                                           name="password_confirmation" 
                                           placeholder="Retapez votre mot de passe"
                                           required>
                                </div>
                            </div>
                            
                            <!-- Conditions d'utilisation -->
                            <div class="form-check mb-4">
                                <input class="form-check-input" 
                                       type="checkbox" 
                                       id="terms" 
                                       required>
                                <label class="form-check-label" for="terms">
                                    J'accepte les <a href="#" class="text-danger text-decoration-none fw-bold">conditions d'utilisation</a> 
                                    et la <a href="#" class="text-danger text-decoration-none fw-bold">politique de confidentialité</a>
                                </label>
                            </div>
                            
                            <!-- Boutons -->
                            <div class="d-grid gap-2 d-md-flex justify-content-md-between mt-4">
                                <button type="submit" class="btn btn-primary btn-lg flex-md-fill">
                                    <i class="bi bi-person-check me-2"></i>
                                    Créer mon compte
                                </button>
                                
                                <a href="{{ route('donor.login') }}" class="btn btn-outline-secondary btn-lg">
                                    <i class="bi bi-arrow-left me-2"></i>
                                    Retour
                                </a>
                            </div>
                            
                            <!-- Lien connexion -->
                            <div class="text-center mt-4 pt-3 border-top">
                                <p class="text-muted mb-0">
                                    Vous avez déjà un compte ? 
                                    <a href="{{ route('donor.login') }}" class="text-danger fw-bold text-decoration-none">
                                        Se connecter
                                    </a>
                                </p>
                            </div>
                        </form>
                    </div>
                </div>
                
                <!-- Info supplémentaire -->
                <div class="text-center text-white">
                    <p class="mb-1">
                        <i class="bi bi-shield-check me-2"></i>
                        Vos données sont sécurisées et confidentielles
                    </p>
                    <p class="small">
                        <i class="bi bi-info-circle me-2"></i>
                        Pour toute question, contactez-nous au +229 XX XX XX XX
                    </p>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
</body>
</html>
