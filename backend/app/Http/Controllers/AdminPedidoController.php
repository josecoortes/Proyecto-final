<?php

namespace App\Http\Controllers;

use App\Models\Pedido;
use Illuminate\Http\Request;

class AdminPedidoController extends Controller
{
    /**
     * Muestra la lista de todos los pedidos activos para los repartidores/cajeros.
     */
    public function index()
    {
        $query = Pedido::with(['user', 'platos']);

        // Filtrado lógico por rol
        $userRol = auth()->user()->rol;
        if ($userRol === 'repartidor') {
            // El repartidor SOLO ve pedidos a domicilio
            $query->where('metodo_entrega', 'domicilio');
        } elseif ($userRol === 'cajero') {
            // El cajero SOLO ve pedidos para recoger en local
            $query->where('metodo_entrega', 'recoger');
        }
        // Si es 'admin', ve todo, no ponemos where.

        $pedidos = $query->orderBy('fecha', 'desc')
                        ->orderBy('hora', 'desc')
                        ->get();

        return view('admin.pedidos.index', compact('pedidos', 'userRol'));
    }

    /**
     * Actualiza el estado del pedido (Pendiente -> Preparando -> En Reparto -> Entregado).
     */
    public function updateEstado(Request $request, $id)
    {
        $request->validate([
            'estado' => 'required|in:pendiente,preparando,en_reparto,entregado,cancelado'
        ]);

        $pedido = Pedido::findOrFail($id);
        $pedido->estado = $request->estado;
        $pedido->save();

        return redirect()->back()->with('success', 'Estado del pedido #'.$pedido->id.' actualizado correctamente.');
    }

    /**
     * Marca un pedido como PAGADO.
     */
    public function updatePago(Request $request, $id)
    {
        $pedido = Pedido::findOrFail($id);
        $pedido->estado_pago = 'pagado';
        $pedido->save();

        return redirect()->back()->with('success', '💸 ¡Cobro registrado! El pedido #'.$pedido->id.' ha sido marcado como PAGADO.');
    }
}
