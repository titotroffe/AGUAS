<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $moduloId = 5; // Agua Potable de Red
        $frecuenciaMensualId = 1;
        $frecuenciaSemestralId = 3; // ID real de Semestral es 3 (2 es Trimestral)

        // Limpiar lo que haya en la frecuencia Semestral (3) y Trimestral (2) para este módulo
        DB::table('lab_mediciones')
            ->where('modulo_id', $moduloId)
            ->whereIn('frecuencia_id', [2, 3])
            ->delete();

        // Traer las mediciones mensuales
        $mensuales = DB::table('lab_mediciones')
            ->where('modulo_id', $moduloId)
            ->where('frecuencia_id', $frecuenciaMensualId)
            ->get();

        $idsAEliminar = [
            66, // Zinc
            67, // Sodio
            68  // Detergentes sintéticos
        ];

        $insertData = [];
        foreach ($mensuales as $mensual) {
            // Saltamos sodio, zinc y detergentes
            if (in_array($mensual->tipo_medicion_id, $idsAEliminar)) {
                continue;
            }

            $insertData[] = [
                'modulo_id' => $moduloId,
                'frecuencia_id' => $frecuenciaSemestralId, // ID correcto (3)
                'tipo_medicion_id' => $mensual->tipo_medicion_id,
                'activo' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        if (!empty($insertData)) {
            DB::table('lab_mediciones')->insert($insertData);
        }
    }

    public function down(): void
    {
        // ...
    }
};
