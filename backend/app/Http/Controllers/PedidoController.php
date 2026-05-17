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

    // 3. CREAR SESIÓN STRIPE
    public function crearSesionStripe(Request $request)
    {
        $validated = $request->validate([
            'metodo_entrega' => 'nullable|string',
            'direccion_empresa' => 'nullable|string',
            'platos' => 'required|array',
            'platos.*.id' => 'required|exists:platos,id',
            'platos.*.cantidad' => 'required|integer|min:1'
        ]);

        try {
            $session = $this->pedidoService->crearSesionStripe($validated, auth()->id());
            
            return response()->json([
                'url' => $session['url']
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error al crear la sesión de pago: ' . $e->getMessage()], 500);
        }
    }

    // 4. CONFIRMAR PAGO STRIPE
    public function confirmarPagoStripe(Request $request)
    {
        $validated = $request->validate([
            'session_id' => 'required|string',
            'pedido_id' => 'required|integer|exists:pedidos,id'
        ]);

        $confirmado = $this->pedidoService->confirmarPago($validated['session_id'], $validated['pedido_id']);

        if ($confirmado) {
            return response()->json(['mensaje' => 'Pago confirmado con éxito.']);
        }

        return response()->json(['error' => 'No se pudo verificar el pago.'], 400);
    }
}
