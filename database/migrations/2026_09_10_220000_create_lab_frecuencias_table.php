<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lab_frecuencias', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->string('slug')->unique();
            $table->timestamps();
        });

        // Insert default frequencies
        DB::table('lab_frecuencias')->insert([
            ['id' => 1, 'nombre' => 'Mensual', 'slug' => 'mensual', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 2, 'nombre' => 'Trimestral', 'slug' => 'trimestral', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('lab_frecuencias');
    }
};
