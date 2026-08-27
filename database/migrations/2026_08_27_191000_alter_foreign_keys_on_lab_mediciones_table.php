<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('lab_mediciones', function (Blueprint $table) {
            $table->dropForeign(['modulo_id']);
            $table->dropForeign(['insumo_id']);
            $table->dropForeign(['pozo_id']);
            $table->dropForeign(['tipo_medicion_id']);

            $table->foreign('modulo_id')->references('id')->on('lab_modulos')->onDelete('restrict');
            $table->foreign('insumo_id')->references('id')->on('lab_insumos')->onDelete('restrict');
            $table->foreign('pozo_id')->references('id')->on('lab_pozos')->onDelete('restrict');
            $table->foreign('tipo_medicion_id')->references('id')->on('lab_tipos_medicion')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('lab_mediciones', function (Blueprint $table) {
            $table->dropForeign(['modulo_id']);
            $table->dropForeign(['insumo_id']);
            $table->dropForeign(['pozo_id']);
            $table->dropForeign(['tipo_medicion_id']);

            $table->foreign('modulo_id')->references('id')->on('lab_modulos')->onDelete('cascade');
            $table->foreign('insumo_id')->references('id')->on('lab_insumos')->onDelete('cascade');
            $table->foreign('pozo_id')->references('id')->on('lab_pozos')->onDelete('cascade');
            $table->foreign('tipo_medicion_id')->references('id')->on('lab_tipos_medicion')->onDelete('cascade');
        });
    }
};
