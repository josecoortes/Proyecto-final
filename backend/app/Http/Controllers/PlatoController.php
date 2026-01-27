<?php

namespace App\Http\Controllers;

use App\Models\Plato;
use Illuminate\Http\Request;

class PlatoController extends Controller
{
    // Función para listar todos los platos
    public function index()
    {
        // Pide todos los platos a la base de datos
        $platos = Plato::all();
        
        // Los devuelve en formato JSON
        return response()->json($platos);
    }
}