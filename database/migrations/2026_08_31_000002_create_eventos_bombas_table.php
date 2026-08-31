<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('eventos_bombas', function (Blueprint $table) {
            $table->id();
            $table->string('dispositivo'); // bomba_1, bomba_2, bomba_3, pozo_norte, pozo_sur
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->timestamp('encendido_at');
            $table->timestamp('apagado_at')->nullable();
            $table->unsignedInteger('duracion_segundos')->nullable(); // calculado al apagar
            $table->timestamps();

            $table->index(['dispositivo', 'encendido_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('eventos_bombas');
    }
};
