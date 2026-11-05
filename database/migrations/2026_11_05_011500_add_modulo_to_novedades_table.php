<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!Schema::hasColumn('novedades', 'modulo')) {
            Schema::table('novedades', function (Blueprint $table) {
                $table->string('modulo', 30)->nullable()->after('user_id')->index();
            });
        }

        if (!Schema::hasColumn('novedades', 'es_jefatura')) {
            Schema::table('novedades', function (Blueprint $table) {
                $table->boolean('es_jefatura')->default(false)->after('modulo');
            });
        }

        // Backfill de novedades existentes según el rol del usuario que las creó
        DB::statement("
            UPDATE novedades n
            INNER JOIN users u ON n.user_id = u.id
            SET n.modulo = CASE
                WHEN u.role = 'operador' THEN 'operadores'
                WHEN u.role = 'quimico' THEN 'quimico'
                WHEN u.role = 'laboratorio' THEN 'laboratorio'
                ELSE 'operadores'
            END
            WHERE n.modulo IS NULL
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('novedades', function (Blueprint $table) {
            if (Schema::hasColumn('novedades', 'es_jefatura')) {
                $table->dropColumn('es_jefatura');
            }
            if (Schema::hasColumn('novedades', 'modulo')) {
                $table->dropColumn('modulo');
            }
        });
    }
};
