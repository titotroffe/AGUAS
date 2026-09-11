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
        Schema::table('users', function (Blueprint $table) {
            $table->softDeletes();
        });

        $tablesWithCascade = [
            'calidad_aguas',
            'registro_presiones',
            'registro_filtros',
            'nivel_quimicos',
            'ensayos_bacteriologicos',
        ];

        foreach ($tablesWithCascade as $tableName) {
            Schema::table($tableName, function (Blueprint $table) use ($tableName) {
                $table->dropForeign(['user_id']);
            });
            Schema::table($tableName, function (Blueprint $table) {
                // Change to RESTRICT (default constrained without onDelete)
                $table->foreign('user_id')->references('id')->on('users')->restrictOnDelete();
            });
        }
        
        // caudalimetros, estado_bombas, eventos_bombas currently have SET NULL, we don't necessarily need to touch them, 
        // but if we want them consistent we could. However, they are nullable and SET NULL is fine for them.
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $tablesWithCascade = [
            'calidad_aguas',
            'registro_presiones',
            'registro_filtros',
            'nivel_quimicos',
            'ensayos_bacteriologicos',
        ];

        foreach ($tablesWithCascade as $tableName) {
            Schema::table($tableName, function (Blueprint $table) use ($tableName) {
                $table->dropForeign(['user_id']);
            });
            Schema::table($tableName, function (Blueprint $table) {
                $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            });
        }

        Schema::table('users', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
    }
};
