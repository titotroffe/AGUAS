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
            $table->string('role')->default('operador')->after('email');
            $table->boolean('is_approved')->default(false)->after('role');
        });

        // A todos los usuarios existentes los hacemos jefatura y aprobados para no bloquearlos
        \Illuminate\Support\Facades\DB::table('users')->update([
            'role' => 'jefatura',
            'is_approved' => true
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['role', 'is_approved']);
        });
    }
};
