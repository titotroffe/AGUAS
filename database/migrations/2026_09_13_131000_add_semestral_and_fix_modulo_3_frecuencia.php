<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Insert Semestral frequency if it doesn't exist
        DB::table('lab_frecuencias')->updateOrInsert(
            ['id' => 3],
            ['nombre' => 'Semestral', 'slug' => 'semestral', 'created_at' => now(), 'updated_at' => now()]
        );

        // Assign 'Mensual' (frecuencia_id = 1) to all Modulo 3 fields
        DB::table('lab_mediciones')
            ->where('modulo_id', 3)
            ->whereNull('frecuencia_id')
            ->update(['frecuencia_id' => 1]);
    }

    public function down(): void
    {
        // Remove Semestral frequency
        DB::table('lab_frecuencias')->where('id', 3)->delete();

        // Revert Modulo 3 fields back to null if needed
        DB::table('lab_mediciones')
            ->where('modulo_id', 3)
            ->where('frecuencia_id', 1)
            ->update(['frecuencia_id' => null]);
    }
};
