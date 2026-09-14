<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $replacements = [
            // id_to_remove => id_to_keep
            72 => 62, // Clorfenoles: 2, 4, 6 - Tricorofenol
        ];

        foreach ($replacements as $removeId => $keepId) {
            DB::table('lab_mediciones')
                ->where('tipo_medicion_id', $removeId)
                ->update(['tipo_medicion_id' => $keepId]);

            DB::table('lab_tipos_medicion')
                ->where('id', $removeId)
                ->delete();
        }
    }

    public function down(): void
    {
        // Not easily reversible
    }
};
