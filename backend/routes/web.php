<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

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
