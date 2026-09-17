<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\RegistroPresion;
use App\Models\RegistroFiltro;
use App\Models\NivelQuimico;
use App\Models\Novedad;
use App\Models\EstadoBomba;
use App\Models\EventoBomba;
use App\Models\CalidadAgua;
use App\Models\Caudalimetro;
use App\Models\EnsayoBacteriologico;
use Carbon\Carbon;
use Faker\Factory as Faker;

class OperadorQuimicoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Faker::create('es_ES');
        
        // Obtener o crear un usuario para asociar los registros
        $user = User::first();
        if (!$user) {
            $user = User::create([
                'name' => 'Operador Prueba',
                'email' => 'operador@aguas.com',
                'password' => bcrypt('password'),
                'role' => 'operador'
            ]);
        }

        // 1. Seed RegistroPresion
        for ($i = 0; $i < 50; $i++) {
            RegistroPresion::create([
                'user_id' => $user->id,
                'presion_tanque' => $faker->randomFloat(2, 0, 26),
                'presion_planta' => $faker->randomFloat(2, 0, 22),
                'presion_falcon' => $faker->randomFloat(2, 0, 12),
                'nivel_cisterna' => $faker->randomFloat(2, 0, 100),
                'created_at' => Carbon::now()->subDays(rand(0, 30))->subHours(rand(0, 23)),
                'updated_at' => Carbon::now()
            ]);
        }

        // 2. Seed RegistroFiltro
        for ($i = 0; $i < 30; $i++) {
            $inicioLavado = Carbon::now()->subDays(rand(0, 30))->subHours(rand(1, 23));
            $finLavado = (clone $inicioLavado)->addMinutes(rand(30, 240)); // Máximo 4 horas (240 min) de duración

            RegistroFiltro::create([
                'user_id' => $user->id,
                'norte_1' => $faker->boolean(20),
                'norte_2' => $faker->boolean(20),
                'norte_3' => $faker->boolean(20),
                'sur_1' => $faker->boolean(20),
                'sur_2' => $faker->boolean(20),
                'sur_3' => $faker->boolean(20),
                'inicio_lavado' => $inicioLavado,
                'fin_lavado' => $finLavado,
                'created_at' => $inicioLavado,
                'updated_at' => $inicioLavado
            ]);
        }

        // 3. Seed NivelQuimico
        $quimicos = ['cloro', 'poliamina', 'sulfato'];
        $tanques = ['principal', 'auxiliar'];

        foreach ($quimicos as $quimico) {
            foreach ($tanques as $tanque) {
                // Crear unos cuantos registros históricos de niveles para cada tanque
                $nivelActual = $faker->randomFloat(2, 20, 100);
                for ($i = 0; $i < 10; $i++) {
                    NivelQuimico::create([
                        'user_id' => $user->id,
                        'quimico' => $quimico,
                        'tipo_tanque' => $tanque,
                        'nivel' => $nivelActual,
                        'created_at' => Carbon::now()->subDays(10 - $i),
                        'updated_at' => Carbon::now()->subDays(10 - $i)
                    ]);
                    // Simular consumo bajando el nivel o recarga si baja mucho
                    $nivelActual -= $faker->randomFloat(2, 1, 10);
                    if ($nivelActual < 10) {
                        $nivelActual = $faker->randomFloat(2, 80, 100);
                    }
                }
            }
        }

        // 4. Seed Novedad
        for ($i = 0; $i < 15; $i++) {
            Novedad::create([
                'user_id' => $user->id,
                'mensaje' => $faker->realText(rand(50, 200)),
                'created_at' => Carbon::now()->subDays(rand(0, 10)),
                'updated_at' => Carbon::now()->subDays(rand(0, 10))
            ]);
        }

        // 5. Seed EstadoBomba y Eventos
        $dispositivos = ['bomba_1', 'bomba_2', 'bomba_3', 'pozo_norte', 'pozo_sur'];
        
        // Simular que bomba_1 y pozo_norte están encendidos, el resto apagados
        // Respetando validación de max 2 bombas de rio encendidas
        foreach ($dispositivos as $dispositivo) {
            $encendido = in_array($dispositivo, ['bomba_1', 'pozo_norte']);
            
            EstadoBomba::updateOrCreate(
                ['dispositivo' => $dispositivo],
                ['estado' => $encendido, 'user_id' => $user->id]
            );

            // Crear algunos eventos históricos
            for ($i = 0; $i < 5; $i++) {
                $encendidoAt = Carbon::now()->subDays(rand(1, 10))->subHours(rand(2, 10));
                $apagadoAt = (clone $encendidoAt)->addHours(rand(1, 8));
                
                EventoBomba::create([
                    'dispositivo' => $dispositivo,
                    'user_id' => $user->id,
                    'encendido_at' => $encendidoAt,
                    'apagado_at' => $apagadoAt,
                    'duracion_segundos' => $encendidoAt->diffInSeconds($apagadoAt)
                ]);
            }
            
            // Si actualmente está encendido, abrimos un evento
            if ($encendido) {
                EventoBomba::create([
                    'dispositivo' => $dispositivo,
                    'user_id' => $user->id,
                    'encendido_at' => Carbon::now()->subHours(2),
                    'apagado_at' => null,
                ]);
            }
        }

        // 6. Seed CalidadAgua (Módulo Químico)
        $lugaresCalidad = [
            ['lugar' => 'DECANTADOR NORTE', 'max_turb' => 300, 'has_cloro' => false, 'has_filtro' => false],
            ['lugar' => 'DECANTADOR SUR', 'max_turb' => 300, 'has_cloro' => false, 'has_filtro' => false],
            ['lugar' => 'CISTERNA', 'max_turb' => 10, 'has_cloro' => true, 'has_filtro' => false],
            ['lugar' => 'BAJADA DE TANQUE', 'max_turb' => 10, 'has_cloro' => true, 'has_filtro' => false],
            ['lugar' => 'RIO', 'max_turb' => 300, 'has_cloro' => false, 'has_filtro' => false],
            ['lugar' => 'FILTRO LINEA NORTE', 'max_turb' => 50, 'has_cloro' => false, 'has_filtro' => true],
            ['lugar' => 'FILTRO LINEA SUR', 'max_turb' => 50, 'has_cloro' => false, 'has_filtro' => true],
        ];

        foreach ($lugaresCalidad as $lugarData) {
            for ($i = 0; $i < 10; $i++) {
                $data = [
                    'user_id' => $user->id,
                    'lugar' => $lugarData['lugar'],
                    'turbiedad' => $faker->randomFloat(2, 0, $lugarData['max_turb']),
                    'ph' => $faker->randomFloat(2, 6, 8),
                    'created_at' => Carbon::now()->subDays(rand(0, 10)),
                    'updated_at' => Carbon::now()
                ];
                if ($lugarData['has_cloro']) {
                    $data['cloro_residual'] = $faker->randomFloat(2, 0, 3);
                }
                if ($lugarData['has_filtro']) {
                    $data['filtro_numero'] = $faker->randomElement(['Filtro 1', 'Filtro 2', 'Filtro 3']);
                }
                CalidadAgua::create($data);
            }
        }

        // 7. Seed EnsayoBacteriologico (Módulo Químico)
        $lugaresBact = ['CISTERNA', 'BAJADA DE TANQUE', 'RIO', 'DECANTADOR NORTE', 'DECANTADOR SUR'];
        foreach ($lugaresBact as $lugar) {
            for ($i = 0; $i < 5; $i++) {
                EnsayoBacteriologico::create([
                    'user_id' => $user->id,
                    'lugar' => $lugar,
                    'e_coli' => $faker->numberBetween(0, 50),
                    'coliformes_totales' => $faker->numberBetween(0, 200),
                    'created_at' => Carbon::now()->subDays(rand(0, 10)),
                    'updated_at' => Carbon::now()
                ]);
            }
        }

        // 8. Seed Caudalimetro (Módulo Químico)
        $bombasCaudal = ['sulfato', 'cloro'];
        foreach ($bombasCaudal as $bomba) {
            for ($i = 0; $i < 15; $i++) {
                Caudalimetro::create([
                    'user_id' => $user->id,
                    'bomba' => $bomba,
                    'caudal_m3h' => $faker->randomFloat(2, 0, 500),
                    'created_at' => Carbon::now()->subDays(rand(0, 10)),
                    'updated_at' => Carbon::now()
                ]);
            }
        }
    }
}
