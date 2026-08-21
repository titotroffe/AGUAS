<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EavSeeder extends Seeder
{
    public function run()
    {
        // Modulos
        $modulos = [
            ['id' => 1, 'descripcion' => 'Insumos'],
            ['id' => 2, 'descripcion' => 'Tratamiento'],
            ['id' => 3, 'descripcion' => 'Producto'],
            ['id' => 4, 'descripcion' => 'Pozos'],
        ];
        DB::table('lab_modulos')->insert($modulos);

        // Insumos
        $insumos = [
            ['id' => 1, 'nombre' => 'Sulfato de Aluminio'],
            ['id' => 2, 'nombre' => 'Hipoclorito de Sodio'],
            ['id' => 3, 'nombre' => 'Poliamina'],
            ['id' => 4, 'nombre' => 'Cal Hidráulica'],
        ];
        DB::table('lab_insumos')->insert($insumos);

        // Tipos de Medición (Para Insumos inicialmente)
        $tiposMedicion = [
            ['id' => 1, 'nombre' => 'Residuo Insoluble'],
            ['id' => 2, 'nombre' => 'Óxido Ferroso'],
            ['id' => 3, 'nombre' => 'Óxido Férrico'],
            ['id' => 4, 'nombre' => 'Óxido de Aluminio'],
            ['id' => 5, 'nombre' => 'Óxidos Útiles'],
            ['id' => 6, 'nombre' => 'Manganeso'],
            ['id' => 7, 'nombre' => 'Densidad A 20°C'],
            ['id' => 8, 'nombre' => 'Cloro Activo'],
            ['id' => 9, 'nombre' => 'Peso Litro'],
            ['id' => 10, 'nombre' => 'Preparación Archivo Contramuestra'],
        ];
        DB::table('lab_tipos_medicion')->insert($tiposMedicion);

        // Configuracion Mediciones (Solo modulo 1 Insumos por ahora para reemplazar lo anterior)
        $configuraciones = [
            // Sulfato
            ['modulo_id' => 1, 'insumo_id' => 1, 'pozo_id' => null, 'tipo_medicion_id' => 10, 'activo' => true], // Contramuestra
            ['modulo_id' => 1, 'insumo_id' => 1, 'pozo_id' => null, 'tipo_medicion_id' => 1, 'activo' => true],
            ['modulo_id' => 1, 'insumo_id' => 1, 'pozo_id' => null, 'tipo_medicion_id' => 2, 'activo' => true],
            ['modulo_id' => 1, 'insumo_id' => 1, 'pozo_id' => null, 'tipo_medicion_id' => 3, 'activo' => true],
            ['modulo_id' => 1, 'insumo_id' => 1, 'pozo_id' => null, 'tipo_medicion_id' => 4, 'activo' => true],
            ['modulo_id' => 1, 'insumo_id' => 1, 'pozo_id' => null, 'tipo_medicion_id' => 5, 'activo' => true],
            ['modulo_id' => 1, 'insumo_id' => 1, 'pozo_id' => null, 'tipo_medicion_id' => 6, 'activo' => true],
            ['modulo_id' => 1, 'insumo_id' => 1, 'pozo_id' => null, 'tipo_medicion_id' => 7, 'activo' => true],
            
            // Hipoclorito
            ['modulo_id' => 1, 'insumo_id' => 2, 'pozo_id' => null, 'tipo_medicion_id' => 10, 'activo' => true], // Contramuestra
            ['modulo_id' => 1, 'insumo_id' => 2, 'pozo_id' => null, 'tipo_medicion_id' => 8, 'activo' => true], // Cloro Activo
            ['modulo_id' => 1, 'insumo_id' => 2, 'pozo_id' => null, 'tipo_medicion_id' => 7, 'activo' => true], // Densidad

            // Poliamina
            ['modulo_id' => 1, 'insumo_id' => 3, 'pozo_id' => null, 'tipo_medicion_id' => 10, 'activo' => true], // Contramuestra
            ['modulo_id' => 1, 'insumo_id' => 3, 'pozo_id' => null, 'tipo_medicion_id' => 7, 'activo' => true], // Densidad

            // Cal
            ['modulo_id' => 1, 'insumo_id' => 4, 'pozo_id' => null, 'tipo_medicion_id' => 10, 'activo' => true], // Contramuestra
            ['modulo_id' => 1, 'insumo_id' => 4, 'pozo_id' => null, 'tipo_medicion_id' => 9, 'activo' => true], // Peso litro
        ];

        DB::table('lab_mediciones')->insert($configuraciones);
    }
}
