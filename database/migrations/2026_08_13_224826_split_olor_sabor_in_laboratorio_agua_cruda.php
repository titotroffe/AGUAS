<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('laboratorio_agua_cruda', function (Blueprint $table) {
            $table->dropColumn('olor_sabor');
            $table->string('olor')->nullable()->after('color');
            $table->string('sabor')->nullable()->after('olor');
        });
    }

    public function down()
    {
        Schema::table('laboratorio_agua_cruda', function (Blueprint $table) {
            $table->dropColumn(['olor', 'sabor']);
            $table->string('olor_sabor')->nullable()->after('color');
        });
    }
};
