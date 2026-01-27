<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
    Schema::create('comentarios', function (Blueprint $table) {
        $table->id(); // id_comentario

        // Relaciones: Usuario opina sobre Plato
        $table->foreignId('user_id')->constrained()->onDelete('cascade');
        $table->foreignId('plato_id')->constrained()->onDelete('cascade');

        $table->integer('valoracion');
        $table->text('contenido');
        $table->date('fecha')->default(now());
        $table->timestamps();
    });
}
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('comentarios');
    }
};
