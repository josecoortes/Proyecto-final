<?php

namespace App\Services;

use App\Models\Pedido;
use Illuminate\Support\Facades\Log;

class PedidoService
{
    /**
     * Obtiene la lista de pedidos con la información formateada para la API
     */
    public function obtenerPedidos()
    {
        return Pedido::with(['user', 'platos'])->get()->map(function ($pedido) {
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
}
