<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\EstadoBomba;
use App\Models\EventoBomba;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\JsonResponse;

class BombasController extends Controller
{
    const DISPOSITIVOS_BOMBA = ['bomba_1', 'bomba_2', 'bomba_3'];
    const MAX_BOMBAS_ENCENDIDAS = 2;

    /**
     * Retorna el estado actual de todos los dispositivos (para polling AJAX).
     */
    public function estado(): JsonResponse
    {
        $estados = EstadoBomba::all()->keyBy('dispositivo');

        $data = [];
        foreach (['bomba_1', 'bomba_2', 'bomba_3', 'pozo_norte', 'pozo_sur'] as $dispositivo) {
            $registro = $estados->get($dispositivo);
            $data[$dispositivo] = [
                'estado'   => $registro ? (bool) $registro->estado : false,
                'operador' => $registro && $registro->user ? $registro->user->name : null,
                'updated'  => $registro ? $registro->updated_at->toIso8601String() : null,
            ];
        }

        return response()->json($data);
    }

    /**
     * Cambia el estado de un dispositivo (encender/apagar).
     */
    public function toggle(Request $request): JsonResponse
    {
        $request->validate([
            'dispositivo' => 'required|in:bomba_1,bomba_2,bomba_3,pozo_norte,pozo_sur',
            'estado'      => 'required|boolean',
        ]);

        $dispositivo = $request->dispositivo;
        $nuevoEstado = (bool) $request->estado;

        // Regla de negocio: máx. 2 bombas de río encendidas simultáneamente
        if ($nuevoEstado && in_array($dispositivo, self::DISPOSITIVOS_BOMBA)) {
            $bombasEncendidas = EstadoBomba::whereIn('dispositivo', self::DISPOSITIVOS_BOMBA)
                ->where('estado', true)
                ->where('dispositivo', '!=', $dispositivo)
                ->count();

            if ($bombasEncendidas >= self::MAX_BOMBAS_ENCENDIDAS) {
                return response()->json([
                    'ok'      => false,
                    'mensaje' => 'No se pueden encender más de 2 bombas de río simultáneamente.',
                ], 422);
            }
        }

        // Actualizar estado actual (upsert por dispositivo)
        $estadoActual = EstadoBomba::where('dispositivo', $dispositivo)->first();

        // Solo actuar si el estado cambió
        if ($estadoActual && (bool) $estadoActual->estado === $nuevoEstado) {
            return response()->json(['ok' => true, 'sin_cambio' => true]);
        }

        EstadoBomba::updateOrCreate(
            ['dispositivo' => $dispositivo],
            ['estado' => $nuevoEstado, 'user_id' => Auth::id()]
        );

        // Registrar evento
        if ($nuevoEstado) {
            // Encendido: abrir nuevo evento
            EventoBomba::create([
                'dispositivo'  => $dispositivo,
                'user_id'      => Auth::id(),
                'encendido_at' => now(),
                'apagado_at'   => null,
            ]);
        } else {
            // Apagado: cerrar el último evento abierto
            $eventoAbierto = EventoBomba::where('dispositivo', $dispositivo)
                ->whereNull('apagado_at')
                ->latest('encendido_at')
                ->first();

            if ($eventoAbierto) {
                $duracion = $eventoAbierto->encendido_at->diffInSeconds(now());
                $eventoAbierto->update([
                    'apagado_at'        => now(),
                    'duracion_segundos' => $duracion,
                ]);
            }
        }

        return response()->json([
            'ok'      => true,
            'estado'  => $nuevoEstado,
            'mensaje' => $nuevoEstado
                ? ucfirst(str_replace('_', ' ', $dispositivo)) . ' encendido.'
                : ucfirst(str_replace('_', ' ', $dispositivo)) . ' apagado.',
        ]);
    }

    /**
     * Helper estático para cargar estados desde los controladores de vistas.
     */
    public static function cargarEstados(): array
    {
        $registros = EstadoBomba::with('user')->get()->keyBy('dispositivo');

        $resultado = [];
        foreach (['bomba_1', 'bomba_2', 'bomba_3', 'pozo_norte', 'pozo_sur'] as $dispositivo) {
            $reg = $registros->get($dispositivo);
            $resultado[$dispositivo] = [
                'estado'   => $reg ? (bool) $reg->estado : false,
                'operador' => $reg && $reg->user ? $reg->user->name : null,
            ];
        }

        return $resultado;
    }
}
