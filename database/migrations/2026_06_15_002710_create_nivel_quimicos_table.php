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
    Schema::create('nivel_quimicos', function (Blueprint $table) {
        $table->id();
        $table->foreignId('turno_id')->constrained('turnos')->onDelete('cascade');
        $table->string('quimico'); // Acá guardaremos si es 'Cloro', 'Poliamina' o 'Sulfato'
        $table->decimal('tanque_principal', 5, 2)->nullable(); // Ej: 85.50
        $table->decimal('tanque_auxiliar', 5, 2)->nullable();
        $table->timestamps(); 
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('nivel_quimicos');
    }
};
