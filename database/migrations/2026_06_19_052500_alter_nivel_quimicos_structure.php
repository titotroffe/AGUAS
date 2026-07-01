<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('nivel_quimicos')->truncate();

        Schema::table('nivel_quimicos', function (Blueprint $table) {
            $table->dropColumn(['tanque_principal', 'tanque_auxiliar']);
            $table->string('tipo_tanque')->after('quimico');
            $table->decimal('nivel', 5, 2)->after('tipo_tanque');
        });
    }

    public function down(): void
    {
        DB::table('nivel_quimicos')->truncate();
        
        Schema::table('nivel_quimicos', function (Blueprint $table) {
            $table->dropColumn(['tipo_tanque', 'nivel']);
            $table->decimal('tanque_principal', 5, 2)->nullable()->after('quimico');
            $table->decimal('tanque_auxiliar', 5, 2)->nullable()->after('tanque_principal');
        });
    }
};
