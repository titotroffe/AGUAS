<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\CalidadAgua;
use App\Models\User;
use Carbon\Carbon;

class CalidadAguaFalsaSeeder extends Seeder
{
    public function run()
    {
        $user = User::first();
        $userId = $user ? $user->id : 1;
        
        $now = Carbon::now();
        
        $lugaresBase = [
            'RIO' => ['turb' => [50, 100]],
            'DECANTADOR NORTE' => ['turb' => [20, 40]],
            'DECANTADOR SUR' => ['turb' => [20, 40]],
            'FILTRO LINEA NORTE_Filtro 1' => ['turb' => [1, 5], 'filtro' => 'Filtro 1', 'base' => 'FILTRO LINEA NORTE'],
            'FILTRO LINEA NORTE_Filtro 2' => ['turb' => [1, 5], 'filtro' => 'Filtro 2', 'base' => 'FILTRO LINEA NORTE'],
            'FILTRO LINEA NORTE_Filtro 3' => ['turb' => [1, 5], 'filtro' => 'Filtro 3', 'base' => 'FILTRO LINEA NORTE'],
            'FILTRO LINEA SUR_Filtro 1' => ['turb' => [1, 5], 'filtro' => 'Filtro 1', 'base' => 'FILTRO LINEA SUR'],
            'FILTRO LINEA SUR_Filtro 2' => ['turb' => [1, 5], 'filtro' => 'Filtro 2', 'base' => 'FILTRO LINEA SUR'],
            'FILTRO LINEA SUR_Filtro 3' => ['turb' => [1, 5], 'filtro' => 'Filtro 3', 'base' => 'FILTRO LINEA SUR'],
            'CISTERNA' => ['turb' => [0.5, 2.0], 'cloro' => [1.0, 2.5]],
        ];

        // Generate data for the last 24 hours, every 2 hours (12 points per location)
        for ($i = 24; $i >= 0; $i -= 2) {
            $timestamp = $now->copy()->subHours($i);
            
            foreach ($lugaresBase as $key => $config) {
                
                $turbiedad = rand($config['turb'][0] * 100, $config['turb'][1] * 100) / 100;
                $ph = rand(650, 850) / 100; // 6.5 to 8.5
                $cloro = isset($config['cloro']) ? rand($config['cloro'][0] * 100, $config['cloro'][1] * 100) / 100 : null;
                
                $lugar = isset($config['base']) ? $config['base'] : $key;
                $filtro_numero = isset($config['filtro']) ? $config['filtro'] : null;

                CalidadAgua::create([
                    'user_id' => $userId,
                    'lugar' => $lugar,
                    'filtro_numero' => $filtro_numero,
                    'turbiedad' => $turbiedad,
                    'ph' => $ph,
                    'cloro_residual' => $cloro,
                    'created_at' => $timestamp,
                    'updated_at' => $timestamp,
                ]);
            }
        }
    }
}
