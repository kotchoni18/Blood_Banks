<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{

    

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string  ...$roles
     * @return \Symfony\Component\HttpFoundation\Response
     */
    
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        // Vérifier si l'utilisateur est connecté
        if (!auth()->check()) {
            return redirect()->route('home')
                ->with('error', 'Vous devez être connecté pour accéder à cette page');
        }

        $user = auth()->user();
        
        // Vérifier si le compte est actif
        if (!$user->is_active) {
            auth()->logout();
            return redirect()->route('home')
                ->with('error', 'Votre compte est désactivé. Contactez l\'administrateur.');
        }

        // Vérifier si l'utilisateur a l'un des rôles autorisés
        if (!in_array($user->role, $roles)) {
            abort(403, 'Accès refusé');
        }
        return $next($request);
    }
}