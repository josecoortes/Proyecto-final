<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pedido extends Model
{
    use HasFactory;

    protected $guarded = [];

    // --- RELACIONES ---

    // Un pedido pertenece a UN usuario
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Un pedido contiene muchos platos
    public function platos()
    {
        return $this->belongsToMany(Plato::class)->withPivot('cantidad');
    }
}