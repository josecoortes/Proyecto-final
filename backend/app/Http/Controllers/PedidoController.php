<?php

namespace App\Http\Controllers;

use App\Services\PedidoService;
use Illuminate\Http\Request;

class PedidoController extends Controller
{
    protected $pedidoService;

    // Inyectamos el servicio en el constructor (Mejor práctica de arquitectura)
    public function __construct(PedidoService $pedidoService)
    {
        $this->pedidoService = $pedidoService;
    }

    // 1. LISTAR PEDIDOS
    public function index()
    {
        // El controlador delega al servicio la logica de esta petición
        $pedidos = $this->pedidoService->obtenerPedidos();

        return response()->json($pedidos);
    }

    // 2. CREAR UN PEDIDO
    public function store(Request $request)
    {
        // El controlador valida la petición HTTP
        $validated = $request->validate([
            'metodo_entrega' => 'nullable|string',
            'direccion_empresa' => 'nullable|string',
            'metodo_pago' => 'nullable|string|in:efectivo,tarjeta',
            'platos' => 'required|array',
            'platos.*.id' => 'required|exists:platos,id',
            'platos.*.cantidad' => 'required|integer|min:1'
        ]);

        // Delegamos la lógica de negocio al Servicio
        $pedido = $this->pedidoService->crearPedido($validated, auth()->id());

        return response()->json([
            'mensaje' => 'Pedido creado con éxito',
            'pedido_id' => $pedido->id
        ], 201);
    }
}
