<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Resolvemos los IDs de forma dinámica por nombre para evitar problemas de autoincrement desfasados en DB de prod
        $duplicatesToKeepNames = [
            // nombre_to_delete => nombre_to_keep
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
                $idToDelete = $typeToDelete->id;
                $idToKeep = $typeToKeep->id;

                $medicionesToDelete = DB::table('lab_mediciones')->where('tipo_medicion_id', $idToDelete)->get();
                
                foreach ($medicionesToDelete as $medToDelete) {
                    $medToKeep = DB::table('lab_mediciones')
                        ->where('modulo_id', $medToDelete->modulo_id)
                        ->where('tipo_medicion_id', $idToKeep)
                        ->first();
                        
                    if ($medToKeep) {
                        DB::table('lab_valores')
                            ->where('medicion_id', $medToDelete->id)
                            ->update(['medicion_id' => $medToKeep->id]);
                    } else {
                        DB::table('lab_mediciones')
                            ->where('id', $medToDelete->id)
                            ->update(['tipo_medicion_id' => $idToKeep]);
                    }
                }

                DB::table('lab_mediciones')->where('tipo_medicion_id', $idToDelete)->delete();
                DB::table('lab_tipos_medicion')->where('id', $idToDelete)->delete();
            }
        }
    }

    public function down(): void
    {
        // Esta migración es destructiva para los tipos duplicados y no debe revertirse fácilmente
        // ya que los datos históricos se fusionaron y no se puede saber cuáles eran del ID original.
    }
};
