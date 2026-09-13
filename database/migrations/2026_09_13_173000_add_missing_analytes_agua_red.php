<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $moduloId = 5; // Agua Potable de Red
        $frecuenciaId = 1; // Mensual

        // Tipos de medición a agregar
        $tiposMedicionIds = [
            66, // Zinc
            67, // Sodio
            68  // Detergentes sintéticos
        ];

        $insertData = [];
        foreach ($tiposMedicionIds as $tipoId) {
            // Verificar si ya existe para no duplicar
            $existe = DB::table('lab_mediciones')
                        ->where('modulo_id', $moduloId)
                        ->where('frecuencia_id', $frecuenciaId)
                        ->where('tipo_medicion_id', $tipoId)
                        ->exists();
            
            if (!$existe) {
                $insertData[] = [
                    'modulo_id' => $moduloId,
                    'frecuencia_id' => $frecuenciaId,
                    'tipo_medicion_id' => $tipoId,
                    'activo' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
        }

        if (!empty($insertData)) {
            DB::table('lab_mediciones')->insert($insertData);
        }
    }

    public function down(): void
    {
        DB::table('lab_mediciones')
            ->where('modulo_id', 5)
            ->where('frecuencia_id', 1)
            ->whereIn('tipo_medicion_id', [66, 67, 68])
            ->delete();
    }
};
