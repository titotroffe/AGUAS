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

        // QA-05: Jefatura puede VER (GET) cualquier módulo para monitorear,
        // pero solo puede ESCRIBIR en sus propios módulos (/jefatura, /bombas).
        if (in_array($user->role, ['admin', 'jefatura'])) {
            $isReadOnly = $request->isMethod('GET') || $request->isMethod('HEAD');
            $isOwnModule = str_starts_with($request->path(), 'jefatura')
                        || str_starts_with($request->path(), 'bombas');

            if ($isReadOnly || $isOwnModule) {
                return $next($request);
            }

            return redirect('menu')->with('error_acceso', 'Jefatura no tiene permisos de escritura en este módulo. Los registros operativos deben ser creados por el rol correspondiente.');
        }

        if (!empty($roles) && !in_array($user->role, $roles)) {
            return redirect('menu')->with('error_acceso', 'No tenés permisos para ingresar a este módulo.');
        }

        return $next($request);
    }
}
