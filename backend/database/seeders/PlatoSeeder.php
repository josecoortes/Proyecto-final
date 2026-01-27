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
            // Foto de Wikimedia Commons (Fiable)
            'imagen' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/4/47/Hamburger_%28black_bg%29.jpg/640px-Hamburger_%28black_bg%29.jpg' 
        ]);

        // Plato 2: Pizza
        Plato::create([
            'nombre' => 'Pizza Margarita',
            'descripcion' => 'Tomate, mozzarella y albahaca fresca.',
            'precio' => 10.00,
            'imagen' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/a/a3/Eq_it-na_pizza-margherita_sep2005_sml.jpg/640px-Eq_it-na_pizza-margherita_sep2005_sml.jpg'
        ]);
        
        // Plato 3: Sushi
        Plato::create([
            'nombre' => 'Sushi Mix',
            'descripcion' => 'Bandeja de 12 piezas variadas.',
            'precio' => 18.90,
            'imagen' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/6/60/Sushi_platter.jpg/640px-Sushi_platter.jpg'
        ]);
    }
}