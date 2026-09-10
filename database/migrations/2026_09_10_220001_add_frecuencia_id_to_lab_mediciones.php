<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('lab_mediciones', function (Blueprint $table) {
            $table->foreignId('frecuencia_id')->nullable()->after('pozo_id')->constrained('lab_frecuencias')->onDelete('cascade');
        });

        // Set existing Agua Cruda fields to Mensual
        DB::table('lab_mediciones')
            ->where('modulo_id', 2)
            ->update(['frecuencia_id' => 1]);
    }

    public function down(): void
    {
        Schema::table('lab_mediciones', function (Blueprint $table) {
            $table->dropForeign(['frecuencia_id']);
            $table->dropColumn('frecuencia_id');
        });
    }
};
