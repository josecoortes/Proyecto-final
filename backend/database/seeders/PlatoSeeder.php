<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Plato;

class PlatoSeeder extends Seeder
{
    public function run(): void
    {
        // En vez de usar imágenes locales que pueden fallar, usamos imágenes reales en alta calidad de Unsplash.

        Plato::create([
            'nombre' => 'Marina Clásica',
            'descripcion' => 'Carne 100% vacuno de 200g con lechuga fresca, tomate y nuestro queso cheddar fundido.',
            'precio' => 12.50,
            'imagen' => 'https://images.unsplash.com/photo-1568901346375-23c9450c58cd?auto=format&fit=crop&w=800&q=80' 
        ]);

        Plato::create([
            'nombre' => 'Doble Smash Bacon',
            'descripcion' => 'Doble carne smash, cuádruple bacon crujiente y salsa BBQ ahumada.',
            'precio' => 14.90,
            'imagen' => 'https://images.pexels.com/photos/1639557/pexels-photo-1639557.jpeg?auto=compress&cs=tinysrgb&w=800'
        ]);
        
        Plato::create([
            'nombre' => 'Pollo Crujiente Supreme',
            'descripcion' => 'Contramuslo de pollo frito estilo Kentucky con ensalada coleslaw y mayonesa trufada.',
            'precio' => 11.90,
            'imagen' => 'https://images.unsplash.com/photo-1615719413546-198b25453f85?auto=format&fit=crop&w=800&q=80'
        ]);

        Plato::create([
            'nombre' => 'Veggie Beyond Burger',
            'descripcion' => 'Hamburguesa 100% vegetal con pan brioche vegano, aguacate y cebolla caramelizada.',
            'precio' => 13.50,
            'imagen' => 'https://images.unsplash.com/photo-1585238342024-78d387f4a707?auto=format&fit=crop&w=800&q=80'
        ]);

        Plato::create([
            'nombre' => 'Patatas Trufadas con Parmesano',
            'descripcion' => 'Ración grande de patatas fritas bañadas en aceite de trufa blanca y queso parmesano rallado.',
            'precio' => 5.50,
            'imagen' => 'https://images.pexels.com/photos/115740/pexels-photo-115740.jpeg?auto=compress&cs=tinysrgb&w=800'
        ]);

        Plato::create([
            'nombre' => 'Aros de Cebolla a la Cerveza',
            'descripcion' => 'Crujientes aros de cebolla rebozados en masa de cerveza artesanal. Incluye salsa cheddar.',
            'precio' => 4.90,
            'imagen' => 'https://images.unsplash.com/photo-1639024471283-03518883512d?auto=format&fit=crop&w=800&q=80'
        ]);

        Plato::create([
            'nombre' => 'Batido de Oreo Volcánico',
            'descripcion' => 'Helado de vainilla batido con galletas Oreo enteras, nata montada y sirope de chocolate.',
            'precio' => 6.00,
            'imagen' => 'https://images.pexels.com/photos/3727250/pexels-photo-3727250.jpeg?auto=compress&cs=tinysrgb&w=800'
        ]);

        Plato::create([
            'nombre' => 'Tarta de Queso Marina',
            'descripcion' => 'Nuestra famosa tarta de queso horneada, muy cremosa por dentro, con coulis de frutos rojos.',
            'precio' => 6.50,
            'imagen' => 'https://images.unsplash.com/photo-1533134242443-d4fd215305ad?auto=format&fit=crop&w=800&q=80'
        ]);
    }
}