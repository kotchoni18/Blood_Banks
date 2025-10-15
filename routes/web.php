<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AgentController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\DonorController;
use App\Http\Controllers\DonationController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\StockController;
use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\AdminNotificationController;
use App\Http\Controllers\UserNotificationController;

// Page d'accueil - Redirection vers login
/* Route::get('/', function () {
    return redirect()->route('home');
}); */

// Page d'accueil : Formulaire de connexion général
Route::get('/', [AuthController::class, 'showLogin'])->name('home');
Route::post('/login', [AuthController::class, 'logins'])->name('logins');

// Routes d'authentification principales
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Auth Admin
Route::get('/admin/login', [AuthController::class, 'adminLogin'])->name('getadmin.login');
Route::post('/admin/login', [AdminController::class, 'login'])->name('admin.login');

//Auth agent
Route::get('/agent/login', [AuthController::class, 'agentLogin'])->name('getagent.login');
Route::post('/agent/login', [AgentController::class, 'login'])->name('agent.login');

//Auth Doneur
Route::get('/donor/login', [AuthController::class, 'donorLogin'])->name('getdonor.login');
Route::post('/donor/login', [DonorController::class, 'login'])->name('donor.login');



Route::middleware(['auth', 'role:admin'])->group(function () {
    // Formulaires d'inscription
     Route::get('/register-agent', [AuthController::class, 'showAgentRegister'])->name('agent.register');
     Route::get('/register-admin', [AuthController::class, 'showAdminRegister'])->name('admin.register');
    
     // Traitement des inscriptions
     Route::post('/register-agent', [AuthController::class, 'registerAgent'])->name('agent.register.post');
     Route::post('/register-admin', [AuthController::class, 'registerAdmin'])->name('admin.register.post');
});


// Inscription 
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
// Traitement de l'inscription donneur (public)
Route::post('/register', [AuthController::class, 'register'])->name('register.post');

/*
|--------------------------------------------------------------------------
| Routes ADMIN
|--------------------------------------------------------------------------
*/
Route::prefix('admin')->name('admin.')->middleware(['web', 'auth', 'role:admin'])->group(function () {

    // Dashboard principal
    Route::get('/', [AdminController::class, 'dashboard'])->name('dashboard');
    // Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard.full');

    // API pour les graphiques et statistiques
    Route::get('/chart-data', [AdminController::class, 'getChartData'])->name('chart-data');
    Route::get('/stats', [AdminController::class, 'getStats'])->name('stats');

    // Gestion des utilisateurs
     Route::get('/user', [UserController::class, 'index'])->name('users.index');
     Route::get('/users/create', [UserController::class, 'create'])->name('users.create');
     Route::post('/admin/users', [UserController::class, 'store'])->name('users.store');
     Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('users.destroy');
     Route::patch('users/{user}/toggle-status', [UserController::class, 'toggleStatus'])->name('users.toggle-status');
     Route::get('users/{user}/donations', [UserController::class, 'userDonations'])->name('users.donations');

    // Gestion des stocks
    Route::get('stocks/index', [StockController::class, 'index'])->name('stocks.index');
    Route::get('stocks/critical', [StockController::class, 'critical'])->name('stocks.critical');
    Route::get('stocks/expiring', [StockController::class, 'expiring'])->name('stocks.expiring');
    Route::post('stocks/bulk-update', [StockController::class, 'bulkUpdate'])->name('stocks.bulk-update');

    // Rapports et exports
     Route::get('reports/users', [AdminController::class, 'usersReport'])->name('reports.users');
     Route::get('reports/donations', [AdminController::class, 'donationsReport'])->name('reports.donations');
     Route::get('reports/stocks', [AdminController::class, 'stocksReport'])->name('reports.stocks');

     Route::get('notifications/critical-stocks', [AdminNotificationController::class, 'criticalStocks'])->name('notifications.criticalStocks');
    Route::post('notifications/notify-group', [AdminNotificationController::class, 'notifyGroup'])->name('notifications.notifyGroup');
});


/*
|--------------------------------------------------------------------------
| Routes AGENT MÉDICAL
|--------------------------------------------------------------------------
*/
Route::prefix('agent')->name('agent.')->middleware(['auth', 'role:agent'])->group(function () {
        
     Route::get('/', [AgentController::class, 'dashboard'])->name('dashboard');
    

    //STOCKS
    Route::get('/agent/stocks', [AgentController::class, 'stocks'])->name('stocks.index');
    Route::get('/stock-data', [AgentController::class, 'getStockData'])->name('stock-data');
    Route::get('/agent-stats', [AgentController::class, 'getAgentStats'])->name('agent-stats');

    //HISTORIQUE
    Route::get('/agent/donations/history', [AgentController::class, 'history'])->name('donations.history');

    //FORMULAIRE ENREGISTREMENT DON (PAGE)
    Route::get('/donations/create', [AgentController::class, 'createDonation'])->name('donations.create');

    //TRAITEMENT DU FORMULAIRE (IMPORTANT)
     //Route::post('/donations', [AgentController::class, 'storeDonation'])->name('donations.store');

    //AUTRES FONCTIONNALITÉS (Depuis DonationController)
    Route::get('/donations', [DonationController::class, 'index'])->name('donations.index');
    Route::resource('donations', DonationController::class);
    Route::put('donations/{donation}', [DonationController::class, 'update'])->name('donations.update');
    Route::get('donations/today', [DonationController::class, 'todayDonations'])->name('donations.today');
     //Route::get('donations/{donation}/receipt', [DonationController::class, 'receipt'])->name('donations.receipt');
    Route::post('donations/{donation}/validateDonation', [DonationController::class, 'validateDonation'])->name('donations.validate');

     

     // Route::get('eligibility-check', [AgentController::class, 'eligibilityCheck'])->name('eligibility-check');
     // Route::post('quick-donor-search', [AgentController::class, 'quickDonorSearch'])->name('quick-donor-search');
});

/*
|--------------------------------------------------------------------------
| Routes DONNEUR
|--------------------------------------------------------------------------

*/
Route::prefix('donor')->name('donor.')->group(function () {
    Route::get('/profile', [ProfileController::class, 'show'])->name('profile.show');


    Route::get('/', [DonorController::class, 'dashboard'])->name('dashboard');

    
    // Historique des dons
    Route::get('/donations', [DonorController::class, 'donations'])->name('donations.index');
     //Route::get('/donations', [DonorController::class, 'donations'])->name('donations');
    Route::get('/donations/{donation}', [DonorController::class, 'showDonation'])
         ->name('donations.show');
    
    // Rendez-vous
    Route::get('/appointments', [DonorController::class, 'appointments'])->name('appointments');
    Route::post('/appointments', [AppointmentController::class, 'store'])->name('appointments.store');
     Route::delete('/appointments/{appointment}', [AppointmentController::class, 'cancel'])
          ->name('appointments.cancel');
     Route::put('/appointments/{appointment}', [AppointmentController::class, 'reschedule'])
          ->name('appointments.reschedule');
    
    // Campagnes de don
    Route::get('/campaigns', [DonorController::class, 'campaigns'])->name('campaigns');
    Route::get('/campaigns/{campaign}', [DonorController::class, 'showCampaign'])
         ->name('campaigns.show');
    Route::post('/campaigns/{campaign}/join', [DonorController::class, 'joinCampaign'])
         ->name('campaigns.join');
    
      Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
      Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
      Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.update-password');
      Route::put('/profile/avatar', [ProfileController::class, 'updateAvatar'])->name('profile.update-avatar');
    // Suppression de compte
     Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    
    // Notifications et préférences
    Route::get('/notifications', [UserNotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/{id}/read', [UserNotificationController::class, 'markAsRead'])->name('notifications.read');
     //Route::get('/notifications', [DonorController::class, 'notifications'])->name('profile.notifications');
     //Route::post('/notifications/mark-read', [DonorController::class, 'markNotificationsRead'])
     // ->name('notifications.mark-read');
});
