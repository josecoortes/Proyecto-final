<?php

namespace App\Http\Controllers;

use App\Models\Comentario;
use Illuminate\Http\Request;

class ComentarioController extends Controller
{
    // 1. LISTAR COMENTARIOS
    public function index()
    {
        $comentarios = Comentario::with(['user', 'plato'])->get()->map(function ($comentario) {
            return [
                'id' => $comentario->id,
                'plato' => $comentario->plato->nombre, // Solo el nombre del plato
                'autor' => $comentario->user->name,   // Solo el nombre del usuario
                'valoracion' => $comentario->valoracion,
                'texto' => $comentario->contenido,
                'fecha' => $comentario->fecha
            ];
        });

        return response()->json($comentarios);
    }

    // 2. CREAR UN COMENTARIO
    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'plato_id' => 'required|exists:platos,id',
            'contenido' => 'required|string',
            'valoracion' => 'required|integer|min:1|max:5',
        ]);

        $comentario = Comentario::create([
            'user_id' => $validated['user_id'],
            'plato_id' => $validated['plato_id'],
            'contenido' => $validated['contenido'],
            'valoracion' => $validated['valoracion'],
            'fecha' => now()->toDateString()
        ]);

        return response()->json([
            'mensaje' => 'Comentario añadido correctamente',
            'comentario_id' => $comentario->id
        ], 201);
    }
}
