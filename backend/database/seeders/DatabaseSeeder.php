<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Aquí llamamos a la lista de seeders que queremos ejecutar
        $this->call([
            PlatoSeeder::class,
        ]);
    }
}