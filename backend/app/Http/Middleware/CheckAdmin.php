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

        // Si el usuario está autenticado pero no tiene el rol correcto, lo redirigimos al lugar adecuado
        if (auth()->check()) {
            $rol = auth()->user()->rol;

            // Si es un rol de staff pero está intentando acceder a un área que no le corresponde,
            // lo mandamos a su zona correcta en vez de mostrar un error
            if ($rol === 'empleado') {
                return redirect()->route('admin.platos.index');
            } elseif ($rol === 'repartidor' || $rol === 'cajero') {
                return redirect()->route('admin.pedidos.index');
            }

            // Si es un cliente normal o un rol desconocido, lo mandamos a la tienda
            return redirect(config('app.frontend_url', '/'));
        }

        // Si no está autenticado en absoluto, mandamos al login
        return redirect()->route('login');
    }
}
