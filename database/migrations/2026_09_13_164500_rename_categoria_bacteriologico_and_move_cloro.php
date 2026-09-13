<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Renombrar la categoría 'BACTERIOLOGÍA Y BIOLOGÍA' a 'BACTERIOLÓGICO'
        DB::table('lab_tipos_medicion')
            ->where('categoria', 'BACTERIOLOGÍA Y BIOLOGÍA')
            ->update(['categoria' => 'BACTERIOLÓGICO']);

        // Mover Cloro libre (ID 60) a la categoría 'BACTERIOLÓGICO'
        DB::table('lab_tipos_medicion')
            ->where('id', 60) // Desinfectantes: Cloro (libre residual)
            ->update(['categoria' => 'BACTERIOLÓGICO']);
    }

    public function down(): void
    {
        DB::table('lab_tipos_medicion')
            ->where('categoria', 'BACTERIOLÓGICO')
            ->update(['categoria' => 'BACTERIOLOGÍA Y BIOLOGÍA']);

        DB::table('lab_tipos_medicion')
            ->where('id', 60)
            ->update(['categoria' => 'FISICOQUÍMICO']);
    }
};
