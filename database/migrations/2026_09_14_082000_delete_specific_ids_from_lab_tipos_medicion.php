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
        $idsToRemove = [83, 108, 115, 119, 120];

        // Se eliminan primero las referencias en lab_mediciones
        DB::table('lab_mediciones')
            ->whereIn('tipo_medicion_id', $idsToRemove)
            ->delete();

        // Luego se eliminan los tipos de medición
        DB::table('lab_tipos_medicion')
            ->whereIn('id', $idsToRemove)
            ->delete();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Not easily reversible
    }
};
