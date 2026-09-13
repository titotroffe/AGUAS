<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Insertar el nuevo módulo "Agua Potable de Red"
        $moduloId = DB::table('lab_modulos')->insertGetId([
            'descripcion' => 'Agua Potable de Red',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // 2. Obtener todas las configuraciones de Producto Terminado (Módulo 3)
        // pero SOLO para las frecuencias Mensual (1) y Semestral (3)
        $configuracionesProducto = DB::table('lab_mediciones')
            ->where('modulo_id', 3)
            ->whereIn('frecuencia_id', [1, 3])
            ->get();

        // 3. Clonar las configuraciones al nuevo módulo
        $insertData = [];
        foreach ($configuracionesProducto as $config) {
            $insertData[] = [
                'modulo_id' => $moduloId,
                'insumo_id' => $config->insumo_id,
                'pozo_id' => $config->pozo_id,
                'frecuencia_id' => $config->frecuencia_id,
                'tipo_medicion_id' => $config->tipo_medicion_id,
                'activo' => $config->activo,
                'min' => $config->min,
                'max' => $config->max,
            ];
        }

        if (!empty($insertData)) {
            DB::table('lab_mediciones')->insert($insertData);
        }
    }

    public function down(): void
    {
        $modulo = DB::table('lab_modulos')->where('descripcion', 'Agua Potable de Red')->first();
        
        if ($modulo) {
            DB::table('lab_mediciones')->where('modulo_id', $modulo->id)->delete();
            DB::table('lab_modulos')->where('id', $modulo->id)->delete();
        }
    }
};
