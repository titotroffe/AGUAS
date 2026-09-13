<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $moduloId = 5; // Agua Potable de Red
        $frecuenciaId = 1; // Mensual

        // Primero borramos todas las mediciones de este módulo y frecuencia
        DB::table('lab_mediciones')
            ->where('modulo_id', $moduloId)
            ->where('frecuencia_id', $frecuenciaId)
            ->delete();

        // Tipos de medición requeridos
        $tiposMedicionIds = [
            11, // Color
            12, // Olor
            13, // Sabor
            14, // Turbiedad
            15, // Aluminio
            16, // Cloruro
            17, // Hierro
            18, // pH
            19, // Sulfato
            20, // Sólidos Disueltos Totales
            29, // Coliformes Totales
            30, // E. Coli / Coliformes Fecales
            60  // Desinfectantes: Cloro (libre residual)
        ];

        $insertData = [];
        foreach ($tiposMedicionIds as $tipoId) {
            $insertData[] = [
                'modulo_id' => $moduloId,
                'frecuencia_id' => $frecuenciaId,
                'tipo_medicion_id' => $tipoId,
                'activo' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        DB::table('lab_mediciones')->insert($insertData);
    }

    public function down(): void
    {
        // No implementamos down, ya que el down borraría las nuevas y no tenemos las viejas en memoria para revertir.
        // Anteriormente era un clon de Modulo 3, Frecuencia 1.
    }
};
