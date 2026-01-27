<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Comentario extends Model
{
    use HasFactory;

    protected $guarded = [];

    // --- RELACIONES ---

    // Un comentario lo escribe UN usuario
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Un comentario pertenece a UN plato
    public function plato()
    {
        return $this->belongsTo(Plato::class);
    }
}