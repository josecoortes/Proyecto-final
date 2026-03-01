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
    Schema::create('platos', function (Blueprint $table) {
        $table->id();
        $table->string('nombre', 100);
        $table->text('descripcion')->nullable();
        $table->decimal('precio', 10, 2);
        $table->string('imagen', 255)->nullable();

        // ¡Esta es la línea clave! Conecta el plato con la categoría
        $table->foreignId('categoria_id')->nullable()->constrained('categorias')->nullOnDelete();

        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('platos');
    }
};
