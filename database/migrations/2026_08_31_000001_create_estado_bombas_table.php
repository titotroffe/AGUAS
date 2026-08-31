<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('estado_bombas', function (Blueprint $table) {
            $table->id();
            $table->string('dispositivo'); // bomba_1, bomba_2, bomba_3, pozo_norte, pozo_sur
            $table->boolean('estado')->default(false); // true = encendido
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->timestamps();

            $table->unique('dispositivo');
        });

        // Insertar estados iniciales apagados
        DB::table('estado_bombas')->insert([
            ['dispositivo' => 'bomba_1', 'estado' => false, 'user_id' => null, 'created_at' => now(), 'updated_at' => now()],
            ['dispositivo' => 'bomba_2', 'estado' => false, 'user_id' => null, 'created_at' => now(), 'updated_at' => now()],
            ['dispositivo' => 'bomba_3', 'estado' => false, 'user_id' => null, 'created_at' => now(), 'updated_at' => now()],
            ['dispositivo' => 'pozo_norte', 'estado' => false, 'user_id' => null, 'created_at' => now(), 'updated_at' => now()],
            ['dispositivo' => 'pozo_sur', 'estado' => false, 'user_id' => null, 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('estado_bombas');
    }
};
