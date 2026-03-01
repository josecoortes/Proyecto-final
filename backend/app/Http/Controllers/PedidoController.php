<?php

namespace App\Http\Controllers;

use App\Models\Pedido;
use Illuminate\Http\Request;

class PedidoController extends Controller
{
    // 1. LISTAR PEDIDOS
    public function index()
    {
        $pedidos = Pedido::with(['user', 'platos'])->get()->map(function ($pedido) {
            return [
                'id' => $pedido->id,
                'fecha' => $pedido->fecha,
                'hora' => $pedido->hora,
                'estado' => $pedido->estado,
                'metodo_entrega' => $pedido->metodo_entrega,
                'direccion' => $pedido->direccion_empresa,
                'cliente' => $pedido->user->name, // Ocultamos el email y la contraseña
                'platos' => $pedido->platos->map(function ($plato) {
                    return [
                        'nombre' => $plato->nombre,
                        'cantidad' => $plato->pivot->cantidad, // Sacamos la cantidad de la tabla intermedia
                        'precio_unitario' => $plato->precio
                    ];
                })
            ];
        });

        return response()->json($pedidos);
    }

    // 2. CREAR UN PEDIDO
    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'metodo_entrega' => 'nullable|string',
            'direccion_empresa' => 'nullable|string',
            'platos' => 'required|array',
            'platos.*.id' => 'required|exists:platos,id',
            'platos.*.cantidad' => 'required|integer|min:1'
        ]);

        $pedido = Pedido::create([
            'user_id' => $validated['user_id'],
            'fecha' => now()->toDateString(),
            'hora' => now()->toTimeString(),
            'metodo_entrega' => $validated['metodo_entrega'] ?? 'recoger',
            'direccion_empresa' => $validated['direccion_empresa'] ?? null,
            'estado' => 'pendiente'
        ]);

        foreach ($validated['platos'] as $plato) {
            $pedido->platos()->attach($plato['id'], ['cantidad' => $plato['cantidad']]);
        }

        return response()->json([
            'mensaje' => 'Pedido creado con éxito',
            'pedido_id' => $pedido->id // Devolvemos solo el ID para no sobrecargar
        ], 201);
    }
}
