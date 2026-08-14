<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('laboratorio_insumos', function (Blueprint $table) {
            $table->id();
            $table->date('fecha');
            $table->string('tipo_insumo'); // sulfato, hipoclorito, poliamina, cal_hidraulica
            $table->boolean('preparacion_archivo_contramuestra')->default(false);
            
            // Campos específicos, pueden ser null dependiendo del insumo
            $table->string('residuo_insoluble')->nullable();
            $table->string('oxido_ferroso')->nullable();
            $table->string('oxido_ferrico')->nullable();
            $table->string('oxido_aluminio')->nullable();
            $table->string('oxidos_utiles')->nullable();
            $table->string('manganeso')->nullable();
            $table->string('densidad_20c')->nullable();
            $table->string('cloro_activo')->nullable();
            $table->string('peso_litro')->nullable();
            $table->text('observaciones')->nullable();

            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('laboratorio_insumos');
    }
};
