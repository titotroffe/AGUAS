<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // 1. Modulos (Insumos, Tratamiento, Producto, Pozos)
        Schema::create('lab_modulos', function (Blueprint $table) {
            $table->id();
            $table->string('descripcion');
            $table->timestamps();
        });

        // 2. Insumos Catálogo (Sulfato, Hipoclorito, Cal)
        Schema::create('lab_insumos', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->timestamps();
        });

        // 3. Pozos Catálogo
        Schema::create('lab_pozos', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->boolean('activo')->default(true);
            $table->string('direccion')->nullable();
            $table->timestamps();
        });

        // 4. Tipos de Medición (Residuo Insoluble, Oxido, Color, Sabor, Coliformes...)
        Schema::create('lab_tipos_medicion', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->string('unidad')->nullable();
            $table->timestamps();
        });

        // 5. Tabla Relacional de Configuraciones (MEDICIONES en tu Excel)
        Schema::create('lab_mediciones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('modulo_id')->constrained('lab_modulos')->onDelete('cascade');
            $table->foreignId('insumo_id')->nullable()->constrained('lab_insumos')->onDelete('cascade');
            $table->foreignId('pozo_id')->nullable()->constrained('lab_pozos')->onDelete('cascade');
            $table->foreignId('tipo_medicion_id')->constrained('lab_tipos_medicion')->onDelete('cascade');
            $table->boolean('activo')->default(true);
            $table->decimal('min', 10, 4)->nullable();
            $table->decimal('max', 10, 4)->nullable();
            $table->timestamps();
        });

        // 6. Valores Reales (LAB_med_valores)
        Schema::create('lab_valores', function (Blueprint $table) {
            $table->id();
            $table->date('fecha');
            $table->foreignId('medicion_id')->constrained('lab_mediciones')->onDelete('cascade');
            $table->string('valor')->nullable(); // String para soportar "Marrón", "Feo" o "9.5"
            $table->text('observaciones')->nullable();
            $table->timestamps();
        });

        // Drop the old hardcoded tables for insumos
        Schema::dropIfExists('insumo_sulfatos');
        Schema::dropIfExists('insumo_hipocloritos');
        Schema::dropIfExists('insumo_poliaminas');
        Schema::dropIfExists('insumo_cales');
    }

    public function down()
    {
        Schema::dropIfExists('lab_valores');
        Schema::dropIfExists('lab_mediciones');
        Schema::dropIfExists('lab_tipos_medicion');
        Schema::dropIfExists('lab_pozos');
        Schema::dropIfExists('lab_insumos');
        Schema::dropIfExists('lab_modulos');
    }
};
