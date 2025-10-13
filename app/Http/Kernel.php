<?php

namespace App\Http;

// ✅ ajoute bien ces lignes :
use App\Http\Middleware\CheckRole;
use Illuminate\Auth\Middleware\EnsureEmailIsVerified;
use App\Http\Middleware\Authenticate;

use Illuminate\Foundation\Http\Kernel as HttpKernel;

class Kernel extends HttpKernel
{

     public function __construct(...$args)
    {
        dd('KERNEL CHARGÉ');
        parent::__construct(...$args);
    }

    /**
     * Middleware global (exécuté pour toutes les requêtes)
     */
    protected $middleware = [
        // Vérifie que l'application est en maintenance
        \App\Http\Middleware\PreventRequestsDuringMaintenance::class,
        // Vérifie la taille de la requête
        \Illuminate\Foundation\Http\Middleware\ValidatePostSize::class,
        // Transforme les chaînes vides en null
        \App\Http\Middleware\TrimStrings::class,
        // Convertit les chaînes en null sauf pour certains champs
        \Illuminate\Foundation\Http\Middleware\ConvertEmptyStringsToNull::class,
    ];

    /**
     * Middleware par groupes (web, api)
     */
    protected $middlewareGroups = [
        'web' => [
            \App\Http\Middleware\EncryptCookies::class,
            \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
            \Illuminate\Session\Middleware\StartSession::class,
            \Illuminate\View\Middleware\ShareErrorsFromSession::class,
            \App\Http\Middleware\VerifyCsrfToken::class,
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
        ],

        'api' => [
            'throttle:api',
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
        ],
    ];

    /**
     * Middleware assignables individuellement par route
     */
    protected $routeMiddleware = [
        'auth' => \App\Http\Middleware\Authenticate::class,
        'auth.basic' => \Illuminate\Auth\Middleware\AuthenticateWithBasicAuth::class,
        'verified' => \Illuminate\Auth\Middleware\EnsureEmailIsVerified::class,
        'role' => CheckRole::class, // ✅ ton middleware est bien ici
    ];

    /**
     * Commandes artisan personnalisées
     */
    protected $commands = [
        \App\Console\Commands\CreateFirstAdmin::class,
    ];
}
