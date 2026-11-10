<?php

namespace App\Http\Controllers;

use App\Models\TelemetriaDosificadora;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;

class TelemetriaController extends Controller
{
    const CACHE_KEY_ACTUAL    = 'telemetria_dosificadora_actual';
    const CACHE_KEY_HISTORIAL = 'telemetria_dosificadora_historial';
    const CACHE_KEY_DB_STATE  = 'telemetria_dosificadora_db_state';
    const MAX_HISTORIAL       = 15;
    const INTERVALO_DB_SEG    = 300; // 5 minutos

    /**
     * Recibe los datos enviados por POST desde el ESP32 (/api/dosificadoras)
     */
    public function store(Request $request): JsonResponse
    {
        $payload = $request->all();
        if (empty($payload) && $request->getContent()) {
            $decoded = json_decode($request->getContent(), true);
            if (is_array($decoded)) {
                $payload = $decoded;
            }
        }

        $voltaje   = isset($payload['voltaje']) ? round((float) $payload['voltaje'], 1) : 0.0;
        $corriente = isset($payload['corriente']) ? round((float) $payload['corriente'], 3) : 0.0;
        $ahora     = now();

        $lectura = [
            'voltaje'   => $voltaje,
            'corriente' => $corriente,
            'hora'      => $ahora->format('H:i:s'),
            'fecha'     => $ahora->format('d/m/Y H:i:s'),
            'timestamp' => $ahora->timestamp,
            'ip_esp32'  => $request->ip(),
        ];

        // 1. Guardar última lectura en caché para tiempo real
        Cache::put(self::CACHE_KEY_ACTUAL, $lectura, now()->addHours(24));

        // 2. Guardar historial de las últimas 15 lecturas en vivo
        $historial = Cache::get(self::CACHE_KEY_HISTORIAL, []);
        array_unshift($historial, $lectura);
        $historial = array_slice($historial, 0, self::MAX_HISTORIAL);
        Cache::put(self::CACHE_KEY_HISTORIAL, $historial, now()->addHours(24));

        // 3. Guardado Híbrido en Base de Datos:
        //    - Instantáneo si entra en falla (cae a 0)
        //    - Instantáneo si se recupera de una falla
        //    - Periódico cada 5 minutos (300 s)
        $enFalla = ($voltaje <= 1.0 || $corriente <= 0.02);
        $dbState = Cache::get(self::CACHE_KEY_DB_STATE, [
            'ultimo_guardado_ts' => 0,
            'en_falla'           => null,
        ]);

        $enFallaPrevio    = $dbState['en_falla'];
        $ultimoGuardadoTs = (int) ($dbState['ultimo_guardado_ts'] ?? 0);
        $debeGuardar      = false;
        $tipoRegistro     = $enFalla ? 'falla' : 'rutina';

        if ($enFalla && $enFallaPrevio !== true) {
            // Cayó a 0: guardar de inmediato como falla
            $debeGuardar  = true;
            $tipoRegistro = 'falla';
        } elseif (!$enFalla && $enFallaPrevio === true) {
            // Volvió a valores normales tras una falla: guardar recuperación inmediata
            $debeGuardar  = true;
            $tipoRegistro = 'recuperacion';
        } elseif (($ahora->timestamp - $ultimoGuardadoTs) >= self::INTERVALO_DB_SEG) {
            // Pasaron 5 minutos desde el último guardado en BD
            $debeGuardar  = true;
            $tipoRegistro = $enFalla ? 'falla' : 'rutina';
        }

        if ($debeGuardar) {
            try {
                TelemetriaDosificadora::create([
                    'voltaje'   => $voltaje,
                    'corriente' => $corriente,
                    'tipo'      => $tipoRegistro,
                ]);
                $ultimoGuardadoTs = $ahora->timestamp;
            } catch (\Throwable $e) {
                // Evitar interrumpir la respuesta en vivo si falla la escritura
            }
        }

        Cache::put(self::CACHE_KEY_DB_STATE, [
            'ultimo_guardado_ts' => $ultimoGuardadoTs,
            'en_falla'           => $enFalla,
        ], now()->addHours(24));

        return response()->json([
            'ok'             => true,
            'guardado_en_bd' => $debeGuardar,
            'tipo'           => $tipoRegistro,
            'data'           => $lectura,
        ], 200);
    }

    /**
     * Devuelve al navegador la última lectura recibida por POST y el historial
     */
    public function actual(): JsonResponse
    {
        $actual    = Cache::get(self::CACHE_KEY_ACTUAL);
        $historial = Cache::get(self::CACHE_KEY_HISTORIAL, []);

        if (!$actual) {
            return response()->json([
                'conectado' => false,
                'voltaje'   => null,
                'corriente' => null,
                'hora'      => null,
                'historial' => [],
            ]);
        }

        // Consideramos conectado si el último POST llegó hace menos de 15 segundos
        $segundosSinDatos = now()->timestamp - ($actual['timestamp'] ?? 0);
        $conectado        = $segundosSinDatos <= 15;

        return response()->json([
            'conectado'          => $conectado,
            'segundos_sin_datos' => $segundosSinDatos,
            'voltaje'            => $actual['voltaje'],
            'corriente'          => $actual['corriente'],
            'hora'               => $actual['hora'],
            'fecha'              => $actual['fecha'] ?? null,
            'historial'          => $historial,
        ]);
    }
}
