<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Plato extends Model
{
    use HasFactory;

    protected $guarded = []; // Permite guardar todo sin restricciones

    // --- RELACIONES ---

    // Un plato pertenece a UNA categoría (Esto es NUEVO)
    public function categoria()
    {
        return $this->belongsTo(Categoria::class);
    }

    // Un plato puede estar en muchos pedidos
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
