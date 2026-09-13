<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $camposTrimestrales = range(31, 65);
        $insertData = [];

        foreach ($camposTrimestrales as $tipoId) {
            // Verificar que exista en lab_tipos_medicion por seguridad
            $existe = DB::table('lab_tipos_medicion')->where('id', $tipoId)->exists();
            if ($existe) {
                // Verificar si ya existe esta configuracion para evitar duplicados
                $existeConfig = DB::table('lab_mediciones')
                    ->where('modulo_id', 3)
                    ->where('tipo_medicion_id', $tipoId)
                    ->exists();
                
                if (!$existeConfig) {
                    $insertData[] = [
                        'modulo_id' => 3,
                        'insumo_id' => null,
                        'pozo_id' => null,
                        'frecuencia_id' => 2, // Trimestral
                        'tipo_medicion_id' => $tipoId,
                        'activo' => true,
                        'min' => null,
                        'max' => null,
                    ];
                }
            }
        }

        if (!empty($insertData)) {
            DB::table('lab_mediciones')->insert($insertData);
        }
    }

    public function down(): void
    {
        $camposTrimestrales = range(31, 65);
        DB::table('lab_mediciones')
            ->where('modulo_id', 3)
            ->where('frecuencia_id', 2)
            ->whereIn('tipo_medicion_id', $camposTrimestrales)
            ->delete();
    }
};
