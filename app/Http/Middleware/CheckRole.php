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
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        $user = auth()->user();

        if (!$user) {
            return redirect('login');
        }

        if (!$user->is_approved) {
            return redirect('menu')->with('error_acceso', 'Tu cuenta está pendiente de aprobación por Jefatura.');
        }

        // Si el usuario es admin, siempre tiene acceso
        if ($user->role === 'admin') {
            return $next($request);
        }

        if (!empty($roles) && !in_array($user->role, $roles)) {
            return redirect('menu')->with('error_acceso', 'No tenés permisos para ingresar a este módulo.');
        }

        return $next($request);
    }
}
