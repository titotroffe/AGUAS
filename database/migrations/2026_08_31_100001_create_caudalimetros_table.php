<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('caudalimetros', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->enum('bomba', ['sulfato', 'cloro']);
            $table->decimal('caudal_m3h', 8, 2); // m3/h
            $table->timestamps();

            $table->index(['bomba', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('caudalimetros');
    }
};
