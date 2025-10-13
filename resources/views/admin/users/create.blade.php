<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Créer un Agent - Admin</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.10.0/font/bootstrap-icons.min.css" rel="stylesheet">
    <style>
        body { background: linear-gradient(135deg, #3498db 0%, #2c3e50 100%); min-height: 100vh; padding: 2rem 0; }
        .register-card { background: white; border-radius: 20px; box-shadow: 0 15px 35px rgba(0,0,0,0.1); overflow: hidden; }
        .register-header { background: linear-gradient(135deg, #3498db 0%, #2c3e50 100%); color: white; padding: 2rem; text-align: center; }
        .btn-primary { background: linear-gradient(135deg, #3498db 0%, #2c3e50 100%); border: none; border-radius: 25px; padding: 12px 30px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="register-card">
                    <div class="register-header">
                        <i class="bi bi-person-badge display-4 mb-3"></i>
                        <h3>Créer un Agent Médical</h3>
                        <p class="mb-0">Ajouter un nouveau membre de l'équipe</p>
                    </div>
                    
                    <div class="p-4">
                        @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show">
                            <i class="bi bi-exclamation-triangle-fill me-2"></i>
                            {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                        @endif

                        <form method="POST" action="{{ route('admin.users.store') }}">
                            @csrf
                            <input type="hidden" name="role" value="agent">
                            <input type="hidden" name="is_active" value="1">

                            <h6 class="text-primary mb-3">
                                <i class="bi bi-person-circle me-2"></i>Informations personnelles
                            </h6>
                            
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="first_name" class="form-label">Prénom *</label>
                                    <input type="text" class="form-control @error('first_name') is-invalid @enderror" id="first_name" name="first_name" value="{{ old('first_name') }}" required>
                                    @error('first_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="last_name" class="form-label">Nom *</label>
                                    <input type="text" class="form-control @error('last_name') is-invalid @enderror" id="last_name" name="last_name" value="{{ old('last_name') }}" required>
                                    @error('last_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="email" class="form-label">Email *</label>
                                    <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email') }}" required>
                                    @error('email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="phone" class="form-label">Téléphone</label>
                                    <input type="tel" class="form-control @error('phone') is-invalid @enderror" id="phone" name="phone" value="{{ old('phone') }}">
                                    @error('phone')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <h6 class="text-primary mb-3 mt-4">
                                <i class="bi bi-hospital me-2"></i>Informations professionnelles
                            </h6>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="employee_number" class="form-label">Numéro d'employé *</label>
                                    <input type="text" class="form-control @error('employee_number') is-invalid @enderror" id="employee_number" name="employee_number" value="{{ old('employee_number') }}" placeholder="EMP-001" required>
                                    @error('employee_number')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="department" class="form-label">Département *</label>
                                    <select class="form-select @error('department') is-invalid @enderror" id="department" name="department" required>
                                        <option value="">Sélectionner</option>
                                        <option value="Collecte" {{ old('department') == 'Collecte' ? 'selected' : '' }}>Collecte</option>
                                        <option value="Laboratoire" {{ old('department') == 'Laboratoire' ? 'selected' : '' }}>Laboratoire</option>
                                        <option value="Distribution" {{ old('department') == 'Distribution' ? 'selected' : '' }}>Distribution</option>
                                        <option value="Administration" {{ old('department') == 'Administration' ? 'selected' : '' }}>Administration</option>
                                    </select>
                                    @error('department')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="hire_date" class="form-label">Date d'embauche</label>
                                <input type="date" class="form-control @error('hire_date') is-invalid @enderror" id="hire_date" name="hire_date" value="{{ old('hire_date', date('Y-m-d')) }}">
                                @error('hire_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <h6 class="text-primary mb-3 mt-4">
                                <i class="bi bi-shield-lock me-2"></i>Mot de passe
                            </h6>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="password" class="form-label">Mot de passe *</label>
                                    <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password" required>
                                    @error('password')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="password_confirmation" class="form-label">Confirmer *</label>
                                    <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" required>
                                </div>
                            </div>

                            <div class="d-flex gap-3 mt-4">
                                <button type="submit" class="btn btn-primary flex-fill"><i class="bi bi-check-circle me-2"></i>Créer l'agent</button>
                                <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary">Annuler</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
</body>
</html>
