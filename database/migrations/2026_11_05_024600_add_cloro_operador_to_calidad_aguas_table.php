<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!Schema::hasColumn('calidad_aguas', 'cloro_operador')) {
            Schema::table('calidad_aguas', function (Blueprint $table) {
                $table->string('cloro_operador', 5)->nullable()->after('cloro_residual');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('calidad_aguas', 'cloro_operador')) {
            Schema::table('calidad_aguas', function (Blueprint $table) {
                $table->dropColumn('cloro_operador');
            });
        }
    }
};

