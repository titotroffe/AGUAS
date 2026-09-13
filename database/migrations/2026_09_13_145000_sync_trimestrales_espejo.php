<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Obtener todos los IDs de los análisis trimestrales (frecuencia = 2) de Agua Cruda (Módulo 2)
        $trimestralesAguaCruda = DB::table('lab_mediciones')
            ->where('modulo_id', 2)
            ->where('frecuencia_id', 2)
            ->pluck('tipo_medicion_id');

        // Asignar los mismos a Producto Terminado (Módulo 3) si no existen
        $insertData = [];
        foreach ($trimestralesAguaCruda as $tipoId) {
            $existe = DB::table('lab_mediciones')
                ->where('modulo_id', 3)
                ->where('frecuencia_id', 2)
                ->where('tipo_medicion_id', $tipoId)
                ->exists();

            if (!$existe) {
                $insertData[] = [
                    'modulo_id' => 3,
                    'insumo_id' => null,
                    'pozo_id' => null,
                    'frecuencia_id' => 2,
                    'tipo_medicion_id' => $tipoId,
                    'activo' => true,
                    'min' => null,
                    'max' => null,
                ];
            }
        }

        if (!empty($insertData)) {
            DB::table('lab_mediciones')->insert($insertData);
        }
        
        // Opcional: Eliminar los que están en Módulo 3 Trimestral pero NO en Módulo 2 Trimestral
        // (Para asegurar un espejo exacto)
        DB::table('lab_mediciones')
            ->where('modulo_id', 3)
            ->where('frecuencia_id', 2)
            ->whereNotIn('tipo_medicion_id', $trimestralesAguaCruda)
            ->delete();
    }

    public function down(): void
    {
        // Irreversible
    }
};
