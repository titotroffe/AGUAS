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
        $pozos = DB::table('lab_pozos')->get();
        $pozosSemestral = [11, 12, 13, 14, 18, 67, 19, 20, 23, 22, 33, 34, 24, 36, 6, 21, 39, 40, 41, 42, 43, 44, 45, 46, 47, 48, 49, 50];

        foreach ($pozos as $pozo) {
            foreach ($pozosSemestral as $tipoId) {
                DB::table('lab_mediciones')->updateOrInsert(
                    [
                        'modulo_id' => 4,
                        'insumo_id' => null,
                        'pozo_id' => $pozo->id,
                        'tipo_medicion_id' => $tipoId
                    ],
                    [
                        'frecuencia_id' => 3,
                        'activo' => true,
                        'min' => null,
                        'max' => null
                    ]
                );
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $pozosSemestral = [11, 12, 13, 14, 18, 67, 19, 20, 23, 22, 33, 34, 24, 36, 6, 21, 39, 40, 41, 42, 43, 44, 45, 46, 47, 48, 49, 50];
        
        DB::table('lab_mediciones')
            ->where('modulo_id', 4)
            ->whereIn('tipo_medicion_id', $pozosSemestral)
            ->delete();
    }
};
