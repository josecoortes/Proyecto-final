<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Categoria extends Model
{
    use HasFactory;

    // Aquí le decimos a Laravel: "Tranquilo, es seguro insertar el nombre"
    protected $fillable = ['nombre'];

    // Una categoría tiene muchos platos
    public function platos()
    {
        return $this->hasMany(Plato::class);
    }
}
