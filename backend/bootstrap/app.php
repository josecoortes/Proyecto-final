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
        // con una estructura estricta y profesional, para evitar que devuelvan HTML cuando hay un error inesperado.
        $exceptions->render(function (\Throwable $e, \Illuminate\Http\Request $request) {
            if ($request->is('api/*')) {
                // Si es un error de validación, devolver sus detalles y código 422
                if ($e instanceof \Illuminate\Validation\ValidationException) {
                    return response()->json([
                        'message' => 'The given data was invalid.',
                        'errors' => $e->errors()
                    ], 422);
                }

                // Extraer el código HTTP si la excepción lo tiene
                $statusCode = 500;
                if (method_exists($e, 'getStatusCode')) {
                    $statusCode = $e->getStatusCode();
                } elseif (method_exists($e, 'status')) {
                    $statusCode = $e->status();
                }
                
                $message = $e->getMessage();
                
                if ($statusCode === 500 && !config('app.debug')) {
                    $message = 'Error interno del servidor. Por favor, inténtelo de nuevo más tarde.';
                }

                return response()->json([
                    'success' => false,
                    'error' => [
                        'code' => $statusCode,
                        'message' => $message,
                        'type' => class_basename($e)
                    ]
                ], $statusCode);
            }
        });
    })->create();
