<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('laboratorio_agua_cruda', function (Blueprint $table) {
            $table->id();
            $table->date('fecha');
            
            $table->string('color')->nullable();
            $table->string('olor_sabor')->nullable();
            $table->string('turbiedad')->nullable();
            $table->string('aluminio')->nullable();
            $table->string('cloruro')->nullable();
            $table->string('hierro')->nullable();
            $table->string('ph')->nullable();
            $table->string('sulfato')->nullable();
            $table->string('solidos_disueltos_totales')->nullable();
            $table->string('mercurio')->nullable();
            $table->string('cadmio')->nullable();
            $table->string('arsenico')->nullable();
            $table->string('cromo')->nullable();
            
            $table->string('bacterias_aerobicas_heterotrofas')->nullable();
            $table->string('pseudomona_aeruginosa')->nullable();
            $table->string('giardia_lamblia')->nullable();
            $table->string('fitoplancton_zooplancton')->nullable();
            
            $table->text('observaciones')->nullable();

            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('laboratorio_agua_cruda');
    }
};
