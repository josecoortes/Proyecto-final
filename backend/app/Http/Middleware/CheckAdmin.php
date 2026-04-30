<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, ...$roles)
    {
        // Si no pasamos roles por parámetro, asumimos que solo 'admin' puede entrar
        if (empty($roles)) {
            $roles = ['admin'];
        }

        // Verificamos si el usuario está autenticado y su rol está dentro de los roles permitidos
        if (auth()->check() && in_array(auth()->user()->rol, $roles)) {
            return $next($request);
        }

        // Si pillamos a alguien intentando entrar sin permiso, 403
        abort(403, 'Acceso Denegado. No tienes permisos para acceder a esta sección.');
    }
}
