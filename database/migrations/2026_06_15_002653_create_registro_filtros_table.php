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
    Schema::create('registro_filtros', function (Blueprint $table) {
        $table->id();
        $table->foreignId('turno_id')->constrained('turnos')->onDelete('cascade');
        $table->boolean('norte_1')->default(false);
        $table->boolean('norte_2')->default(false);
        $table->boolean('norte_3')->default(false);
        $table->boolean('sur_1')->default(false);
        $table->boolean('sur_2')->default(false);
        $table->boolean('sur_3')->default(false);
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('registro_filtros');
    }
};
