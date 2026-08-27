<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('lab_tipos_medicion', function (Blueprint $table) {
            $table->string('categoria')->default('FISICOQUÍMICO')->after('unidad');
            $table->boolean('es_texto')->default(false)->after('categoria');
            $table->boolean('es_booleano')->default(false)->after('es_texto');
            $table->string('tipo_campo')->default('number')->after('es_booleano'); // 'number', 'text', 'boolean'
        });
    }

    public function down()
    {
        Schema::table('lab_tipos_medicion', function (Blueprint $table) {
            $table->dropColumn(['categoria', 'es_texto', 'es_booleano', 'tipo_campo']);
        });
    }
};
