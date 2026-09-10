<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Fix for trimestral fields that might have been accidentally set to Mensual (1)
        DB::statement("
            UPDATE lab_mediciones lm
            JOIN lab_tipos_medicion ltm ON lm.tipo_medicion_id = ltm.id
            SET lm.frecuencia_id = 2
            WHERE lm.modulo_id = 2 AND ltm.categoria = 'TRIMESTRAL'
        ");

        // Fix to change the category from 'TRIMESTRAL' to 'FISICOQUÍMICO'
        DB::table('lab_tipos_medicion')
            ->where('categoria', 'TRIMESTRAL')
            ->update(['categoria' => 'FISICOQUÍMICO']);
    }

    public function down(): void
    {
        // No down needed for a data fix
    }
};
