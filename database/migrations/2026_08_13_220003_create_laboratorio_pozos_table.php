<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('laboratorio_pozos', function (Blueprint $table) {
            $table->id();
            $table->date('fecha');
            $table->integer('pozo_numero'); // 1 to 75
            
            $table->string('coliformes_totales')->nullable();
            $table->string('e_coli_coliformes')->nullable();
            
            $table->text('observaciones')->nullable();

            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('laboratorio_pozos');
    }
};
