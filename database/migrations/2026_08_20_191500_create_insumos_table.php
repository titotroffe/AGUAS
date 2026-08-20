<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        Schema::create('insumos', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->string('slug')->unique();
            $table->timestamps();
        });

        // Seed initial data
        DB::table('insumos')->insert([
            ['nombre' => 'Sulfato de Aluminio', 'slug' => 'sulfato', 'created_at' => now(), 'updated_at' => now()],
            ['nombre' => 'Hipoclorito de Sodio', 'slug' => 'hipoclorito', 'created_at' => now(), 'updated_at' => now()],
            ['nombre' => 'Poliamina', 'slug' => 'poliamina', 'created_at' => now(), 'updated_at' => now()],
            ['nombre' => 'Cal Hidráulica', 'slug' => 'cal_hidraulica', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    public function down()
    {
        Schema::dropIfExists('insumos');
    }
};
