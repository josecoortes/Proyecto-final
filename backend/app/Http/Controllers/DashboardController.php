<?php

namespace App\Http\Controllers;

use App\Models\Plato;
use App\Models\Pedido;
use App\Models\Gasto;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        // 1. Obtener el mes y año de la URL, por defecto el actual
        $year = $request->query('year', Carbon::now()->year);
        $month = $request->query('month', Carbon::now()->month);

        // Crear objeto Carbon para el mes actual seleccionado
        $fechaActual = Carbon::create($year, $month, 1);
        $fechaAnterior = $fechaActual->copy()->subMonth();

        $mesActual = $fechaActual->month;
        $anioActual = $fechaActual->year;
        $mesAnterior = $fechaAnterior->month;
        $anioAnterior = $fechaAnterior->year;

        // Platos
        $platosActivos = Plato::count();

        // PEDIDOS: Extraer todos con sus platos para hacer cálculos complejos
        // (En producción real con miles de pedidos se filtraría en la query, pero así vale para TFG)
        $pedidos = Pedido::with('platos')->get();
        
        // Ventas e Ingresos por Mes
        $ingresosActual = 0;
        $ingresosAnterior = 0;
        $ventasDomicilio = 0;
        $ventasRecogida = 0;
        $ingresosDomicilio = 0;
        $ingresosRecogida = 0;

        foreach ($pedidos as $p) {
            $f = Carbon::parse($p->fecha);
            $mesPedido = $f->month;
            $anioPedido = $f->year;

            // SOLO sumamos dinero si el pedido ha sido PAGADO. Contabilidad estricta.
            if ($p->estado_pago === 'pagado') {
                $ingresoPedido = $p->platos->sum(fn($plato) => $plato->precio * $plato->pivot->cantidad);

                if ($mesPedido === $mesActual && $anioPedido === $anioActual) {
                    $ingresosActual += $ingresoPedido;
                    if ($p->metodo_entrega === 'domicilio') {
                        $ventasDomicilio++;
                        $ingresosDomicilio += $ingresoPedido;
                    } else {
                        $ventasRecogida++;
                        $ingresosRecogida += $ingresoPedido;
                    }
                } elseif ($mesPedido === $mesAnterior && $anioPedido === $anioAnterior) {
                    $ingresosAnterior += $ingresoPedido;
                }
            }
        }

        // GASTOS
        $gastosActual = Gasto::whereMonth('fecha', $mesActual)->whereYear('fecha', $anioActual)->sum('monto');
        $gastosAnterior = Gasto::whereMonth('fecha', $mesAnterior)->whereYear('fecha', $anioAnterior)->sum('monto');

        // BENEFICIOS
        $beneficioActual = $ingresosActual - $gastosActual;
        $beneficioAnterior = $ingresosAnterior - $gastosAnterior;

        // Crecimiento (%) - Evitar división por cero
        $crecimiento = $beneficioAnterior > 0 
            ? (($beneficioActual - $beneficioAnterior) / $beneficioAnterior) * 100 
            : ($beneficioActual > 0 ? 100 : 0);

        // Ingresos Hoy (Solo pagados)
        $ingresosHoy = Pedido::with('platos')
            ->whereDate('fecha', Carbon::today())
            ->where('estado_pago', 'pagado')
            ->get()
            ->sum(function($pedido) {
                return $pedido->platos->sum(fn($plato) => $plato->precio * $plato->pivot->cantidad);
            });

        // Traducción de meses
        $mesesEspanol = [
            1 => 'Enero', 2 => 'Febrero', 3 => 'Marzo', 4 => 'Abril',
            5 => 'Mayo', 6 => 'Junio', 7 => 'Julio', 8 => 'Agosto',
            9 => 'Septiembre', 10 => 'Octubre', 11 => 'Noviembre', 12 => 'Diciembre'
        ];
        $nombreMesActual = $mesesEspanol[$mesActual] . ' ' . $anioActual;

        // Cálculos para la paginación (Flechas)
        $linkMesAnterior = route('dashboard', ['month' => $mesAnterior, 'year' => $anioAnterior]);
        $mesSiguiente = $fechaActual->copy()->addMonth();
        $linkMesSiguiente = route('dashboard', ['month' => $mesSiguiente->month, 'year' => $mesSiguiente->year]);

        return view('dashboard', compact(
            'platosActivos', 
            'ingresosActual', 'ingresosAnterior',
            'gastosActual', 'gastosAnterior',
            'beneficioActual', 'beneficioAnterior',
            'crecimiento',
            'ventasDomicilio', 'ventasRecogida',
            'ingresosDomicilio', 'ingresosRecogida',
            'nombreMesActual', 'linkMesAnterior', 'linkMesSiguiente',
            'ingresosHoy'
        ));
    }

    /**
     * Guarda un nuevo gasto desde el Dashboard
     */
    public function storeGasto(Request $request)
    {
        $request->validate([
            'concepto' => 'required|string|max:255',
            'monto' => 'required|numeric|min:0.01',
        ]);

        Gasto::create([
            'concepto' => $request->concepto,
            'monto' => $request->monto,
            'fecha' => now(), // Se guarda como gasto del día actual
        ]);

        return redirect()->back()->with('status', 'Gasto registrado correctamente.');
    }
}
