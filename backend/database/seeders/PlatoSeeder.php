<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Plato;

class PlatoSeeder extends Seeder
{
    public function run(): void
    {
        // Plato 1: Hamburguesa
        Plato::create([
            'nombre' => 'Hamburguesa Clásica',
            'descripcion' => 'Carne 100% vacuno con lechuga y queso cheddar.',
            'precio' => 12.50,
            'imagen' => 'http://localhost:8000/storage/platos/hamburguesa.png' 
        ]);

        // Plato 2: Pizza
        Plato::create([
            'nombre' => 'Pizza Margarita',
            'descripcion' => 'Tomate, mozzarella y albahaca fresca.',
            'precio' => 10.00,
            'imagen' => 'http://localhost:8000/storage/platos/pizza.png'
        ]);
        
        // Plato 3: Sushi
        Plato::create([
            'nombre' => 'Sushi Mix',
            'descripcion' => 'Bandeja de 12 piezas variadas.',
            'precio' => 18.90,
            'imagen' => 'http://localhost:8000/storage/platos/sushi.png'
        ]);
    }
}