<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@marinaburguer.com'],
            [
                'name' => 'El Jefazo (Admin)',
                'password' => Hash::make('secreta123'),
                'rol' => 'admin',
            ]
        );
    }
}
