<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Verificar si por alguna casualidad ya existe para no duplicarlo
        $existe = DB::table('lab_tipos_medicion')->where('nombre', 'Trihalometanos: Cloroformo')->first();

        if (!$existe) {
            // 2. Insertarlo en el catálogo maestro
            $tipoMedicionId = DB::table('lab_tipos_medicion')->insertGetId([
                'nombre' => 'Trihalometanos: Cloroformo',
                'categoria' => 'FISICOQUÍMICO',
                'es_texto' => false,
                'es_booleano' => false,
                'tipo_campo' => 'number',
                'unidad' => null
            ]);

            // 3. Asignarlo a Agua Cruda (Módulo 2) como Trimestral
            DB::table('lab_mediciones')->insert([
                'modulo_id' => 2,
                'insumo_id' => null,
                'pozo_id' => null,
                'frecuencia_id' => 2, // 2 = Trimestral
                'tipo_medicion_id' => $tipoMedicionId,
                'activo' => true,
                'min' => null,
                'max' => null,
            ]);

            // 4. Asignarlo a Producto Terminado (Módulo 3) como Trimestral
            DB::table('lab_mediciones')->insert([
                'modulo_id' => 3,
                'insumo_id' => null,
                'pozo_id' => null,
                'frecuencia_id' => 2, // 2 = Trimestral
                'tipo_medicion_id' => $tipoMedicionId,
                'activo' => true,
                'min' => null,
                'max' => null,
            ]);
        }
    }

    public function down(): void
    {
        $tipo = DB::table('lab_tipos_medicion')->where('nombre', 'Trihalometanos: Cloroformo')->first();
        if ($tipo) {
            DB::table('lab_mediciones')->where('tipo_medicion_id', $tipo->id)->delete();
            DB::table('lab_tipos_medicion')->where('id', $tipo->id)->delete();
        }
    }
};
