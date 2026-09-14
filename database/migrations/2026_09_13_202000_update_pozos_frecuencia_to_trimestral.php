<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Asignar 'Trimestral' (frecuencia_id = 2) a todos los campos del Modulo 4 (Pozos)
        DB::table('lab_mediciones')
            ->where('modulo_id', 4)
            ->whereNull('frecuencia_id')
            ->update(['frecuencia_id' => 2]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('lab_mediciones')
            ->where('modulo_id', 4)
            ->where('frecuencia_id', 2)
            ->update(['frecuencia_id' => null]);
    }
};
