<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Obtener los IDs de los análisis especificados
        $nombres = ['Mercurio', 'Cadmio', 'Arsénico', 'Cromo'];
        $tipos = DB::table('lab_tipos_medicion')
            ->whereIn('nombre', $nombres)
            ->pluck('id');

        if ($tipos->isNotEmpty()) {
            // Asignar esos IDs a la frecuencia Semestral (3) para el Módulo 3 (Producto Terminado)
            DB::table('lab_mediciones')
                ->where('modulo_id', 3)
                ->whereIn('tipo_medicion_id', $tipos)
                ->update(['frecuencia_id' => 3]);
        }
    }

    public function down(): void
    {
        $nombres = ['Mercurio', 'Cadmio', 'Arsénico', 'Cromo'];
        $tipos = DB::table('lab_tipos_medicion')
            ->whereIn('nombre', $nombres)
            ->pluck('id');

        if ($tipos->isNotEmpty()) {
            // Revertir a Mensual (1)
            DB::table('lab_mediciones')
                ->where('modulo_id', 3)
                ->whereIn('tipo_medicion_id', $tipos)
                ->update(['frecuencia_id' => 1]);
        }
    }
};
