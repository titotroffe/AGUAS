<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('lab_tipos_medicion')
            ->where('categoria', 'INSUMOS')
            ->update(['categoria' => 'FISICOQUÍMICO']);
    }

    public function down(): void
    {
        DB::table('lab_tipos_medicion')
            ->whereIn('nombre', [
                'Residuo Insoluble', 'Óxido Ferroso', 'Óxido Férrico', 'Óxido de Aluminio',
                'Óxidos Útiles', 'Manganeso', 'Densidad A 20°C', 'Cloro Activo', 'Peso Litro',
                'Preparación Archivo Contramuestra'
            ])
            ->where('categoria', 'FISICOQUÍMICO')
            ->update(['categoria' => 'INSUMOS']);
    }
};
