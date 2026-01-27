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
    Schema::create('pedido_plato', function (Blueprint $table) {
        $table->id();

        // Unimos pedido y plato
        $table->foreignId('pedido_id')->constrained()->onDelete('cascade');
        $table->foreignId('plato_id')->constrained()->onDelete('cascade');

        $table->integer('cantidad')->default(1); // Cuántas hamburguesas iguales
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pedido_plato');
    }
};
