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
    public function handle(Request $request, Closure $next): Response
    {
        // Verificamos si el usuario está autenticado y tiene rol de administrador
        // Nota para memoria: He tenido que crear este middleware porque si no 
        // cualquiera que se registre en la web y pusiera /login en la url de laravel 
        // podría entrar al dashboard.
        if (auth()->check() && auth()->user()->rol === 'admin') {
            return $next($request);
        }

        // Si pillamos a alguien intentando entrar sin permiso, 403
        abort(403, 'Acceso Denegado. Esta zona es exclusiva para administradores de Marina Burguer.');
    }
}
