<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $nuevosEnsayosTrimestrales = [
            // Metales Pesados e Inorganicos
            ['nombre' => 'Arsénico [As]', 'categoria' => 'FISICOQUÍMICO', 'es_texto' => false, 'es_booleano' => false, 'tipo_campo' => 'number', 'unidad' => null],
            ['nombre' => 'Cadmio [Cd]', 'categoria' => 'FISICOQUÍMICO', 'es_texto' => false, 'es_booleano' => false, 'tipo_campo' => 'number', 'unidad' => null],
            ['nombre' => 'Cianuro [CN⁻]', 'categoria' => 'FISICOQUÍMICO', 'es_texto' => false, 'es_booleano' => false, 'tipo_campo' => 'number', 'unidad' => null],
            ['nombre' => 'Cobre [Cu]', 'categoria' => 'FISICOQUÍMICO', 'es_texto' => false, 'es_booleano' => false, 'tipo_campo' => 'number', 'unidad' => null],
            ['nombre' => 'Cromo Total [Cr Total]', 'categoria' => 'FISICOQUÍMICO', 'es_texto' => false, 'es_booleano' => false, 'tipo_campo' => 'number', 'unidad' => null],
            ['nombre' => 'Fluor [F]', 'categoria' => 'FISICOQUÍMICO', 'es_texto' => false, 'es_booleano' => false, 'tipo_campo' => 'number', 'unidad' => null],
            ['nombre' => 'Manganeso [Mn]', 'categoria' => 'FISICOQUÍMICO', 'es_texto' => false, 'es_booleano' => false, 'tipo_campo' => 'number', 'unidad' => null],
            ['nombre' => 'Mercurio Total [Hg Total]', 'categoria' => 'FISICOQUÍMICO', 'es_texto' => false, 'es_booleano' => false, 'tipo_campo' => 'number', 'unidad' => null],
            ['nombre' => 'Nitrato (como NO₃⁻)', 'categoria' => 'FISICOQUÍMICO', 'es_texto' => false, 'es_booleano' => false, 'tipo_campo' => 'number', 'unidad' => null],
            ['nombre' => 'Nitrito (como NO₂⁻)', 'categoria' => 'FISICOQUÍMICO', 'es_texto' => false, 'es_booleano' => false, 'tipo_campo' => 'number', 'unidad' => null],
            ['nombre' => 'Plomo [Pb]', 'categoria' => 'FISICOQUÍMICO', 'es_texto' => false, 'es_booleano' => false, 'tipo_campo' => 'number', 'unidad' => null],
            ['nombre' => 'Selenio [Se]', 'categoria' => 'FISICOQUÍMICO', 'es_texto' => false, 'es_booleano' => false, 'tipo_campo' => 'number', 'unidad' => null],
            ['nombre' => 'Plata [Ag]', 'categoria' => 'FISICOQUÍMICO', 'es_texto' => false, 'es_booleano' => false, 'tipo_campo' => 'number', 'unidad' => null],

            // Alcanos Clorados
            ['nombre' => 'Alcanos Clorados: 1,2 Dicloroetano', 'categoria' => 'FISICOQUÍMICO', 'es_texto' => false, 'es_booleano' => false, 'tipo_campo' => 'number', 'unidad' => null],
            ['nombre' => 'Alcanos Clorados: Tetracloruro de carbono', 'categoria' => 'FISICOQUÍMICO', 'es_texto' => false, 'es_booleano' => false, 'tipo_campo' => 'number', 'unidad' => null],

            // Etenos Clorados
            ['nombre' => 'Etenos Clorados: 1.1 Dicloroeteno', 'categoria' => 'FISICOQUÍMICO', 'es_texto' => false, 'es_booleano' => false, 'tipo_campo' => 'number', 'unidad' => null],
            ['nombre' => 'Etenos Clorados: Tricloroeteno', 'categoria' => 'FISICOQUÍMICO', 'es_texto' => false, 'es_booleano' => false, 'tipo_campo' => 'number', 'unidad' => null],
            ['nombre' => 'Etenos Clorados: Tetracloroeteno', 'categoria' => 'FISICOQUÍMICO', 'es_texto' => false, 'es_booleano' => false, 'tipo_campo' => 'number', 'unidad' => null],

            // Hidrocarburos Aromaticos
            ['nombre' => 'Hidrocarburos Aromáticos: Benceno', 'categoria' => 'FISICOQUÍMICO', 'es_texto' => false, 'es_booleano' => false, 'tipo_campo' => 'number', 'unidad' => null],
            ['nombre' => 'Hidrocarburos Aromáticos: Benzo (a) pireno', 'categoria' => 'FISICOQUÍMICO', 'es_texto' => false, 'es_booleano' => false, 'tipo_campo' => 'number', 'unidad' => null],

            // Pesticidas
            ['nombre' => 'Pesticidas: Aldrín/Dieldrín', 'categoria' => 'FISICOQUÍMICO', 'es_texto' => false, 'es_booleano' => false, 'tipo_campo' => 'number', 'unidad' => null],
            ['nombre' => 'Pesticidas: Clordano (total isómeros)', 'categoria' => 'FISICOQUÍMICO', 'es_texto' => false, 'es_booleano' => false, 'tipo_campo' => 'number', 'unidad' => null],
            ['nombre' => 'Pesticidas: 2,4D (ácido dicloro-fenoxiacético)', 'categoria' => 'FISICOQUÍMICO', 'es_texto' => false, 'es_booleano' => false, 'tipo_campo' => 'number', 'unidad' => null],
            ['nombre' => 'Pesticidas: DDT (total isómeros)', 'categoria' => 'FISICOQUÍMICO', 'es_texto' => false, 'es_booleano' => false, 'tipo_campo' => 'number', 'unidad' => null],
            ['nombre' => 'Pesticidas: Heptacloro y Heptacloroepóxido', 'categoria' => 'FISICOQUÍMICO', 'es_texto' => false, 'es_booleano' => false, 'tipo_campo' => 'number', 'unidad' => null],
            ['nombre' => 'Pesticidas: Hexaclorobenceno', 'categoria' => 'FISICOQUÍMICO', 'es_texto' => false, 'es_booleano' => false, 'tipo_campo' => 'number', 'unidad' => null],
            ['nombre' => 'Pesticidas: Lindano', 'categoria' => 'FISICOQUÍMICO', 'es_texto' => false, 'es_booleano' => false, 'tipo_campo' => 'number', 'unidad' => null],
            ['nombre' => 'Pesticidas: Metoxicloro', 'categoria' => 'FISICOQUÍMICO', 'es_texto' => false, 'es_booleano' => false, 'tipo_campo' => 'number', 'unidad' => null],
            ['nombre' => 'Pesticidas: Pentaclorofenol', 'categoria' => 'FISICOQUÍMICO', 'es_texto' => false, 'es_booleano' => false, 'tipo_campo' => 'number', 'unidad' => null],

            // Desinfectantes y subproductos
            ['nombre' => 'Desinfectantes: Cloro (libre residual)', 'categoria' => 'FISICOQUÍMICO', 'es_texto' => false, 'es_booleano' => false, 'tipo_campo' => 'number', 'unidad' => null],
            ['nombre' => 'Desinfectantes: Monocloramina', 'categoria' => 'FISICOQUÍMICO', 'es_texto' => false, 'es_booleano' => false, 'tipo_campo' => 'number', 'unidad' => null],

            // Clorfenoles
            ['nombre' => 'Clorfenoles: 2,4,6 - Tricorofenol', 'categoria' => 'FISICOQUÍMICO', 'es_texto' => false, 'es_booleano' => false, 'tipo_campo' => 'number', 'unidad' => null],

            // Trihalometanos
            ['nombre' => 'Trihalometanos: Bromoformo', 'categoria' => 'FISICOQUÍMICO', 'es_texto' => false, 'es_booleano' => false, 'tipo_campo' => 'number', 'unidad' => null],
            ['nombre' => 'Trihalometanos: Dibromoclorometano', 'categoria' => 'FISICOQUÍMICO', 'es_texto' => false, 'es_booleano' => false, 'tipo_campo' => 'number', 'unidad' => null],
            ['nombre' => 'Trihalometanos: Bromodiclorometano', 'categoria' => 'FISICOQUÍMICO', 'es_texto' => false, 'es_booleano' => false, 'tipo_campo' => 'number', 'unidad' => null],
        ];

        foreach ($nuevosEnsayosTrimestrales as $ensayo) {
            $tipoMedicionId = DB::table('lab_tipos_medicion')->insertGetId($ensayo);

            // Asignar al módulo 2 (Agua Cruda)
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
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $categoriasTrimestrales = [
            'TRIMESTRAL'
        ];

        $tiposMedicion = DB::table('lab_tipos_medicion')
            ->whereIn('categoria', $categoriasTrimestrales)
            ->pluck('id');

        if ($tiposMedicion->isNotEmpty()) {
            DB::table('lab_mediciones')->whereIn('tipo_medicion_id', $tiposMedicion)->delete();
            DB::table('lab_tipos_medicion')->whereIn('id', $tiposMedicion)->delete();
        }
    }
};
