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
        }
        // El cajero y el admin ven todos los pedidos (domicilio y recogida)
        
        // Mostrar todos los pedidos del día o activos
        $query->where(function($q) {
            $q->whereIn('estado', ['pendiente', 'preparando', 'listo', 'en_reparto'])
              ->orWhereDate('fecha', now()->toDateString());
        });
        // Si es 'admin', ve todo, no ponemos where.

        $pedidos = $query->orderBy('id', 'desc')->get();

        // Calcular ingresos de hoy para mostrar en el panel
        $pedidosPagadosHoy = Pedido::with('platos')
            ->whereDate('fecha', now()->toDateString())
            ->where('estado_pago', 'pagado');
            
        // Si el usuario es repartidor, solo vemos los ingresos de su tipo de pedidos
        if ($userRol === 'repartidor') {
            $pedidosPagadosHoy->where('metodo_entrega', 'domicilio');
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
            'estado' => 'required|in:pendiente,preparando,listo,en_reparto,entregado,cancelado'
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
