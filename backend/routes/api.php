<?php
use Illuminate\Support\Facades\Route;

Route::get('/saludo', function () {
    return response()->json([
        'mensaje' => '¡Hola mundo desde Laravel en Docker!',
        'status' => 'success'
    ]);
});