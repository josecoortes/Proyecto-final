<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PlatoController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PedidoController;
use App\Http\Controllers\ComentarioController;


// RUTAS PÚBLICAS (Sin token)
Route::post('/register', [AuthController::class, 'register']); //
Route::post('/login', [AuthController::class, 'login']);       //

Route::get('/platos', [PlatoController::class, 'index']);      // Ver la carta
Route::get('/platos/{id}', [PlatoController::class, 'show']);  // Ver un plato
Route::get('/comentarios', [ComentarioController::class, 'index']); // Ver reseñas

// RUTAS PROTEGIDAS Requieren Token Sanctum

Route::middleware('auth:sanctum')->group(function () {

    // Gestión de Platos (Crear, editar, borrar)
    Route::post('/platos', [PlatoController::class, 'store']);
    Route::put('/platos/{id}', [PlatoController::class, 'update']);
    Route::delete('/platos/{id}', [PlatoController::class, 'destroy']);

    // Gestión de Pedidos
    Route::get('/pedidos', [PedidoController::class, 'index']);
    Route::post('/pedidos', [PedidoController::class, 'store']);

    // Crear Comentarios
    Route::post('/comentarios', [ComentarioController::class, 'store']);
});
