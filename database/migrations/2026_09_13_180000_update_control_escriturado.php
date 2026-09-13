<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $moduloId = 6; // Control Escriturado
        $frecuenciaMensualId = 1;

        // Limpiar cualquier medición previa para este módulo
        DB::table('lab_mediciones')->where('modulo_id', $moduloId)->delete();

        // Tipos de medición requeridos (todos bacteriológicos)
        $tiposMedicionIds = [
            25, // Bacterias Aerobias Heterótrofas
            26, // Pseudomona Aeruginosa
            29, // Coliformes Totales
            30, // E. Coli / Coliformes Fecales
            60  // Desinfectantes: Cloro (libre residual)
        ];

        $insertData = [];
        foreach ($tiposMedicionIds as $tipoId) {
            $insertData[] = [
                'modulo_id' => $moduloId,
                'frecuencia_id' => $frecuenciaMensualId,
                'tipo_medicion_id' => $tipoId,
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
        DB::table('lab_mediciones')->where('modulo_id', 6)->delete();
    }
};
