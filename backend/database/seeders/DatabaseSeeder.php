<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Aquí llamamos a la lista de seeders que queremos ejecutar
        $this->call([
            PlatoSeeder::class,
        ]);
    }
}