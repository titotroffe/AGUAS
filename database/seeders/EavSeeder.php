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

            // Trimestrales - Metales Pesados e Inorganicos (31-43)
            ['id' => 31, 'nombre' => 'Arsénico [As]', 'unidad' => null, 'categoria' => 'FISICOQUÍMICO', 'es_texto' => false, 'es_booleano' => false, 'tipo_campo' => 'number'],
            ['id' => 32, 'nombre' => 'Cadmio [Cd]', 'unidad' => null, 'categoria' => 'FISICOQUÍMICO', 'es_texto' => false, 'es_booleano' => false, 'tipo_campo' => 'number'],
            ['id' => 33, 'nombre' => 'Cianuro [CN⁻]', 'unidad' => null, 'categoria' => 'FISICOQUÍMICO', 'es_texto' => false, 'es_booleano' => false, 'tipo_campo' => 'number'],
            ['id' => 34, 'nombre' => 'Cobre [Cu]', 'unidad' => null, 'categoria' => 'FISICOQUÍMICO', 'es_texto' => false, 'es_booleano' => false, 'tipo_campo' => 'number'],
            ['id' => 35, 'nombre' => 'Cromo Total [Cr Total]', 'unidad' => null, 'categoria' => 'FISICOQUÍMICO', 'es_texto' => false, 'es_booleano' => false, 'tipo_campo' => 'number'],
            ['id' => 36, 'nombre' => 'Fluor [F]', 'unidad' => null, 'categoria' => 'FISICOQUÍMICO', 'es_texto' => false, 'es_booleano' => false, 'tipo_campo' => 'number'],
            ['id' => 37, 'nombre' => 'Manganeso [Mn]', 'unidad' => null, 'categoria' => 'FISICOQUÍMICO', 'es_texto' => false, 'es_booleano' => false, 'tipo_campo' => 'number'],
            ['id' => 38, 'nombre' => 'Mercurio Total [Hg Total]', 'unidad' => null, 'categoria' => 'FISICOQUÍMICO', 'es_texto' => false, 'es_booleano' => false, 'tipo_campo' => 'number'],
            ['id' => 39, 'nombre' => 'Nitrato (como NO₃⁻)', 'unidad' => null, 'categoria' => 'FISICOQUÍMICO', 'es_texto' => false, 'es_booleano' => false, 'tipo_campo' => 'number'],
            ['id' => 40, 'nombre' => 'Nitrito (como NO₂⁻)', 'unidad' => null, 'categoria' => 'FISICOQUÍMICO', 'es_texto' => false, 'es_booleano' => false, 'tipo_campo' => 'number'],
            ['id' => 41, 'nombre' => 'Plomo [Pb]', 'unidad' => null, 'categoria' => 'FISICOQUÍMICO', 'es_texto' => false, 'es_booleano' => false, 'tipo_campo' => 'number'],
            ['id' => 42, 'nombre' => 'Selenio [Se]', 'unidad' => null, 'categoria' => 'FISICOQUÍMICO', 'es_texto' => false, 'es_booleano' => false, 'tipo_campo' => 'number'],
            ['id' => 43, 'nombre' => 'Plata [Ag]', 'unidad' => null, 'categoria' => 'FISICOQUÍMICO', 'es_texto' => false, 'es_booleano' => false, 'tipo_campo' => 'number'],

            // Trimestrales - Alcanos Clorados (44-45)
            ['id' => 44, 'nombre' => 'Alcanos Clorados: 1,2 Dicloroetano', 'unidad' => null, 'categoria' => 'FISICOQUÍMICO', 'es_texto' => false, 'es_booleano' => false, 'tipo_campo' => 'number'],
            ['id' => 45, 'nombre' => 'Alcanos Clorados: Tetracloruro de carbono', 'unidad' => null, 'categoria' => 'FISICOQUÍMICO', 'es_texto' => false, 'es_booleano' => false, 'tipo_campo' => 'number'],

            // Trimestrales - Etenos Clorados (46-48)
            ['id' => 46, 'nombre' => 'Etenos Clorados: 1.1 Dicloroeteno', 'unidad' => null, 'categoria' => 'FISICOQUÍMICO', 'es_texto' => false, 'es_booleano' => false, 'tipo_campo' => 'number'],
            ['id' => 47, 'nombre' => 'Etenos Clorados: Tricloroeteno', 'unidad' => null, 'categoria' => 'FISICOQUÍMICO', 'es_texto' => false, 'es_booleano' => false, 'tipo_campo' => 'number'],
            ['id' => 48, 'nombre' => 'Etenos Clorados: Tetracloroeteno', 'unidad' => null, 'categoria' => 'FISICOQUÍMICO', 'es_texto' => false, 'es_booleano' => false, 'tipo_campo' => 'number'],

            // Trimestrales - Hidrocarburos Aromaticos (49-50)
            ['id' => 49, 'nombre' => 'Hidrocarburos Aromáticos: Benceno', 'unidad' => null, 'categoria' => 'FISICOQUÍMICO', 'es_texto' => false, 'es_booleano' => false, 'tipo_campo' => 'number'],
            ['id' => 50, 'nombre' => 'Hidrocarburos Aromáticos: Benzo (a) pireno', 'unidad' => null, 'categoria' => 'FISICOQUÍMICO', 'es_texto' => false, 'es_booleano' => false, 'tipo_campo' => 'number'],

            // Trimestrales - Pesticidas (51-59)
            ['id' => 51, 'nombre' => 'Pesticidas: Aldrín/Dieldrín', 'unidad' => null, 'categoria' => 'FISICOQUÍMICO', 'es_texto' => false, 'es_booleano' => false, 'tipo_campo' => 'number'],
            ['id' => 52, 'nombre' => 'Pesticidas: Clordano (total isómeros)', 'unidad' => null, 'categoria' => 'FISICOQUÍMICO', 'es_texto' => false, 'es_booleano' => false, 'tipo_campo' => 'number'],
            ['id' => 53, 'nombre' => 'Pesticidas: 2,4D (ácido dicloro-fenoxiacético)', 'unidad' => null, 'categoria' => 'FISICOQUÍMICO', 'es_texto' => false, 'es_booleano' => false, 'tipo_campo' => 'number'],
            ['id' => 54, 'nombre' => 'Pesticidas: DDT (total isómeros)', 'unidad' => null, 'categoria' => 'FISICOQUÍMICO', 'es_texto' => false, 'es_booleano' => false, 'tipo_campo' => 'number'],
            ['id' => 55, 'nombre' => 'Pesticidas: Heptacloro y Heptacloroepóxido', 'unidad' => null, 'categoria' => 'FISICOQUÍMICO', 'es_texto' => false, 'es_booleano' => false, 'tipo_campo' => 'number'],
            ['id' => 56, 'nombre' => 'Pesticidas: Hexaclorobenceno', 'unidad' => null, 'categoria' => 'FISICOQUÍMICO', 'es_texto' => false, 'es_booleano' => false, 'tipo_campo' => 'number'],
            ['id' => 57, 'nombre' => 'Pesticidas: Lindano', 'unidad' => null, 'categoria' => 'FISICOQUÍMICO', 'es_texto' => false, 'es_booleano' => false, 'tipo_campo' => 'number'],
            ['id' => 58, 'nombre' => 'Pesticidas: Metoxicloro', 'unidad' => null, 'categoria' => 'FISICOQUÍMICO', 'es_texto' => false, 'es_booleano' => false, 'tipo_campo' => 'number'],
            ['id' => 59, 'nombre' => 'Pesticidas: Pentaclorofenol', 'unidad' => null, 'categoria' => 'FISICOQUÍMICO', 'es_texto' => false, 'es_booleano' => false, 'tipo_campo' => 'number'],

            // Trimestrales - Desinfectantes y subproductos (60-61)
            ['id' => 60, 'nombre' => 'Desinfectantes: Cloro (libre residual)', 'unidad' => null, 'categoria' => 'FISICOQUÍMICO', 'es_texto' => false, 'es_booleano' => false, 'tipo_campo' => 'number'],
            ['id' => 61, 'nombre' => 'Desinfectantes: Monocloramina', 'unidad' => null, 'categoria' => 'FISICOQUÍMICO', 'es_texto' => false, 'es_booleano' => false, 'tipo_campo' => 'number'],

            // Trimestrales - Clorfenoles (62)
            ['id' => 62, 'nombre' => 'Clorfenoles: 2,4,6 - Tricorofenol', 'unidad' => null, 'categoria' => 'FISICOQUÍMICO', 'es_texto' => false, 'es_booleano' => false, 'tipo_campo' => 'number'],

            // Trimestrales - Trihalometanos (63-65)
            ['id' => 63, 'nombre' => 'Trihalometanos: Bromoformo', 'unidad' => null, 'categoria' => 'FISICOQUÍMICO', 'es_texto' => false, 'es_booleano' => false, 'tipo_campo' => 'number'],
            ['id' => 64, 'nombre' => 'Trihalometanos: Dibromoclorometano', 'unidad' => null, 'categoria' => 'FISICOQUÍMICO', 'es_texto' => false, 'es_booleano' => false, 'tipo_campo' => 'number'],
            ['id' => 65, 'nombre' => 'Trihalometanos: Bromodiclorometano', 'unidad' => null, 'categoria' => 'FISICOQUÍMICO', 'es_texto' => false, 'es_booleano' => false, 'tipo_campo' => 'number'],

            // Mensual Tratamiento - Nuevos Fisicoquimicos (66-74)
            ['id' => 66, 'nombre' => 'Zinc', 'unidad' => 'mg/L', 'categoria' => 'FISICOQUÍMICO', 'es_texto' => false, 'es_booleano' => false, 'tipo_campo' => 'number'],
            ['id' => 67, 'nombre' => 'Sodio', 'unidad' => 'mg/L', 'categoria' => 'FISICOQUÍMICO', 'es_texto' => false, 'es_booleano' => false, 'tipo_campo' => 'number'],
            ['id' => 68, 'nombre' => 'Detergentes sintéticos', 'unidad' => 'mg/L', 'categoria' => 'FISICOQUÍMICO', 'es_texto' => false, 'es_booleano' => false, 'tipo_campo' => 'number'],
            ['id' => 69, 'nombre' => 'Plomo', 'unidad' => 'mg/L', 'categoria' => 'FISICOQUÍMICO', 'es_texto' => false, 'es_booleano' => false, 'tipo_campo' => 'number'],
            ['id' => 70, 'nombre' => 'DBO', 'unidad' => 'mg/L', 'categoria' => 'FISICOQUÍMICO', 'es_texto' => false, 'es_booleano' => false, 'tipo_campo' => 'number'],
            ['id' => 71, 'nombre' => 'DQO', 'unidad' => 'mg/L', 'categoria' => 'FISICOQUÍMICO', 'es_texto' => false, 'es_booleano' => false, 'tipo_campo' => 'number'],
            ['id' => 72, 'nombre' => 'Clorfenoles: 2, 4, 6 - Tricorofenol', 'unidad' => null, 'categoria' => 'FISICOQUÍMICO', 'es_texto' => false, 'es_booleano' => false, 'tipo_campo' => 'number'],
            ['id' => 73, 'nombre' => 'Hidrocarburos: Benzeno', 'unidad' => null, 'categoria' => 'FISICOQUÍMICO', 'es_texto' => false, 'es_booleano' => false, 'tipo_campo' => 'number'],
            ['id' => 74, 'nombre' => 'Hidrocarburos: Benzeno (a) pireno', 'unidad' => null, 'categoria' => 'FISICOQUÍMICO', 'es_texto' => false, 'es_booleano' => false, 'tipo_campo' => 'number'],
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
            ['modulo_id' => 1, 'insumo_id' => 1, 'pozo_id' => null, 'frecuencia_id' => null, 'tipo_medicion_id' => 10, 'activo' => true, 'min' => null, 'max' => null],
            ['modulo_id' => 1, 'insumo_id' => 1, 'pozo_id' => null, 'frecuencia_id' => null, 'tipo_medicion_id' => 1, 'activo' => true, 'min' => 0, 'max' => 1000],
            ['modulo_id' => 1, 'insumo_id' => 1, 'pozo_id' => null, 'frecuencia_id' => null, 'tipo_medicion_id' => 2, 'activo' => true, 'min' => 0, 'max' => 1000],
            ['modulo_id' => 1, 'insumo_id' => 1, 'pozo_id' => null, 'frecuencia_id' => null, 'tipo_medicion_id' => 3, 'activo' => true, 'min' => 0, 'max' => 1000],
            ['modulo_id' => 1, 'insumo_id' => 1, 'pozo_id' => null, 'frecuencia_id' => null, 'tipo_medicion_id' => 4, 'activo' => true, 'min' => 0, 'max' => 1000],
            ['modulo_id' => 1, 'insumo_id' => 1, 'pozo_id' => null, 'frecuencia_id' => null, 'tipo_medicion_id' => 5, 'activo' => true, 'min' => 0, 'max' => 1000],
            ['modulo_id' => 1, 'insumo_id' => 1, 'pozo_id' => null, 'frecuencia_id' => null, 'tipo_medicion_id' => 6, 'activo' => true, 'min' => 0, 'max' => 1000],
            ['modulo_id' => 1, 'insumo_id' => 1, 'pozo_id' => null, 'frecuencia_id' => null, 'tipo_medicion_id' => 7, 'activo' => true, 'min' => 0, 'max' => 1000],
            
            // Hipoclorito
            ['modulo_id' => 1, 'insumo_id' => 2, 'pozo_id' => null, 'frecuencia_id' => null, 'tipo_medicion_id' => 10, 'activo' => true, 'min' => null, 'max' => null],
            ['modulo_id' => 1, 'insumo_id' => 2, 'pozo_id' => null, 'frecuencia_id' => null, 'tipo_medicion_id' => 8, 'activo' => true, 'min' => 0, 'max' => 1000],
            ['modulo_id' => 1, 'insumo_id' => 2, 'pozo_id' => null, 'frecuencia_id' => null, 'tipo_medicion_id' => 7, 'activo' => true, 'min' => 0, 'max' => 1000],

            // Poliamina
            ['modulo_id' => 1, 'insumo_id' => 3, 'pozo_id' => null, 'frecuencia_id' => null, 'tipo_medicion_id' => 10, 'activo' => true, 'min' => null, 'max' => null],
            ['modulo_id' => 1, 'insumo_id' => 3, 'pozo_id' => null, 'frecuencia_id' => null, 'tipo_medicion_id' => 7, 'activo' => true, 'min' => 0, 'max' => 1000],

            // Cal
            ['modulo_id' => 1, 'insumo_id' => 4, 'pozo_id' => null, 'frecuencia_id' => null, 'tipo_medicion_id' => 10, 'activo' => true, 'min' => null, 'max' => null],
            ['modulo_id' => 1, 'insumo_id' => 4, 'pozo_id' => null, 'frecuencia_id' => null, 'tipo_medicion_id' => 9, 'activo' => true, 'min' => 0, 'max' => 1000],
        ];
        
        // Configuración para Agua Cruda (Modulo 2) - MENSUAL (11-28, 66-74)
        $camposAguaCrudaMensual = [11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25, 26, 27, 28, 66, 67, 68, 69, 70, 71, 72, 73, 74];
        foreach ($camposAguaCrudaMensual as $tipoId) {
            $min = ($tipoId == 18) ? 0 : 0;
            $max = ($tipoId == 18) ? 14 : 1000;
            $configuraciones[] = ['modulo_id' => 2, 'insumo_id' => null, 'pozo_id' => null, 'frecuencia_id' => 1, 'tipo_medicion_id' => $tipoId, 'activo' => true, 'min' => $min, 'max' => $max];
        }

        // Configuración para Agua Cruda (Modulo 2) - TRIMESTRAL (31-65)
        // IDs eliminados por ser duplicados de Mensual/Semestral: 31 (As), 32 (Cd), 35 (Cr), 38 (Hg), 41 (Pb), 49 (Benceno), 50 (Benzo a pireno)
        $camposAguaCrudaTrimestral = array_diff(range(31, 65), [31, 32, 35, 38, 41, 49, 50]);
        foreach ($camposAguaCrudaTrimestral as $tipoId) {
            $configuraciones[] = ['modulo_id' => 2, 'insumo_id' => null, 'pozo_id' => null, 'frecuencia_id' => 2, 'tipo_medicion_id' => $tipoId, 'activo' => true, 'min' => null, 'max' => null];
        }

        // Configuración para Producto Terminado (Modulo 3)
        foreach ($camposAguaCrudaMensual as $tipoId) {
            $min = ($tipoId == 18) ? 0 : 0;
            $max = ($tipoId == 18) ? 14 : 1000;
            // 21 (Mercurio), 22 (Cadmio), 23 (Arsénico), 24 (Cromo) son Semestrales para Modulo 3
            $frecuenciaProducto = in_array($tipoId, [21, 22, 23, 24]) ? 3 : 1;
            $configuraciones[] = ['modulo_id' => 3, 'insumo_id' => null, 'pozo_id' => null, 'frecuencia_id' => $frecuenciaProducto, 'tipo_medicion_id' => $tipoId, 'activo' => true, 'min' => $min, 'max' => $max];
        }
        foreach ($camposAguaCrudaTrimestral as $tipoId) {
            $configuraciones[] = ['modulo_id' => 3, 'insumo_id' => null, 'pozo_id' => null, 'frecuencia_id' => 2, 'tipo_medicion_id' => $tipoId, 'activo' => true, 'min' => null, 'max' => null];
        }

        // Configuración para Pozos (Modulo 4)
        foreach ($pozos as $pozo) {
            $configuraciones[] = ['modulo_id' => 4, 'insumo_id' => null, 'pozo_id' => $pozo['id'], 'frecuencia_id' => null, 'tipo_medicion_id' => 29, 'activo' => true, 'min' => null, 'max' => null];
            $configuraciones[] = ['modulo_id' => 4, 'insumo_id' => null, 'pozo_id' => $pozo['id'], 'frecuencia_id' => null, 'tipo_medicion_id' => 30, 'activo' => true, 'min' => null, 'max' => null];
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
