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
        
        // No mostrar pedidos que ya están completados (pagados y entregados)
        $query->whereNot(function ($q) {
            $q->where('estado', 'entregado')
              ->where('estado_pago', 'pagado');
        });
        // Si es 'admin', ve todo, no ponemos where.

        $pedidos = $query->orderBy('id', 'desc')->get();

        // Calcular ingresos de hoy para mostrar en el panel
        $pedidosPagadosHoy = Pedido::with('platos')
            ->whereDate('fecha', now()->toDateString())
            ->where('estado_pago', 'pagado');
            
        // Si el usuario es cajero o repartidor, solo vemos los ingresos de su tipo de pedidos
        if ($userRol === 'repartidor') {
            $pedidosPagadosHoy->where('metodo_entrega', 'domicilio');
        } elseif ($userRol === 'cajero') {
            $pedidosPagadosHoy->where('metodo_entrega', 'recoger');
        }
        
        $pedidosPagadosHoy = $pedidosPagadosHoy->get();

        $ingresosHoy = $pedidosPagadosHoy->sum(function($pedido) {
            return $pedido->platos->sum(fn($plato) => $plato->precio * $plato->pivot->cantidad);
        });
        $completadosHoy = $pedidosPagadosHoy->count();

        return view('admin.pedidos.index', compact('pedidos', 'userRol', 'ingresosHoy', 'completadosHoy'));
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

        if ($request->estado === 'entregado' && $pedido->estado_pago !== 'pagado') {
            return redirect()->back()->with('error', 'No puedes marcar el pedido como entregado sin haber registrado el pago.');
        }

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
