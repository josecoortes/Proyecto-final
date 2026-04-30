<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use App\Models\Gasto;
use App\Models\Pedido;
use App\Models\Plato;
use Carbon\Carbon;

class DashboardSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Crear Gastos del mes actual y el pasado
        $gastos = [
            ['concepto' => 'Factura Luz', 'monto' => 350.00, 'fecha' => Carbon::now()->subDays(15)],
            ['concepto' => 'Carne Proveedor Local', 'monto' => 850.50, 'fecha' => Carbon::now()->subDays(5)],
            ['concepto' => 'Alquiler Local', 'monto' => 1200.00, 'fecha' => Carbon::now()->startOfMonth()],
            ['concepto' => 'Factura Agua', 'monto' => 80.00, 'fecha' => Carbon::now()->subMonths(1)->addDays(10)],
            ['concepto' => 'Publicidad Instagram', 'monto' => 150.00, 'fecha' => Carbon::now()->subMonths(1)->addDays(20)],
            ['concepto' => 'Panadería Artesanal', 'monto' => 420.00, 'fecha' => Carbon::now()->subMonths(1)->startOfMonth()],
        ];

        foreach ($gastos as $g) {
            Gasto::create($g);
        }

        // 2. Crear Pedidos Pasados (Asumimos que el usuario ID 1 existe, si no, creamos uno básico o usamos el 1 si hay)
        // Buscamos platos para asociarlos a los pedidos
        $platos = Plato::all();
        if ($platos->isEmpty()) {
            return; // Si no hay platos no podemos simular ventas reales
        }

        // Simular 30 pedidos en los últimos 2 meses
        for ($i = 0; $i < 30; $i++) {
            // Fecha aleatoria entre hace 60 días y hoy
            $fechaRandom = Carbon::now()->subDays(rand(1, 60));
            $metodo = rand(0, 1) ? 'domicilio' : 'recoger';

            $pedido = Pedido::create([
                'user_id' => 1, // Usuario por defecto (Admin u otro)
                'fecha' => $fechaRandom->toDateString(),
                'hora' => $fechaRandom->toTimeString(),
                'metodo_entrega' => $metodo,
                'direccion_empresa' => $metodo === 'domicilio' ? 'Calle Simulada ' . rand(1, 100) : null,
                'estado' => 'completado',
                'created_at' => $fechaRandom,
                'updated_at' => $fechaRandom,
            ]);

            // Añadir de 1 a 3 platos aleatorios a cada pedido
            $cantidadPlatos = rand(1, 3);
            for ($j = 0; $j < $cantidadPlatos; $j++) {
                $platoRandom = $platos->random();
                $pedido->platos()->attach($platoRandom->id, ['cantidad' => rand(1, 4)]);
            }
        }
    }
}
