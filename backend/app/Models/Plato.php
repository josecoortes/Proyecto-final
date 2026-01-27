<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Plato extends Model
{
    use HasFactory;

    // Esto permite guardar cualquier dato sin restricciones (útil para desarrollo)
    protected $guarded = [];

    // --- RELACIONES ---

    // Un plato puede estar en muchos pedidos
    // Usamos 'withPivot' para saber la cantidad pedida de este plato
    public function pedidos()
    {
        return $this->belongsToMany(Pedido::class)->withPivot('cantidad');
    }

    // Un plato tiene muchos comentarios
    public function comentarios()
    {
        return $this->hasMany(Comentario::class);
    }
}