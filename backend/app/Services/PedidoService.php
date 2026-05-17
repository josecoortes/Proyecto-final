<?php

namespace App\Services;

use App\Models\Pedido;
use App\Models\Plato;
use Illuminate\Support\Facades\Log;
use Stripe\Stripe;
use Stripe\Checkout\Session;

class PedidoService
{
    /**
     * Obtiene la lista de pedidos con la información formateada para la API
     */
    public function obtenerPedidos()
    {
        return Pedido::with(['user', 'platos'])
            ->where('user_id', auth()->id()) // Solo devolvemos los pedidos del usuario autenticado
            ->orderBy('id', 'desc')
            ->get()
            ->map(function ($pedido) {
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
                        'cantidad' => $plato->pivot->cantidad,
                        'precio_unitario' => $plato->precio
                    ];
                })
            ];
        });
    }

    /**
     * Crea un nuevo pedido aplicando las reglas de negocio y guardando un registro en el log
     */
    public function crearPedido(array $datosValidados, int $userId): Pedido
    {
        // 1. Reglas de negocio
        $metodoPago = $datosValidados['metodo_pago'] ?? 'efectivo';
        $estadoPago = ($metodoPago === 'tarjeta') ? 'pagado' : 'pendiente';

        // 2. Creación del pedido
        $pedido = Pedido::create([
            'user_id' => $userId, 
            'fecha' => now()->toDateString(),
            'hora' => now()->toTimeString(),
            'metodo_entrega' => $datosValidados['metodo_entrega'] ?? 'recoger',
            'direccion_empresa' => $datosValidados['direccion_empresa'] ?? null,
            'estado' => 'pendiente',
            'metodo_pago' => $metodoPago,
            'estado_pago' => $estadoPago
        ]);

        // 3. Relación de platos (Muchos a Muchos)
        foreach ($datosValidados['platos'] as $plato) {
            $pedido->platos()->attach($plato['id'], ['cantidad' => $plato['cantidad']]);
        }

        // 4. Logging centralizado para seguridad
        Log::info("Nuevo pedido creado", [
            'pedido_id' => $pedido->id,
            'user_id' => $userId,
            'metodo_pago' => $metodoPago,
            'metodo_entrega' => $pedido->metodo_entrega
        ]);

        return $pedido;
    }

    /**
     * Crea una sesión de pago en Stripe (Checkout) y devuelve la URL para redirigir al usuario.
     */
    public function crearSesionStripe(array $datosValidados, int $userId): array
    {
        Stripe::setApiKey(env('STRIPE_SECRET'));

        // 1. Calculamos el total de forma SEGURA en el backend para que el usuario no pueda falsificar precios
        $lineItems = [];
        foreach ($datosValidados['platos'] as $platoReq) {
            $platoDB = Plato::find($platoReq['id']);
            if ($platoDB) {
                $lineItems[] = [
                    'price_data' => [
                        'currency' => 'eur',
                        'product_data' => [
                            'name' => $platoDB->nombre,
                            'images' => $platoDB->imagen ? [$platoDB->imagen] : [],
                        ],
                        // Stripe espera el importe en céntimos
                        'unit_amount' => (int) ($platoDB->precio * 100),
                    ],
                    'quantity' => $platoReq['cantidad'],
                ];
            }
        }

        // 2. Si el carrito estuviera vacío o los platos fueran inválidos, fallamos
        if (empty($lineItems)) {
            throw new \Exception('No hay platos válidos para pagar.');
        }

        // 3. Crear el pedido preliminar en estado "pendiente" en Base de datos
        // Esto permite guardar qué iba a pedir antes del pago
        $datosValidados['metodo_pago'] = 'tarjeta';
        $pedido = $this->crearPedido($datosValidados, $userId);
        
        // Lo marcamos temporalmente como pendiente de pago
        $pedido->update(['estado_pago' => 'pendiente']);

        // 4. Crear la sesión en Stripe
        $session = Session::create([
            'payment_method_types' => ['card'],
            'line_items' => $lineItems,
            'mode' => 'payment',
            // Usamos FRONTEND_URL para que Stripe devuelva al usuario a Angular y no a Laravel
            'success_url' => env('FRONTEND_URL', 'http://localhost:4200') . '/pago-exito?session_id={CHECKOUT_SESSION_ID}&pedido_id=' . $pedido->id,
            'cancel_url' => env('FRONTEND_URL', 'http://localhost:4200') . '/pago-cancelado',
            'metadata' => [
                'pedido_id' => $pedido->id,
                'user_id' => $userId
            ]
        ]);

        return [
            'id' => $session->id,
            'url' => $session->url
        ];
    }

    /**
     * Confirma que el pago ha sido exitoso comunicándose con Stripe
     */
    public function confirmarPago(string $sessionId, int $pedidoId)
    {
        Stripe::setApiKey(env('STRIPE_SECRET'));
        
        try {
            $session = Session::retrieve($sessionId);
            
            if ($session->payment_status === 'paid') {
                $pedido = Pedido::findOrFail($pedidoId);
                $pedido->update(['estado_pago' => 'pagado']);
                
                Log::info("Pago Stripe exitoso", ['pedido_id' => $pedido->id, 'session_id' => $sessionId]);
                return true;
            }
            return false;
        } catch (\Exception $e) {
            Log::error("Error validando sesión de Stripe", ['error' => $e->getMessage()]);
            return false;
        }
    }
}
