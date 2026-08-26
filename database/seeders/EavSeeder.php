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

        // Tipos de Medición
        $tiposMedicion = [
            // Insumos (1-10)
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
            // Agua Cruda / Producto Terminado (11-28)
            ['id' => 11, 'nombre' => 'Color'],
            ['id' => 12, 'nombre' => 'Olor'],
            ['id' => 13, 'nombre' => 'Sabor'],
            ['id' => 14, 'nombre' => 'Turbiedad'],
            ['id' => 15, 'nombre' => 'Aluminio'],
            ['id' => 16, 'nombre' => 'Cloruro'],
            ['id' => 17, 'nombre' => 'Hierro'],
            ['id' => 18, 'nombre' => 'pH'],
            ['id' => 19, 'nombre' => 'Sulfato'],
            ['id' => 20, 'nombre' => 'Sólidos Disueltos Totales'],
            ['id' => 21, 'nombre' => 'Mercurio'],
            ['id' => 22, 'nombre' => 'Cadmio'],
            ['id' => 23, 'nombre' => 'Arsénico'],
            ['id' => 24, 'nombre' => 'Cromo'],
            ['id' => 25, 'nombre' => 'Bacterias Aerobias Heterótrofas'],
            ['id' => 26, 'nombre' => 'Pseudomona Aeruginosa'],
            ['id' => 27, 'nombre' => 'Giardia Lamblia'],
            ['id' => 28, 'nombre' => 'Fitoplancton / Zooplancton'],
            // Pozos (29-30)
            ['id' => 29, 'nombre' => 'Coliformes Totales'],
            ['id' => 30, 'nombre' => 'E. Coli / Coliformes Fecales'],
        ];
        DB::table('lab_tipos_medicion')->insert($tiposMedicion);
        
        // Pozos Catálogo
        $pozos = [
            ['id' => 1, 'nombre' => 'Pozo 1', 'activo' => true],
            ['id' => 2, 'nombre' => 'Pozo 2', 'activo' => true],
            ['id' => 3, 'nombre' => 'Pozo 3', 'activo' => true],
        ];
        DB::table('lab_pozos')->insert($pozos);

        
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
        
        // Agregar configuración para Agua Cruda (Modulo 2)
        $camposAguaCruda = [11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25, 26, 27, 28];
        foreach ($camposAguaCruda as $tipoId) {
            $configuraciones[] = ['modulo_id' => 2, 'insumo_id' => null, 'pozo_id' => null, 'tipo_medicion_id' => $tipoId, 'activo' => true];
        }

        // Agregar configuración para Producto Terminado (Modulo 3)
        foreach ($camposAguaCruda as $tipoId) {
            $configuraciones[] = ['modulo_id' => 3, 'insumo_id' => null, 'pozo_id' => null, 'tipo_medicion_id' => $tipoId, 'activo' => true];
        }

        // Agregar configuración para Pozos (Modulo 4)
        foreach ($pozos as $pozo) {
            $configuraciones[] = ['modulo_id' => 4, 'insumo_id' => null, 'pozo_id' => $pozo['id'], 'tipo_medicion_id' => 29, 'activo' => true];
            $configuraciones[] = ['modulo_id' => 4, 'insumo_id' => null, 'pozo_id' => $pozo['id'], 'tipo_medicion_id' => 30, 'activo' => true];
        }

        DB::table('lab_mediciones')->insert($configuraciones);
    }
}
