<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Insertar el módulo "Control Escriturado"
        DB::table('lab_modulos')->insert([
            'descripcion' => 'Control Escriturado',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // 2. Insertar el módulo "Control de Tanques de Escuelas e Instituciones"
        DB::table('lab_modulos')->insert([
            'descripcion' => 'Control de Tanques de Escuelas e Instituciones',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function down(): void
    {
        $modulo6 = DB::table('lab_modulos')->where('descripcion', 'Control Escriturado')->first();
        if ($modulo6) {
            DB::table('lab_mediciones')->where('modulo_id', $modulo6->id)->delete();
            DB::table('lab_modulos')->where('id', $modulo6->id)->delete();
        }

        $modulo7 = DB::table('lab_modulos')->where('descripcion', 'Control de Tanques de Escuelas e Instituciones')->first();
        if ($modulo7) {
            DB::table('lab_mediciones')->where('modulo_id', $modulo7->id)->delete();
            DB::table('lab_modulos')->where('id', $modulo7->id)->delete();
        }
    }
};
