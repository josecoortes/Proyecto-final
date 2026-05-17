<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;

// ─── LOGIN EXCLUSIVO DEL PANEL DE ADMINISTRACIÓN ───────────────────────────
// Página de login para el panel Blade (independiente del login de Angular)
Route::get('/admin/login', function (Request $request) {
    // AUTO-LOGIN: Si Angular nos manda un token cifrado, lo procesamos automáticamente
    if ($request->has('auto')) {
        try {
            $payload = \Illuminate\Support\Facades\Crypt::decryptString($request->auto);
            [$userId, $expiry] = explode('|', $payload);

            // Verificar que el token no ha caducado (5 minutos)
            if (now()->timestamp > (int)$expiry) {
                return redirect('/admin/login')->withErrors(['email' => 'El enlace de acceso ha caducado. Inicia sesión de nuevo.']);
            }

            $user = \App\Models\User::findOrFail($userId);
            \Illuminate\Support\Facades\Auth::login($user, true);
            $request->session()->regenerate();

            if ($user->rol === 'empleado') {
                return redirect()->route('admin.platos.index');
            } elseif ($user->rol === 'repartidor' || $user->rol === 'cajero') {
                return redirect()->route('admin.pedidos.index');
            }
            return redirect()->route('dashboard');

        } catch (\Exception $e) {
            return redirect('/admin/login')->withErrors(['email' => 'Token de acceso inválido. Por favor, inicia sesión manualmente.']);
        }
    }

    // LOGIN MANUAL: Si ya está autenticado, redirige al panel
    if (auth()->check()) {
        return redirect('/dashboard');
    }

    // Mostrar el formulario de login
    return view('admin.admin-login');
})->name('admin.login.form');

Route::post('/admin/login', function (Request $request) {
    $credentials = $request->only('email', 'password');

    if (\Illuminate\Support\Facades\Auth::attempt($credentials, true)) {
        $request->session()->regenerate();
        $user = auth()->user();

        if ($user->rol === 'empleado') {
            return redirect()->route('admin.platos.index');
        } elseif ($user->rol === 'repartidor' || $user->rol === 'cajero') {
            return redirect()->route('admin.pedidos.index');
        }
        return redirect()->route('dashboard');
    }

    return back()->withErrors(['email' => 'Credenciales incorrectas. Inténtalo de nuevo.']);
})->middleware(['throttle:10,1']);

Route::get('/', function () {
    return redirect(env('FRONTEND_URL', 'http://localhost:4200'));
});

// Ruta mágica para auto-login desde Angular
Route::get('/magic-login/{user}', function (\Illuminate\Http\Request $request, \App\Models\User $user) {
    if (! $request->hasValidSignature()) {
        abort(401, 'El enlace mágico ha caducado o no es válido.');
    }

    \Illuminate\Support\Facades\Auth::login($user);
    $rol = $user->rol;

    if ($rol === 'empleado') {
        return redirect(route('admin.platos.index'));
    } elseif ($rol === 'repartidor' || $rol === 'cajero') {
        return redirect(route('admin.pedidos.index'));
    }

    return redirect(route('dashboard'));
})->name('admin.magic_login')->middleware('web');

// Panel de Administración Base (Cualquier usuario logueado en web puede entrar, luego el middleware los expulsa)
Route::middleware(['auth'])->group(function () {
    
    // Gestión del perfil del admin
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    
    // -------------------------------------------------------------
    // ZONA FINANCIERA: Solo accesible para Admin y Gestor
    // -------------------------------------------------------------
    Route::middleware(['is_admin:admin,gestor'])->group(function () {
        Route::get('/dashboard', [\App\Http\Controllers\DashboardController::class, 'index'])->name('dashboard');
        Route::post('/dashboard/gasto', [\App\Http\Controllers\DashboardController::class, 'storeGasto'])->name('dashboard.gasto.store');
    });

    // -------------------------------------------------------------
    // ZONA DE COCINA: Solo accesible para Admin y Empleado
    // -------------------------------------------------------------
    Route::middleware(['is_admin:admin,empleado'])->group(function () {
        Route::resource('admin/platos', App\Http\Controllers\AdminPlatoController::class)->names([
            'index' => 'admin.platos.index',
            'create' => 'admin.platos.create',
            'store' => 'admin.platos.store',
            'edit' => 'admin.platos.edit',
            'update' => 'admin.platos.update',
            'destroy' => 'admin.platos.destroy',
        ]);
    });

    // -------------------------------------------------------------
    // ZONA DE LOGÍSTICA: Solo accesible para Admin, Repartidor y Cajero
    // -------------------------------------------------------------
    Route::middleware(['is_admin:admin,repartidor,cajero'])->group(function () {
        Route::get('admin/pedidos', [\App\Http\Controllers\AdminPedidoController::class, 'index'])->name('admin.pedidos.index');
        Route::patch('admin/pedidos/{id}/estado', [\App\Http\Controllers\AdminPedidoController::class, 'updateEstado'])->name('admin.pedidos.estado');
        Route::patch('admin/pedidos/{id}/pago', [\App\Http\Controllers\AdminPedidoController::class, 'updatePago'])->name('admin.pedidos.pago');
    });

    // -------------------------------------------------------------
    // ZONA DE USUARIOS: Solo accesible para Admin supremo
    // -------------------------------------------------------------
    Route::middleware(['is_admin:admin'])->group(function () {
        Route::resource('admin/empleados', \App\Http\Controllers\UserController::class)->names([
            'index' => 'admin.empleados.index',
            'create' => 'admin.empleados.create',
            'store' => 'admin.empleados.store',
            'edit' => 'admin.empleados.edit',
            'update' => 'admin.empleados.update',
            'destroy' => 'admin.empleados.destroy',
        ]);
    });
});

require __DIR__.'/auth.php';

//Redirige cualquier ruta no encontrada a un lugar seguro según el rol
Route::fallback(function () {
    if (auth()->check()) {
        $user = auth()->user();
        if ($user->rol === 'admin' || $user->rol === 'gestor') {
            return redirect()->route('dashboard');
        } elseif ($user->rol === 'repartidor' || $user->rol === 'cajero') {
            return redirect()->route('admin.pedidos.index');
        } elseif ($user->rol === 'empleado') {
            return redirect()->route('admin.platos.index');
        }
    }
    
    // Si no está logueado o es un cliente, lo mandamos al inicio (Angular)
    return redirect(env('FRONTEND_URL', 'http://localhost:4200'));
});
