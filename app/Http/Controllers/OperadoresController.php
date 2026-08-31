<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\RegistroPresion;
use App\Models\RegistroFiltro;
use App\Models\NivelQuimico;
use App\Models\Novedad;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Validation\ValidationException;
use App\Http\Controllers\BombasController;

class OperadoresController extends Controller
{
    // Mostrar la pantalla principal
    public function index()
    {
        $ultimosRegistros = RegistroPresion::with('user')
            ->orderBy('id', 'desc')
            ->take(24)
            ->get();

        $ultimosLavados = RegistroFiltro::with('user')
            ->orderBy('id', 'desc')
            ->take(6)
            ->get();

        $ultimoCloro = (object)[
            'tanque_principal' => NivelQuimico::where('quimico', 'cloro')->where('tipo_tanque', 'principal')->latest()->value('nivel'),
            'tanque_auxiliar' => NivelQuimico::where('quimico', 'cloro')->where('tipo_tanque', 'auxiliar')->latest()->value('nivel')
        ];

        $ultimaPoliamina = (object)[
            'tanque_principal' => NivelQuimico::where('quimico', 'poliamina')->where('tipo_tanque', 'principal')->latest()->value('nivel'),
            'tanque_auxiliar' => NivelQuimico::where('quimico', 'poliamina')->where('tipo_tanque', 'auxiliar')->latest()->value('nivel')
        ];

        $ultimoSulfato = (object)[
            'tanque_principal' => NivelQuimico::where('quimico', 'sulfato')->where('tipo_tanque', 'principal')->latest()->value('nivel'),
            'tanque_auxiliar' => NivelQuimico::where('quimico', 'sulfato')->where('tipo_tanque', 'auxiliar')->latest()->value('nivel')
        ];

        $ultimasNovedades = Novedad::with('user')->latest()->take(10)->get();

        $unreadQuery = Novedad::where('user_id', '!=', Auth::id())
            ->where('created_at', '>=', now()->subHours(16));
            
        if (Auth::user()->novedades_leidas_hasta) {
            $unreadQuery->where('created_at', '>', Auth::user()->novedades_leidas_hasta);
        }

        $novedadesRecientes = $unreadQuery->count();

        $estadosBombas = BombasController::cargarEstados();

        return view('operadores.index', compact('ultimosRegistros', 'ultimosLavados', 'ultimoCloro', 'ultimaPoliamina', 'ultimoSulfato', 'ultimasNovedades', 'novedadesRecientes', 'estadosBombas'));
    }

    // Guardar los datos de presiones
    public function storePresion(Request $request)
    {
        $request->validate([
            'presion_tanque' => 'required|numeric|min:0|max:26',
            'presion_planta' => 'required|numeric|min:0|max:22',
            'presion_falcon' => 'required|numeric|min:0|max:12',
            'nivel_cisterna' => 'required|numeric|min:0|max:100',
        ], [
            'presion_tanque.required' => 'La presión del tanque es obligatoria.',
            'presion_planta.required' => 'La presión de la planta es obligatoria.',
            'presion_falcon.required' => 'La presión de Falcon es obligatoria.',
            'nivel_cisterna.required' => 'El nivel de cisterna es obligatorio.',
            'numeric' => 'Las presiones y niveles deben ser números válidos (sin letras).',
            'min' => 'No se permiten números negativos.',
            'nivel_cisterna.max' => 'El nivel de cisterna no puede superar el 100%.',
            'presion_falcon.max' => 'La presión de falcon no puede superar 12.',
            'presion_tanque.max' => 'La presión del tanque no puede superar 26.',
            'presion_planta.max' => 'La presión de la planta no puede superar 22.',
            'presion_falcon.min' => 'La presión de falcon no puede ser menor a 0.',
            'presion_tanque.min' => 'La presión del tanque no puede ser menor a 0.',
            'presion_planta.min' => 'La presión de la planta no puede ser menor a 0.',
            'nivel_cisterna.min' => 'El nivel de cisterna no puede ser menor a 0.',
        ]);

        RegistroPresion::create([
            'user_id' => Auth::id(),
            'presion_tanque' => $request->presion_tanque,
            'presion_planta' => $request->presion_planta,
            'presion_falcon' => $request->presion_falcon,
            'nivel_cisterna' => $request->nivel_cisterna,
        ]);

        return back()->with('success_presiones', 'Presiones registradas correctamente');
    }

    // Guardar los datos de lavado de filtros
    public function storeFiltro(Request $request)
    {
        $request->validate([
            'inicio_lavado' => [
                'required',
                'date',
                function ($attribute, $value, $fail) {
                    // Límite de 2 horas hacia atrás desde el momento actual
                    $limiteAtras = now()->subHours(2);
                    // Límite de 1 hora hacia adelante desde el momento actual
                    $limiteAdelante = now()->addHour(1);
                    
                    $fechaInicio = Carbon::parse($value);

                    if ($fechaInicio->isBefore($limiteAtras)) {
                        $fail('El inicio del lavado no puede tener más de 2 horas de antigüedad.');
                    }

                    if ($fechaInicio->isAfter($limiteAdelante)) {
                        $fail('El inicio del lavado no puede programarse para más de 1 hora en el futuro.');
                    }
                },
            ],
            'fin_lavado' => [
                'required',
                'date',
                'after:inicio_lavado',
                function ($attribute, $value, $fail) use ($request) {
                    // Límite de 4 horas de duración
                    if ($request->filled('inicio_lavado')) {
                        $inicio = Carbon::parse($request->inicio_lavado);
                        $fin = Carbon::parse($value);
                        
                        if ($inicio->diffInMinutes($fin) > 240) { 
                            $fail('La duración del lavado no puede superar las 4 horas.');
                        }
                    }
                },
            ],
        ], [
            'inicio_lavado.required' => 'La fecha de inicio de lavado es obligatoria.',
            'fin_lavado.required' => 'La fecha de fin de lavado es obligatoria.',
            'inicio_lavado.date' => 'La fecha de inicio de lavado no es válida.',
            'fin_lavado.date' => 'La fecha de fin de lavado no es válida.',
            'fin_lavado.after' => 'La fecha de fin de lavado debe ser posterior a la de inicio.',
        ]);

        if (!$request->hasAny(['norte_1', 'norte_2', 'norte_3', 'sur_1', 'sur_2', 'sur_3'])) {
            throw ValidationException::withMessages([
                'filtros' => 'Debe seleccionar al menos un filtro para lavar.',
            ]);
        }

        RegistroFiltro::create([
            'user_id' => Auth::id(),
            'norte_1' => $request->has('norte_1'),
            'norte_2' => $request->has('norte_2'),
            'norte_3' => $request->has('norte_3'),
            'sur_1' => $request->has('sur_1'),
            'sur_2' => $request->has('sur_2'),
            'sur_3' => $request->has('sur_3'),
            'inicio_lavado' => $request->inicio_lavado,
            'fin_lavado' => $request->fin_lavado,
        ]);

        return back()->with('success_lavados', 'Lavado de filtros registrado correctamente');
    }

    // Guardar los datos de niveles de químicos
    public function storeQuimico(Request $request)
    {
        $request->validate([
            'cloro_principal' => 'nullable|numeric|min:0|max:100',
            'cloro_auxiliar' => 'nullable|numeric|min:0|max:100',
            'poliamina_principal' => 'nullable|numeric|min:0|max:100',
            'poliamina_auxiliar' => 'nullable|numeric|min:0|max:100',
            'sulfato_principal' => 'nullable|numeric|min:0|max:100',
            'sulfato_auxiliar' => 'nullable|numeric|min:0|max:100',
        ], [
            'numeric' => 'Los niveles deben ser números.',
            'min' => 'El nivel no puede ser negativo.',
            'max' => 'El nivel no puede superar el 100%.',
        ]);

        $quimicos = ['cloro', 'poliamina', 'sulfato'];
        $errores = [];

        // Primero verificamos que no haya valores idénticos a los actuales
        foreach ($quimicos as $quimico) {
            $principal = $request->input("{$quimico}_principal");
            if (!is_null($principal)) {
                $ultimoPrincipal = NivelQuimico::where('quimico', $quimico)->where('tipo_tanque', 'principal')->latest()->value('nivel');
                if ((float)$principal === (float)$ultimoPrincipal) {
                    $errores["{$quimico}_principal"] = 'No se puede ingresar el mismo porcentaje actual.';
                }
            }

            $auxiliar = $request->input("{$quimico}_auxiliar");
            if (!is_null($auxiliar)) {
                $ultimoAuxiliar = NivelQuimico::where('quimico', $quimico)->where('tipo_tanque', 'auxiliar')->latest()->value('nivel');
                if ((float)$auxiliar === (float)$ultimoAuxiliar) {
                    $errores["{$quimico}_auxiliar"] = 'No se puede ingresar el mismo porcentaje actual.';
                }
            }
        }

        if (!empty($errores)) {
            throw \Illuminate\Validation\ValidationException::withMessages($errores);
        }

        $actualizado = false;

        foreach ($quimicos as $quimico) {
            $principal = $request->input("{$quimico}_principal");
            $auxiliar = $request->input("{$quimico}_auxiliar");

            if (!is_null($principal)) {
                NivelQuimico::create([
                    'user_id' => Auth::id(),
                    'quimico' => $quimico,
                    'tipo_tanque' => 'principal',
                    'nivel' => $principal,
                ]);
                $actualizado = true;
            }

            if (!is_null($auxiliar)) {
                NivelQuimico::create([
                    'user_id' => Auth::id(),
                    'quimico' => $quimico,
                    'tipo_tanque' => 'auxiliar',
                    'nivel' => $auxiliar,
                ]);
                $actualizado = true;
            }
        }

        if (!$actualizado) {
            return back()->with('error_quimicos', 'Debe ingresar al menos el nivel de un tanque para actualizar.');
        }

        return back()->with('success_quimicos', "Niveles químicos actualizados correctamente.");
    }

    // Guardar una novedad
    public function storeNovedad(Request $request)
    {
        $request->validate([
            'mensaje' => 'required|string|max:1000',
        ], [
            'mensaje.required' => 'Debe escribir un mensaje para la novedad.',
            'mensaje.max' => 'La novedad es demasiado larga (máximo 1000 caracteres).',
        ]);

        Novedad::create([
            'user_id' => Auth::id(),
            'mensaje' => $request->mensaje,
        ]);

        return back()->with('success_novedades', 'Novedad registrada correctamente.');
    }

    // Borrar un registro de presión
    public function destroy($id)
    {
        $registro = RegistroPresion::findOrFail($id);

        // Verificar que el registro pertenezca al usuario logueado
        if ($registro->user_id !== Auth::id()) {
            return back()->with('error_presiones', 'No tienes permisos para borrar este registro.');
        }

        // Verificar que haya sido cargado hace menos de 2 horas
        if ($registro->created_at->lt(now()->subHours(2))) {
            return back()->with('error_presiones', 'No se puede borrar un registro con más de 2 horas de antigüedad.');
        }

        $registro->delete();

        return back()->with('success_presiones', 'Registro eliminado correctamente.');
    }

    // Borrar un registro de lavado de filtros
    public function destroyFiltro($id)
    {
        $registro = RegistroFiltro::findOrFail($id);

        // Verificar que el registro pertenezca al usuario logueado
        if ($registro->user_id !== Auth::id()) {
            return back()->with('error_lavados', 'No tienes permisos para borrar este registro.');
        }

        // Verificar que haya sido cargado hace menos de 2 horas
        if ($registro->created_at->lt(now()->subHours(2))) {
            return back()->with('error_lavados', 'No se puede borrar un registro con más de 2 horas de antigüedad.');
        }

        $registro->delete();

        return back()->with('success_lavados', 'Lavado de filtro eliminado correctamente.');
    }

    // Borrar una novedad
    public function destroyNovedad($id)
    {
        $novedad = Novedad::findOrFail($id);

        if ($novedad->user_id !== Auth::id()) {
            return back()->with('error_novedades', 'No tienes permisos para borrar esta novedad.');
        }

        if ($novedad->created_at->lt(now()->subHours(2))) {
            return back()->with('error_novedades', 'No se puede borrar una novedad con más de 2 horas de antigüedad.');
        }

        $novedad->delete();

        return back()->with('success_novedades', 'Novedad eliminada correctamente.');
    }

    // Marcar novedades como leidas
    public function marcarLeidas()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $user->novedades_leidas_hasta = now();
        $user->save();

        return back()->with('success_novedades', 'Novedades marcadas como leídas.');
    }
}
