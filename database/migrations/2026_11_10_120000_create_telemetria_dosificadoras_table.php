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
        Schema::create('telemetria_dosificadoras', function (Blueprint $table) {
            $table->id();
            $table->decimal('voltaje', 6, 1)->default(0);
            $table->decimal('corriente', 6, 3)->default(0);
            $table->string('tipo', 20)->default('rutina'); // rutina | falla | recuperacion
            $table->timestamps();

            $table->index('created_at');
            $table->index('tipo');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('telemetria_dosificadoras');
    }
};

