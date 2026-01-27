<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PlatoController;

Route::get('/saludo', function () {
    return response()->json([
        'mensaje' => '¡Hola mundo desde Laravel en Docker!',
        'status' => 'success'
    ]);
});
Route::get('/platos', [PlatoController::class, 'index']);