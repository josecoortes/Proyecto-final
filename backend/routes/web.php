<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Panel de Administración (Protegido por cookie de sesión + Rol Admin)
Route::middleware(['auth', 'is_admin'])->group(function () {
    
    // Dashboard resumen
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    // Gestión del perfil del admin
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    
    // CRUD para Platos desde el panel
    Route::resource('admin/platos', App\Http\Controllers\AdminPlatoController::class)->names([
        'index' => 'admin.platos.index',
        'create' => 'admin.platos.create',
        'store' => 'admin.platos.store',
        'edit' => 'admin.platos.edit',
        'update' => 'admin.platos.update',
        'destroy' => 'admin.platos.destroy',
    ]);
});

require __DIR__.'/auth.php';
