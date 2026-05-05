<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Plato;

class PlatoSeeder extends Seeder
{
    public function run(): void
    {
        $baseUrl = config('app.url') . ':8000'; // En local usamos el puerto 8000, en prod se suele configurar APP_URL ya con el puerto si es necesario.

        // Plato 1: Hamburguesa
        Plato::create([
            'nombre' => 'Hamburguesa Clásica',
            'descripcion' => 'Carne 100% vacuno con lechuga y queso cheddar.',
            'precio' => 12.50,
            'imagen' => $baseUrl . '/storage/platos/hamburguesa.png' 
        ]);

        // Plato 2: Pizza
        Plato::create([
            'nombre' => 'Pizza Margarita',
            'descripcion' => 'Tomate, mozzarella y albahaca fresca.',
            'precio' => 10.00,
            'imagen' => $baseUrl . '/storage/platos/pizza.png'
        ]);
        
        // Plato 3: Sushi
        Plato::create([
            'nombre' => 'Sushi Mix',
            'descripcion' => 'Bandeja de 12 piezas variadas.',
            'precio' => 18.90,
            'imagen' => $baseUrl . '/storage/platos/sushi.png'
        ]);
    }
}