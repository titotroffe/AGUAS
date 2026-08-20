<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        Schema::table('laboratorio_insumos', function (Blueprint $table) {
            $table->unsignedBigInteger('insumo_id')->nullable()->after('fecha');
        });

        // Migrate existing data
        $insumos = DB::table('insumos')->get();
        foreach ($insumos as $insumo) {
            DB::table('laboratorio_insumos')
                ->where('tipo_insumo', $insumo->slug)
                ->update(['insumo_id' => $insumo->id]);
        }

        Schema::table('laboratorio_insumos', function (Blueprint $table) {
            $table->foreign('insumo_id')->references('id')->on('insumos')->onDelete('cascade');
            $table->dropColumn('tipo_insumo');
        });
    }

    public function down()
    {
        Schema::table('laboratorio_insumos', function (Blueprint $table) {
            $table->string('tipo_insumo')->after('fecha')->nullable();
        });

        // Rollback data
        $insumos = DB::table('insumos')->get();
        foreach ($insumos as $insumo) {
            DB::table('laboratorio_insumos')
                ->where('insumo_id', $insumo->id)
                ->update(['tipo_insumo' => $insumo->slug]);
        }

        Schema::table('laboratorio_insumos', function (Blueprint $table) {
            $table->dropForeign(['insumo_id']);
            $table->dropColumn('insumo_id');
        });
    }
};
