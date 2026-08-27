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
        foreach ($modulos as $item) {
            DB::table('lab_modulos')->updateOrInsert(['id' => $item['id']], $item);
        }

        // Insumos
        $insumos = [
            ['id' => 1, 'nombre' => 'Sulfato de Aluminio'],
            ['id' => 2, 'nombre' => 'Hipoclorito de Sodio'],
            ['id' => 3, 'nombre' => 'Poliamina'],
            ['id' => 4, 'nombre' => 'Cal Hidráulica'],
        ];
        foreach ($insumos as $item) {
            DB::table('lab_insumos')->updateOrInsert(['id' => $item['id']], $item);
        }

        // Tipos de Medición
        $tiposMedicion = [
            // Insumos (1-10)
            ['id' => 1, 'nombre' => 'Residuo Insoluble', 'unidad' => null, 'categoria' => 'INSUMOS', 'es_texto' => false, 'es_booleano' => false, 'tipo_campo' => 'number'],
            ['id' => 2, 'nombre' => 'Óxido Ferroso', 'unidad' => null, 'categoria' => 'INSUMOS', 'es_texto' => false, 'es_booleano' => false, 'tipo_campo' => 'number'],
            ['id' => 3, 'nombre' => 'Óxido Férrico', 'unidad' => null, 'categoria' => 'INSUMOS', 'es_texto' => false, 'es_booleano' => false, 'tipo_campo' => 'number'],
            ['id' => 4, 'nombre' => 'Óxido de Aluminio', 'unidad' => null, 'categoria' => 'INSUMOS', 'es_texto' => false, 'es_booleano' => false, 'tipo_campo' => 'number'],
            ['id' => 5, 'nombre' => 'Óxidos Útiles', 'unidad' => null, 'categoria' => 'INSUMOS', 'es_texto' => false, 'es_booleano' => false, 'tipo_campo' => 'number'],
            ['id' => 6, 'nombre' => 'Manganeso', 'unidad' => null, 'categoria' => 'INSUMOS', 'es_texto' => false, 'es_booleano' => false, 'tipo_campo' => 'number'],
            ['id' => 7, 'nombre' => 'Densidad A 20°C', 'unidad' => null, 'categoria' => 'INSUMOS', 'es_texto' => false, 'es_booleano' => false, 'tipo_campo' => 'number'],
            ['id' => 8, 'nombre' => 'Cloro Activo', 'unidad' => null, 'categoria' => 'INSUMOS', 'es_texto' => false, 'es_booleano' => false, 'tipo_campo' => 'number'],
            ['id' => 9, 'nombre' => 'Peso Litro', 'unidad' => null, 'categoria' => 'INSUMOS', 'es_texto' => false, 'es_booleano' => false, 'tipo_campo' => 'number'],
            ['id' => 10, 'nombre' => 'Preparación Archivo Contramuestra', 'unidad' => null, 'categoria' => 'INSUMOS', 'es_texto' => false, 'es_booleano' => true, 'tipo_campo' => 'boolean'],

            // Agua Cruda / Producto Terminado - FISICOQUÍMICO (11-24)
            ['id' => 11, 'nombre' => 'Color', 'unidad' => null, 'categoria' => 'FISICOQUÍMICO', 'es_texto' => true, 'es_booleano' => false, 'tipo_campo' => 'text'],
            ['id' => 12, 'nombre' => 'Olor', 'unidad' => null, 'categoria' => 'FISICOQUÍMICO', 'es_texto' => true, 'es_booleano' => false, 'tipo_campo' => 'text'],
            ['id' => 13, 'nombre' => 'Sabor', 'unidad' => null, 'categoria' => 'FISICOQUÍMICO', 'es_texto' => true, 'es_booleano' => false, 'tipo_campo' => 'text'],
            ['id' => 14, 'nombre' => 'Turbiedad', 'unidad' => 'NTU', 'categoria' => 'FISICOQUÍMICO', 'es_texto' => false, 'es_booleano' => false, 'tipo_campo' => 'number'],
            ['id' => 15, 'nombre' => 'Aluminio', 'unidad' => 'mg/L', 'categoria' => 'FISICOQUÍMICO', 'es_texto' => false, 'es_booleano' => false, 'tipo_campo' => 'number'],
            ['id' => 16, 'nombre' => 'Cloruro', 'unidad' => 'mg/L', 'categoria' => 'FISICOQUÍMICO', 'es_texto' => false, 'es_booleano' => false, 'tipo_campo' => 'number'],
            ['id' => 17, 'nombre' => 'Hierro', 'unidad' => 'mg/L', 'categoria' => 'FISICOQUÍMICO', 'es_texto' => false, 'es_booleano' => false, 'tipo_campo' => 'number'],
            ['id' => 18, 'nombre' => 'pH', 'unidad' => null, 'categoria' => 'FISICOQUÍMICO', 'es_texto' => false, 'es_booleano' => false, 'tipo_campo' => 'number'],
            ['id' => 19, 'nombre' => 'Sulfato', 'unidad' => 'mg/L', 'categoria' => 'FISICOQUÍMICO', 'es_texto' => false, 'es_booleano' => false, 'tipo_campo' => 'number'],
            ['id' => 20, 'nombre' => 'Sólidos Disueltos Totales', 'unidad' => 'mg/L', 'categoria' => 'FISICOQUÍMICO', 'es_texto' => false, 'es_booleano' => false, 'tipo_campo' => 'number'],
            ['id' => 21, 'nombre' => 'Mercurio', 'unidad' => 'mg/L', 'categoria' => 'FISICOQUÍMICO', 'es_texto' => false, 'es_booleano' => false, 'tipo_campo' => 'number'],
            ['id' => 22, 'nombre' => 'Cadmio', 'unidad' => 'mg/L', 'categoria' => 'FISICOQUÍMICO', 'es_texto' => false, 'es_booleano' => false, 'tipo_campo' => 'number'],
            ['id' => 23, 'nombre' => 'Arsénico', 'unidad' => 'mg/L', 'categoria' => 'FISICOQUÍMICO', 'es_texto' => false, 'es_booleano' => false, 'tipo_campo' => 'number'],
            ['id' => 24, 'nombre' => 'Cromo', 'unidad' => 'mg/L', 'categoria' => 'FISICOQUÍMICO', 'es_texto' => false, 'es_booleano' => false, 'tipo_campo' => 'number'],

            // BACTERIOLOGÍA Y BIOLOGÍA (25-28)
            ['id' => 25, 'nombre' => 'Bacterias Aerobias Heterótrofas', 'unidad' => 'UFC/mL', 'categoria' => 'BACTERIOLOGÍA Y BIOLOGÍA', 'es_texto' => true, 'es_booleano' => false, 'tipo_campo' => 'text'],
            ['id' => 26, 'nombre' => 'Pseudomona Aeruginosa', 'unidad' => null, 'categoria' => 'BACTERIOLOGÍA Y BIOLOGÍA', 'es_texto' => true, 'es_booleano' => false, 'tipo_campo' => 'text'],
            ['id' => 27, 'nombre' => 'Giardia Lamblia', 'unidad' => null, 'categoria' => 'BACTERIOLOGÍA Y BIOLOGÍA', 'es_texto' => true, 'es_booleano' => false, 'tipo_campo' => 'text'],
            ['id' => 28, 'nombre' => 'Fitoplancton / Zooplancton', 'unidad' => null, 'categoria' => 'BACTERIOLOGÍA Y BIOLOGÍA', 'es_texto' => true, 'es_booleano' => false, 'tipo_campo' => 'text'],

            // Pozos (29-30)
            ['id' => 29, 'nombre' => 'Coliformes Totales', 'unidad' => 'NMP/100mL', 'categoria' => 'BACTERIOLOGÍA Y BIOLOGÍA', 'es_texto' => true, 'es_booleano' => false, 'tipo_campo' => 'text'],
            ['id' => 30, 'nombre' => 'E. Coli / Coliformes Fecales', 'unidad' => 'NMP/100mL', 'categoria' => 'BACTERIOLOGÍA Y BIOLOGÍA', 'es_texto' => true, 'es_booleano' => false, 'tipo_campo' => 'text'],
        ];
        foreach ($tiposMedicion as $item) {
            DB::table('lab_tipos_medicion')->updateOrInsert(['id' => $item['id']], $item);
        }

        // Pozos Catálogo
        $pozos = [
            ['id' => 1, 'nombre' => 'Pozo 1', 'activo' => true],
            ['id' => 2, 'nombre' => 'Pozo 2', 'activo' => true],
            ['id' => 3, 'nombre' => 'Pozo 3', 'activo' => true],
        ];
        foreach ($pozos as $item) {
            DB::table('lab_pozos')->updateOrInsert(['id' => $item['id']], $item);
        }

        $configuraciones = [
            // Sulfato
            ['modulo_id' => 1, 'insumo_id' => 1, 'pozo_id' => null, 'tipo_medicion_id' => 10, 'activo' => true, 'min' => null, 'max' => null],
            ['modulo_id' => 1, 'insumo_id' => 1, 'pozo_id' => null, 'tipo_medicion_id' => 1, 'activo' => true, 'min' => 0, 'max' => 1000],
            ['modulo_id' => 1, 'insumo_id' => 1, 'pozo_id' => null, 'tipo_medicion_id' => 2, 'activo' => true, 'min' => 0, 'max' => 1000],
            ['modulo_id' => 1, 'insumo_id' => 1, 'pozo_id' => null, 'tipo_medicion_id' => 3, 'activo' => true, 'min' => 0, 'max' => 1000],
            ['modulo_id' => 1, 'insumo_id' => 1, 'pozo_id' => null, 'tipo_medicion_id' => 4, 'activo' => true, 'min' => 0, 'max' => 1000],
            ['modulo_id' => 1, 'insumo_id' => 1, 'pozo_id' => null, 'tipo_medicion_id' => 5, 'activo' => true, 'min' => 0, 'max' => 1000],
            ['modulo_id' => 1, 'insumo_id' => 1, 'pozo_id' => null, 'tipo_medicion_id' => 6, 'activo' => true, 'min' => 0, 'max' => 1000],
            ['modulo_id' => 1, 'insumo_id' => 1, 'pozo_id' => null, 'tipo_medicion_id' => 7, 'activo' => true, 'min' => 0, 'max' => 1000],
            
            // Hipoclorito
            ['modulo_id' => 1, 'insumo_id' => 2, 'pozo_id' => null, 'tipo_medicion_id' => 10, 'activo' => true, 'min' => null, 'max' => null],
            ['modulo_id' => 1, 'insumo_id' => 2, 'pozo_id' => null, 'tipo_medicion_id' => 8, 'activo' => true, 'min' => 0, 'max' => 1000],
            ['modulo_id' => 1, 'insumo_id' => 2, 'pozo_id' => null, 'tipo_medicion_id' => 7, 'activo' => true, 'min' => 0, 'max' => 1000],

            // Poliamina
            ['modulo_id' => 1, 'insumo_id' => 3, 'pozo_id' => null, 'tipo_medicion_id' => 10, 'activo' => true, 'min' => null, 'max' => null],
            ['modulo_id' => 1, 'insumo_id' => 3, 'pozo_id' => null, 'tipo_medicion_id' => 7, 'activo' => true, 'min' => 0, 'max' => 1000],

            // Cal
            ['modulo_id' => 1, 'insumo_id' => 4, 'pozo_id' => null, 'tipo_medicion_id' => 10, 'activo' => true, 'min' => null, 'max' => null],
            ['modulo_id' => 1, 'insumo_id' => 4, 'pozo_id' => null, 'tipo_medicion_id' => 9, 'activo' => true, 'min' => 0, 'max' => 1000],
        ];
        
        // Configuración para Agua Cruda (Modulo 2)
        $camposAguaCruda = [11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25, 26, 27, 28];
        foreach ($camposAguaCruda as $tipoId) {
            $min = ($tipoId == 18) ? 0 : 0;
            $max = ($tipoId == 18) ? 14 : 1000;
            $configuraciones[] = ['modulo_id' => 2, 'insumo_id' => null, 'pozo_id' => null, 'tipo_medicion_id' => $tipoId, 'activo' => true, 'min' => $min, 'max' => $max];
        }

        // Configuración para Producto Terminado (Modulo 3)
        foreach ($camposAguaCruda as $tipoId) {
            $min = ($tipoId == 18) ? 0 : 0;
            $max = ($tipoId == 18) ? 14 : 1000;
            $configuraciones[] = ['modulo_id' => 3, 'insumo_id' => null, 'pozo_id' => null, 'tipo_medicion_id' => $tipoId, 'activo' => true, 'min' => $min, 'max' => $max];
        }

        // Configuración para Pozos (Modulo 4)
        foreach ($pozos as $pozo) {
            $configuraciones[] = ['modulo_id' => 4, 'insumo_id' => null, 'pozo_id' => $pozo['id'], 'tipo_medicion_id' => 29, 'activo' => true, 'min' => null, 'max' => null];
            $configuraciones[] = ['modulo_id' => 4, 'insumo_id' => null, 'pozo_id' => $pozo['id'], 'tipo_medicion_id' => 30, 'activo' => true, 'min' => null, 'max' => null];
        }

        foreach ($configuraciones as $config) {
            DB::table('lab_mediciones')->updateOrInsert(
                [
                    'modulo_id' => $config['modulo_id'],
                    'insumo_id' => $config['insumo_id'],
                    'pozo_id' => $config['pozo_id'],
                    'tipo_medicion_id' => $config['tipo_medicion_id']
                ],
                $config
            );
        }
    }
}
