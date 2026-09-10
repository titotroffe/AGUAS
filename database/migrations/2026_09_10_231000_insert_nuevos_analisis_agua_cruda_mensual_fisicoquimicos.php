<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $nuevosEnsayosMensuales = [
            ['nombre' => 'Zinc', 'unidad' => 'mg/L', 'categoria' => 'FISICOQUÍMICO', 'es_texto' => false, 'es_booleano' => false, 'tipo_campo' => 'number'],
            ['nombre' => 'Sodio', 'unidad' => 'mg/L', 'categoria' => 'FISICOQUÍMICO', 'es_texto' => false, 'es_booleano' => false, 'tipo_campo' => 'number'],
            ['nombre' => 'Detergentes sintéticos', 'unidad' => 'mg/L', 'categoria' => 'FISICOQUÍMICO', 'es_texto' => false, 'es_booleano' => false, 'tipo_campo' => 'number'],
            ['nombre' => 'Plomo', 'unidad' => 'mg/L', 'categoria' => 'FISICOQUÍMICO', 'es_texto' => false, 'es_booleano' => false, 'tipo_campo' => 'number'],
            ['nombre' => 'DBO', 'unidad' => 'mg/L', 'categoria' => 'FISICOQUÍMICO', 'es_texto' => false, 'es_booleano' => false, 'tipo_campo' => 'number'],
            ['nombre' => 'DQO', 'unidad' => 'mg/L', 'categoria' => 'FISICOQUÍMICO', 'es_texto' => false, 'es_booleano' => false, 'tipo_campo' => 'number'],
            ['nombre' => 'Clorfenoles: 2, 4, 6 - Tricorofenol', 'unidad' => null, 'categoria' => 'FISICOQUÍMICO', 'es_texto' => false, 'es_booleano' => false, 'tipo_campo' => 'number'],
            ['nombre' => 'Hidrocarburos: Benzeno', 'unidad' => null, 'categoria' => 'FISICOQUÍMICO', 'es_texto' => false, 'es_booleano' => false, 'tipo_campo' => 'number'],
            ['nombre' => 'Hidrocarburos: Benzeno (a) pireno', 'unidad' => null, 'categoria' => 'FISICOQUÍMICO', 'es_texto' => false, 'es_booleano' => false, 'tipo_campo' => 'number'],
        ];

        foreach ($nuevosEnsayosMensuales as $ensayo) {
            $tipoMedicionId = DB::table('lab_tipos_medicion')->insertGetId($ensayo);

            // Asignar al módulo 2 (Agua Cruda / Tratamiento) para frecuencia Mensual (id = 1)
            DB::table('lab_mediciones')->insert([
                'modulo_id' => 2,
                'insumo_id' => null,
                'pozo_id' => null,
                'frecuencia_id' => 1, // 1 = Mensual
                'tipo_medicion_id' => $tipoMedicionId,
                'activo' => true,
                'min' => null,
                'max' => null,
            ]);

            // Asignar al módulo 3 (Producto Terminado)
            DB::table('lab_mediciones')->insert([
                'modulo_id' => 3,
                'insumo_id' => null,
                'pozo_id' => null,
                'frecuencia_id' => null,
                'tipo_medicion_id' => $tipoMedicionId,
                'activo' => true,
                'min' => null,
                'max' => null,
            ]);
        }
    }

    public function down(): void
    {
        // No implementado para simplificar la corrección de datos en vivo.
    }
};
