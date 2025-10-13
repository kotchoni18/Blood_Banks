
@extends('layouts.donor')

@section('title', 'Préférences de notification')

@section('content')
<div class="container">
    <div class="row">
        <!-- Sidebar Navigation -->
        <div class="col-lg-3 mb-4">
            <div class="card">
                <div class="card-body text-center">
                    <!-- Photo de profil -->
                    <div class="profile-avatar mb-3">
                        @if(Auth::user()->avatar)
                            <img src="{{ asset('storage/avatars/' . Auth::user()->avatar) }}" 
                                 alt="Photo de profil" 
                                 class="rounded-circle" 
                                 width="120" 
                                 height="120"
                                 style="object-fit: cover;">
                        @else
                            <div class="bg-primary rounded-circle d-flex align-items-center justify-content-center text-white mx-auto" 
                                 style="width: 120px; height: 120px; font-size: 48px;">
                                {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                            </div>
                        @endif
                    </div>
                    
                    <h5 class="mb-1">{{ Auth::user()->name }}</h5>
                    <p class="text-muted mb-0">{{ Auth::user()->email }}</p>
                </div>
            </div>

            <!-- Menu Navigation -->
            <div class="card mt-3">
                <div class="list-group list-group-flush">
                    <a href="{{ route('donor.profile.show') }}" class="list-group-item list-group-item-action">
                        <i class="bi bi-person me-2"></i>Aperçu du profil
                    </a>
                    <a href="{{ route('donor.profile.edit') }}" class="list-group-item list-group-item-action">
                        <i class="bi bi-pencil-square me-2"></i>Modifier le profil
                    </a>
                    <a href="{{ route('donor.profile.medical-history') }}" class="list-group-item list-group-item-action">
                        <i class="bi bi-heart-pulse me-2"></i>Historique médical
                    </a>
                    <a href="{{ route('donor.profile.notifications') }}" class="list-group-item list-group-item-action active">
                        <i class="bi bi-bell me-2"></i>Notifications
                    </a>
                    <a href="{{ route('donor.appointments') }}" class="list-group-item list-group-item-action">
                        <i class="bi bi-calendar-event me-2"></i>Mes rendez-vous
                    </a>
                </div>
            </div>
        </div>

        <!-- Contenu principal -->
        <div class="col-lg-9">
            <!-- Titre de la page -->
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">
                        <i class="bi bi-bell me-2"></i>Préférences de Notification
                    </h4>
                </div>
                <div class="card-body">
                    <p class="text-muted">
                        Gérez vos préférences de notification pour rester informé des campagnes de don, 
                        des rappels de rendez-vous et des actualités importantes.
                    </p>
                </div>
            </div>

            <!-- Alertes -->
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show">
                    <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if($errors->any())
                <div class="alert alert-danger alert-dismissible fade show">
                    <ul class="mb-0">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <!-- Formulaire de préférences -->
            <form action="{{ route('donor.profile.update-notifications') }}" method="POST">
                @csrf
                @method('PUT')

                <!-- Notifications par Email -->
                <div class="card mb-4">
                    <div class="card-header bg-light">
                        <h5 class="mb-0">
                            <i class="bi bi-envelope me-2"></i>Notifications par Email
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="form-check form-switch mb-3">
                            <input class="form-check-input" type="checkbox" id="email_notifications" 
                                   name="email_notifications" value="1"
                                   {{ old('email_notifications', $user->email_notifications ?? true) ? 'checked' : '' }}>
                            <label class="form-check-label" for="email_notifications">
                                <strong>Activer les notifications par email</strong>
                                <p class="text-muted mb-0 small">Recevoir des emails pour les informations importantes</p>
                            </label>
                        </div>

                        <hr>

                        <div class="ms-4">
                            <div class="form-check mb-3">
                                <input class="form-check-input" type="checkbox" id="email_appointment_reminders" 
                                       name="email_appointment_reminders" value="1"
                                       {{ old('email_appointment_reminders', $user->email_appointment_reminders ?? true) ? 'checked' : '' }}>
                                <label class="form-check-label" for="email_appointment_reminders">
                                    <strong>Rappels de rendez-vous</strong>
                                    <p class="text-muted mb-0 small">Recevoir un rappel 24h avant votre rendez-vous</p>
                                </label>
                            </div>

                            <div class="form-check mb-3">
                                <input class="form-check-input" type="checkbox" id="email_eligibility_alerts" 
                                       name="email_eligibility_alerts" value="1"
                                       {{ old('email_eligibility_alerts', $user->email_eligibility_alerts ?? true) ? 'checked' : '' }}>
                                <label class="form-check-label" for="email_eligibility_alerts">
                                    <strong>Alertes d'éligibilité</strong>
                                    <p class="text-muted mb-0 small">Être notifié quand vous pouvez à nouveau donner</p>
                                </label>
                            </div>

                            <div class="form-check mb-3">
                                <input class="form-check-input" type="checkbox" id="email_campaign_updates" 
                                       name="email_campaign_updates" value="1"
                                       {{ old('email_campaign_updates', $user->email_campaign_updates ?? true) ? 'checked' : '' }}>
                                <label class="form-check-label" for="email_campaign_updates">
                                    <strong>Nouvelles campagnes</strong>
                                    <p class="text-muted mb-0 small">Informations sur les nouvelles campagnes de don</p>
                                </label>
                            </div>

                            <div class="form-check mb-3">
                                <input class="form-check-input" type="checkbox" id="email_donation_confirmation" 
                                       name="email_donation_confirmation" value="1"
                                       {{ old('email_donation_confirmation', $user->email_donation_confirmation ?? true) ? 'checked' : '' }}>
                                <label class="form-check-label" for="email_donation_confirmation">
                                    <strong>Confirmations de don</strong>
                                    <p class="text-muted mb-0 small">Confirmation après chaque don effectué</p>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Notifications par SMS -->
                <div class="card mb-4">
                    <div class="card-header bg-light">
                        <h5 class="mb-0">
                            <i class="bi bi-phone me-2"></i>Notifications par SMS
                        </h5>
                    </div>
                    <div class="card-body">
                        @if($user->phone)
                            <div class="alert alert-info mb-3">
                                <i class="bi bi-info-circle me-2"></i>
                                SMS envoyés au : <strong>{{ $user->phone }}</strong>
                                <a href="{{ route('donor.profile.edit') }}" class="alert-link">Modifier</a>
                            </div>

                            <div class="form-check form-switch mb-3">
                                <input class="form-check-input" type="checkbox" id="sms_notifications" 
                                       name="sms_notifications" value="1"
                                       {{ old('sms_notifications', $user->sms_notifications ?? false) ? 'checked' : '' }}>
                                <label class="form-check-label" for="sms_notifications">
                                    <strong>Activer les notifications par SMS</strong>
                                    <p class="text-muted mb-0 small">Recevoir des SMS pour les informations urgentes</p>
                                </label>
                            </div>

                            <hr>

                            <div class="ms-4">
                                <div class="form-check mb-3">
                                    <input class="form-check-input" type="checkbox" id="sms_appointment_reminders" 
                                           name="sms_appointment_reminders" value="1"
                                           {{ old('sms_appointment_reminders', $user->sms_appointment_reminders ?? false) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="sms_appointment_reminders">
                                        <strong>Rappels de rendez-vous par SMS</strong>
                                        <p class="text-muted mb-0 small">SMS de rappel la veille du rendez-vous</p>
                                    </label>
                                </div>

                                <div class="form-check mb-3">
                                    <input class="form-check-input" type="checkbox" id="sms_urgent_needs" 
                                           name="sms_urgent_needs" value="1"
                                           {{ old('sms_urgent_needs', $user->sms_urgent_needs ?? false) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="sms_urgent_needs">
                                        <strong>Besoins urgents</strong>
                                        <p class="text-muted mb-0 small">Alertes SMS pour les besoins urgents de sang</p>
                                    </label>
                                </div>
                            </div>
                        @else
                            <div class="alert alert-warning">
                                <i class="bi bi-exclamation-triangle me-2"></i>
                                <strong>Numéro de téléphone manquant</strong>
                                <p class="mb-2">Vous devez ajouter un numéro de téléphone pour recevoir des notifications par SMS.</p>
                                <a href="{{ route('donor.profile.edit') }}" class="btn btn-warning btn-sm">
                                    <i class="bi bi-plus-circle me-1"></i>Ajouter un numéro
                                </a>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Notifications Newsletter -->
                <div class="card mb-4">
                    <div class="card-header bg-light">
                        <h5 class="mb-0">
                            <i class="bi bi-newspaper me-2"></i>Newsletter et Actualités
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="form-check mb-3">
                            <input class="form-check-input" type="checkbox" id="newsletter_subscription" 
                                   name="newsletter_subscription" value="1"
                                   {{ old('newsletter_subscription', $user->newsletter_subscription ?? true) ? 'checked' : '' }}>
                            <label class="form-check-label" for="newsletter_subscription">
                                <strong>Newsletter mensuelle</strong>
                                <p class="text-muted mb-0 small">Recevoir la newsletter avec les actualités du don de sang</p>
                            </label>
                        </div>

                        <div class="form-check mb-3">
                            <input class="form-check-input" type="checkbox" id="health_tips" 
                                   name="health_tips" value="1"
                                   {{ old('health_tips', $user->health_tips ?? true) ? 'checked' : '' }}>
                            <label class="form-check-label" for="health_tips">
                                <strong>Conseils santé</strong>
                                <p class="text-muted mb-0 small">Conseils et recommandations pour les donneurs de sang</p>
                            </label>
                        </div>

                        <div class="form-check mb-3">
                            <input class="form-check-input" type="checkbox" id="event_invitations" 
                                   name="event_invitations" value="1"
                                   {{ old('event_invitations', $user->event_invitations ?? true) ? 'checked' : '' }}>
                            <label class="form-check-label" for="event_invitations">
                                <strong>Invitations aux événements</strong>
                                <p class="text-muted mb-0 small">Être invité aux événements spéciaux pour les donneurs</p>
                            </label>
                        </div>
                    </div>
                </div>

                <!-- Fréquence des notifications -->
                <div class="card mb-4">
                    <div class="card-header bg-light">
                        <h5 class="mb-0">
                            <i class="bi bi-clock me-2"></i>Fréquence des Notifications
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label for="notification_frequency" class="form-label">
                                <strong>Fréquence des emails groupés</strong>
                            </label>
                            <select class="form-select" id="notification_frequency" name="notification_frequency">
                                <option value="instant" {{ old('notification_frequency', $user->notification_frequency ?? 'instant') == 'instant' ? 'selected' : '' }}>
                                    Instantané - Recevoir immédiatement
                                </option>
                                <option value="daily" {{ old('notification_frequency', $user->notification_frequency ?? 'instant') == 'daily' ? 'selected' : '' }}>
                                    Quotidien - Un résumé par jour
                                </option>
                                <option value="weekly" {{ old('notification_frequency', $user->notification_frequency ?? 'instant') == 'weekly' ? 'selected' : '' }}>
                                    Hebdomadaire - Un résumé par semaine
                                </option>
                            </select>
                            <div class="form-text">
                                Choisissez à quelle fréquence vous souhaitez recevoir les notifications non urgentes
                            </div>
                        </div>

                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="do_not_disturb" 
                                   name="do_not_disturb" value="1"
                                   {{ old('do_not_disturb', $user->do_not_disturb ?? false) ? 'checked' : '' }}>
                            <label class="form-check-label" for="do_not_disturb">
                                <strong>Mode "Ne pas déranger" (22h - 8h)</strong>
                                <p class="text-muted mb-0 small">Ne pas recevoir de notifications pendant ces heures (sauf urgences)</p>
                            </label>
                        </div>
                    </div>
                </div>

                <!-- Actions rapides -->
                <div class="card mb-4">
                    <div class="card-header bg-light">
                        <h5 class="mb-0">
                            <i class="bi bi-lightning me-2"></i>Actions Rapides
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 mb-2">
                                <button type="button" class="btn btn-outline-primary w-100" onclick="enableAll()">
                                    <i class="bi bi-check-all me-1"></i>Tout activer
                                </button>
                            </div>
                            <div class="col-md-6 mb-2">
                                <button type="button" class="btn btn-outline-secondary w-100" onclick="disableAll()">
                                    <i class="bi bi-x-circle me-1"></i>Tout désactiver
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Boutons d'action -->
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex gap-3">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="bi bi-check2 me-2"></i>Enregistrer les préférences
                            </button>
                            <a href="{{ route('donor.profile.show') }}" class="btn btn-outline-secondary btn-lg">
                                <i class="bi bi-arrow-left me-2"></i>Retour au profil
                            </a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// Activer toutes les notifications
function enableAll() {
    document.querySelectorAll('input[type="checkbox"]').forEach(checkbox => {
        checkbox.checked = true;
    });
}

// Désactiver toutes les notifications
function disableAll() {
    document.querySelectorAll('input[type="checkbox"]').forEach(checkbox => {
        checkbox.checked = false;
    });
}

// Gérer les dépendances entre les notifications
document.getElementById('email_notifications')?.addEventListener('change', function() {
    const emailCheckboxes = document.querySelectorAll('[id^="email_"]');
    emailCheckboxes.forEach(checkbox => {
        if (checkbox.id !== 'email_notifications') {
            checkbox.disabled = !this.checked;
            if (!this.checked) {
                checkbox.checked = false;
            }
        }
    });
});

document.getElementById('sms_notifications')?.addEventListener('change', function() {
    const smsCheckboxes = document.querySelectorAll('[id^="sms_"]');
    smsCheckboxes.forEach(checkbox => {
        if (checkbox.id !== 'sms_notifications') {
            checkbox.disabled = !this.checked;
            if (!this.checked) {
                checkbox.checked = false;
            }
        }
    });
});

// Initialiser l'état au chargement de la page
document.addEventListener('DOMContentLoaded', function() {
    const emailMain = document.getElementById('email_notifications');
    const smsMain = document.getElementById('sms_notifications');
    
    if (emailMain) {
        emailMain.dispatchEvent(new Event('change'));
    }
    if (smsMain) {
        smsMain.dispatchEvent(new Event('change'));
    }
});
</script>

<style>
.profile-avatar img, .profile-avatar div {
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
}

.card:hover {
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    transition: box-shadow 0.3s ease;
}

.list-group-item-action.active {
    background-color: #0d6efd;
    border-color: #0d6efd;
}

.form-check-input:checked {
    background-color: #0d6efd;
    border-color: #0d6efd;
}

.form-check-input:focus {
    border-color: #0d6efd;
    box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.25);
}

.form-switch .form-check-input {
    width: 3em;
    height: 1.5em;
}
</style>
@endsection