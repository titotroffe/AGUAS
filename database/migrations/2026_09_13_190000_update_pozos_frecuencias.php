<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $moduloId = 4; // Pozos de Extracción
        
        // Limpiar cualquier medición previa para Pozos
        DB::table('lab_mediciones')->where('modulo_id', $moduloId)->delete();

        // Obtener todos los pozos activos
        $pozos = DB::table('lab_pozos')->where('activo', true)->get();

        $trimestralId = 2;
        $semestralId = 3;

        $analisisTrimestral = [29, 30]; // Coliformes Totales, E. Coli
        $analisisSemestral = [
            11, 12, 13, 14, 18, 67, 19, 20, 23, 22, 33, 34, 24, 36, 6, 
            21, 39, 40, 41, 42, 43, 44, 45, 46, 47, 48, 49, 50
        ];

        $insertData = [];

        foreach ($pozos as $pozo) {
            // Trimestral
            foreach ($analisisTrimestral as $tipoId) {
                $insertData[] = [
                    'modulo_id' => $moduloId,
                    'frecuencia_id' => $trimestralId,
                    'pozo_id' => $pozo->id,
                    'tipo_medicion_id' => $tipoId,
                    'activo' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }

            // Semestral
            foreach ($analisisSemestral as $tipoId) {
                $insertData[] = [
                    'modulo_id' => $moduloId,
                    'frecuencia_id' => $semestralId,
                    'pozo_id' => $pozo->id,
                    'tipo_medicion_id' => $tipoId,
                    'activo' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
        }

        if (!empty($insertData)) {
            // Insert in chunks of 100 to avoid limits just in case
            foreach (array_chunk($insertData, 100) as $chunk) {
                DB::table('lab_mediciones')->insert($chunk);
            }
        }
    }

    public function down(): void
    {
        DB::table('lab_mediciones')->where('modulo_id', 4)->delete();
    }
};
