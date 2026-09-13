<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Limpieza de duplicados "difusos" (nombres similares pero no idénticos)
        $duplicatesToKeepNames = [
            'Mercurio Total [Hg Total]' => 'Mercurio',
            'Cadmio [Cd]' => 'Cadmio',
            'Arsénico [As]' => 'Arsénico',
            'Cromo Total [Cr Total]' => 'Cromo',
            'Plomo [Pb]' => 'Plomo',
            'Hidrocarburos Aromáticos: Benceno' => 'Hidrocarburos: Benzeno',
            'Hidrocarburos Aromáticos: Benzo (a) pireno' => 'Hidrocarburos: Benzeno (a) pireno',
        ];

        foreach ($duplicatesToKeepNames as $nameToDelete => $nameToKeep) {
            $typeToDelete = DB::table('lab_tipos_medicion')->where('nombre', $nameToDelete)->first();
            $typeToKeep = DB::table('lab_tipos_medicion')->where('nombre', $nameToKeep)->first();

            if ($typeToDelete && $typeToKeep) {
                $this->fusionarRegistros($typeToDelete->id, $typeToKeep->id);
            }
        }

        // 2. Limpieza de duplicados exactos (ej: "Pesticidas" creados 2 veces por error)
        $duplicadosNombres = DB::table('lab_tipos_medicion')
            ->select('nombre')
            ->groupBy('nombre')
            ->havingRaw('COUNT(*) > 1')
            ->pluck('nombre');

        foreach ($duplicadosNombres as $nombre) {
            // Obtener todos los registros con ese nombre ordenados por ID
            $registros = DB::table('lab_tipos_medicion')
                ->where('nombre', $nombre)
                ->orderBy('id', 'asc')
                ->get();

            // El de menor ID será el "principal" que conservaremos
            $principal = $registros->first();
            $idPrincipal = $principal->id;

            // Iteramos sobre los demás (los duplicados)
            foreach ($registros->slice(1) as $duplicado) {
                $this->fusionarRegistros($duplicado->id, $idPrincipal);
            }
        }
    }

    private function fusionarRegistros($idDuplicado, $idPrincipal)
    {
        // 1. Manejar las configuraciones en lab_mediciones
        $medicionesDuplicadas = DB::table('lab_mediciones')
            ->where('tipo_medicion_id', $idDuplicado)
            ->get();

        foreach ($medicionesDuplicadas as $medDuplicada) {
            // ¿Existe ya una configuración idéntica para el ID principal?
            $medPrincipal = DB::table('lab_mediciones')
                ->where('modulo_id', $medDuplicada->modulo_id)
                ->where('frecuencia_id', $medDuplicada->frecuencia_id)
                ->where('pozo_id', $medDuplicada->pozo_id)
                ->where('insumo_id', $medDuplicada->insumo_id)
                ->where('tipo_medicion_id', $idPrincipal)
                ->first();

            if ($medPrincipal) {
                // Existe configuración en el ID principal. Traspasamos valores históricos al medPrincipal
                DB::table('lab_valores')
                    ->where('medicion_id', $medDuplicada->id)
                    ->update(['medicion_id' => $medPrincipal->id]);
                    
                // Y borramos la config duplicada
                DB::table('lab_mediciones')->where('id', $medDuplicada->id)->delete();
            } else {
                // No existe configuración para el ID principal con esos mismos parámetros.
                // Actualizamos el tipo_medicion_id en la config duplicada para que apunte al principal.
                DB::table('lab_mediciones')
                    ->where('id', $medDuplicada->id)
                    ->update(['tipo_medicion_id' => $idPrincipal]);
            }
        }

        // 2. Finalmente, eliminamos el tipo de medición duplicado
        DB::table('lab_tipos_medicion')->where('id', $idDuplicado)->delete();
    }

    public function down(): void
    {
        // Irreversible
    }
};
