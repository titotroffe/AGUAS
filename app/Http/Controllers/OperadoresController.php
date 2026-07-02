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

        return view('operadores.index', compact('ultimosRegistros', 'ultimosLavados', 'ultimoCloro', 'ultimaPoliamina', 'ultimoSulfato', 'ultimasNovedades', 'novedadesRecientes'));
    }

    // Guardar los datos de presiones
    public function storePresion(Request $request)
    {
        $request->validate([
            'presion_tanque' => 'required|numeric|min:0|max:26',
            'presion_planta' => 'required|numeric|min:0|max:22',
            'presion_falcon' => 'required|numeric|min:0 |max:12',
            'nivel_cisterna' => 'required|numeric|min:0|max:100',
        ], [
            'presion_tanque.required' => 'La presión del tanque es obligatoria.',
            'presion_planta.required' => 'La presión de la planta es obligatoria.',
            'presion_falcon.required' => 'La presión de Falcon es obligatoria.',
            'nivel_cisterna.required' => 'El nivel de cisterna es obligatorio.',
            'numeric' => 'Las presiones y niveles deben ser números válidos (sin letras).',
            'min' => 'No se permiten números negativos.',
            'nivel_cisterna.max' => 'El nivel de cisterna no puede superar el 100%.',
        ]);

        RegistroPresion::create([
            'user_id' => Auth::id(),
            'presion_tanque' => $request->presion_tanque,
            'presion_planta' => $request->presion_planta,
            'presion_falcon' => $request->presion_falcon,
            'nivel_cisterna' => $request->nivel_cisterna,
        ]);

        return back()->with('success', 'Presiones registradas correctamente');
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

        return back()->with('success', 'Lavado de filtros registrado correctamente');
    }

    // Guardar los datos de niveles de químicos
    public function storeQuimico(Request $request)
    {
        $request->validate([
            'quimico' => 'required|in:cloro,poliamina,sulfato',
            'tanque_principal' => 'nullable|numeric|min:0|max:100',
            'tanque_auxiliar' => 'nullable|numeric|min:0|max:100',
        ], [
            'quimico.required' => 'Debe especificar el químico.',
            'quimico.in' => 'El químico especificado no es válido.',
            'tanque_principal.numeric' => 'El nivel del tanque principal debe ser un número.',
            'tanque_principal.min' => 'El nivel no puede ser negativo.',
            'tanque_principal.max' => 'El nivel no puede superar el 100%.',
            'tanque_auxiliar.numeric' => 'El nivel del tanque auxiliar debe ser un número.',
            'tanque_auxiliar.min' => 'El nivel no puede ser negativo.',
            'tanque_auxiliar.max' => 'El nivel no puede superar el 100%.',
        ]);

        if (is_null($request->tanque_principal) && is_null($request->tanque_auxiliar)) {
            return back()->with('error', 'Debe ingresar al menos el nivel de un tanque para actualizar.');
        }

        if ($request->filled('tanque_principal')) {
            NivelQuimico::create([
                'user_id' => Auth::id(),
                'quimico' => $request->quimico,
                'tipo_tanque' => 'principal',
                'nivel' => $request->tanque_principal,
            ]);
        }

        if ($request->filled('tanque_auxiliar')) {
            NivelQuimico::create([
                'user_id' => Auth::id(),
                'quimico' => $request->quimico,
                'tipo_tanque' => 'auxiliar',
                'nivel' => $request->tanque_auxiliar,
            ]);
        }

        $nombreQuimico = ucfirst($request->quimico);
        return back()->with('success', "Niveles de $nombreQuimico actualizados correctamente.");
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

        return back()->with('success', 'Novedad registrada correctamente.');
    }

    // Borrar un registro de presión
    public function destroy($id)
    {
        $registro = RegistroPresion::findOrFail($id);

        // Verificar que el registro pertenezca al usuario logueado
        if ($registro->user_id !== Auth::id()) {
            return back()->with('error', 'No tienes permisos para borrar este registro.');
        }

        // Verificar que haya sido cargado hace menos de 2 horas
        if ($registro->created_at->lt(now()->subHours(2))) {
            return back()->with('error', 'No se puede borrar un registro con más de 2 horas de antigüedad.');
        }

        $registro->delete();

        return back()->with('success', 'Registro eliminado correctamente.');
    }

    // Borrar un registro de lavado de filtros
    public function destroyFiltro($id)
    {
        $registro = RegistroFiltro::findOrFail($id);

        // Verificar que el registro pertenezca al usuario logueado
        if ($registro->user_id !== Auth::id()) {
            return back()->with('error', 'No tienes permisos para borrar este registro.');
        }

        // Verificar que haya sido cargado hace menos de 2 horas
        if ($registro->created_at->lt(now()->subHours(2))) {
            return back()->with('error', 'No se puede borrar un registro con más de 2 horas de antigüedad.');
        }

        $registro->delete();

        return back()->with('success', 'Lavado de filtro eliminado correctamente.');
    }

    // Borrar una novedad
    public function destroyNovedad($id)
    {
        $novedad = Novedad::findOrFail($id);

        if ($novedad->user_id !== Auth::id()) {
            return back()->with('error', 'No tienes permisos para borrar esta novedad.');
        }

        if ($novedad->created_at->lt(now()->subHours(2))) {
            return back()->with('error', 'No se puede borrar una novedad con más de 2 horas de antigüedad.');
        }

        $novedad->delete();

        return back()->with('success', 'Novedad eliminada correctamente.');
    }

    // Marcar novedades como leidas
    public function marcarLeidas()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $user->novedades_leidas_hasta = now();
        $user->save();

        return back()->with('success', 'Novedades marcadas como leídas.');
    }
}
