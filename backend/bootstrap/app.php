<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Confiar en los proxies (Nginx) para que las firmas de URL funcionen con HTTPS
        $middleware->trustProxies(at: '*');

        // Redirigir usuarios no autenticados al login del panel admin (NO al login de Angular)
        $middleware->redirectGuestsTo('/admin/login');

        // Excluir rutas del panel admin del CSRF (ya protegidas por auth + is_admin middleware)
        // SameSite=Lax previene ataques CSRF cross-origin en POST de forma nativa
        $middleware->validateCsrfTokens(except: [
            'admin/pedidos/*',
            'admin/pedidos/*/estado',
            'admin/pedidos/*/pago',
            'admin/platos/*',
            'admin/empleados/*',
            'dashboard/gasto',
            'profile',
            'logout',
        ]);

        $middleware->alias([
            'is_admin' => \App\Http\Middleware\CheckAdmin::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        // Estandarización de Errores: Forzar siempre una respuesta JSON en las rutas de la API,
        // evitando que devuelvan HTML (stacktraces) cuando hay un error inesperado.
        $exceptions->shouldRenderJsonWhen(function (\Illuminate\Http\Request $request, \Throwable $e) {
            if ($request->is('api/*')) {
                return true;
            }
            return $request->expectsJson();
        });
    })->create();
