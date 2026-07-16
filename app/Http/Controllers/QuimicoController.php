<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CalidadAgua;
use App\Models\Novedad;
use Illuminate\Support\Facades\Auth;

class QuimicoController extends Controller
{
    // Mostrar la pantalla principal del Módulo Químico
    public function index()
    {
        $ultimosRegistros = CalidadAgua::with('user')
            ->orderBy('id', 'desc')
            ->take(24)
            ->get();

        $ultimasNovedades = Novedad::with('user')->latest()->take(10)->get();

        $unreadQuery = Novedad::where('user_id', '!=', Auth::id())
            ->where('created_at', '>=', now()->subHours(16));
            
        if (Auth::user()->novedades_leidas_hasta) {
            $unreadQuery->where('created_at', '>', Auth::user()->novedades_leidas_hasta);
        }

        $novedadesRecientes = $unreadQuery->count();

        return view('quimico.index', compact('ultimosRegistros', 'ultimasNovedades', 'novedadesRecientes'));
    }

    // Guardar los datos de calidad de agua en filas separadas por lugar
    public function storeCalidad(Request $request)
    {
        $request->validate([
            'decantador_norte_turbiedad' => 'nullable|numeric|min:0|max:300',
            'decantador_norte_ph' => 'nullable|numeric|min:0|max:14',
            'decantador_sur_turbiedad' => 'nullable|numeric|min:0|max:300',
            'decantador_sur_ph' => 'nullable|numeric|min:0|max:14',
            'cisterna_turbiedad' => 'nullable|numeric|min:0|max:10',
            'cisterna_ph' => 'nullable|numeric|min:0|max:14',
            'cisterna_cloro' => 'nullable|numeric|min:0|max:3',
            'rio_turbiedad' => 'nullable|numeric|min:0|max:300',
            'rio_ph' => 'nullable|numeric|min:0|max:14',
            'filtro_norte_select' => 'nullable|string|in:Filtro 1,Filtro 2,Filtro 3',
            'filtro_norte_turbiedad' => 'nullable|numeric|min:0|max:50',
            'filtro_norte_ph' => 'nullable|numeric|min:0|max:14',
            'filtro_sur_select' => 'nullable|string|in:Filtro 1,Filtro 2,Filtro 3',
            'filtro_sur_turbiedad' => 'nullable|numeric|min:0|max:50',
            'filtro_sur_ph' => 'nullable|numeric|min:0|max:14',
        ], [
            'numeric' => 'Las mediciones deben ser números válidos (sin letras).',
            'min' => 'No se permiten números negativos.',
            'decantador_norte_ph.max' => 'El pH de decantador norte no puede superar 14.',
            'decantador_sur_ph.max' => 'El pH de decantador sur no puede superar 14.',
            'cisterna_ph.max' => 'El pH de cisterna no puede superar 14.',
            'rio_ph.max' => 'El pH de río no puede superar 14.',
            'filtro_norte_ph.max' => 'El pH de filtro línea norte no puede superar 14.',
            'filtro_sur_ph.max' => 'El pH de filtro línea sur no puede superar 14.',
            'filtro_norte_select.in' => 'El filtro de la Línea Norte seleccionado no es válido.',
            'filtro_sur_select.in' => 'El filtro de la Línea Sur seleccionado no es válido.',
            'decantador_norte_turbiedad.max' => 'La turbiedad de Decantador Norte no puede superar 300.',
            'decantador_sur_turbiedad.max' => 'La turbiedad de Decantador Sur no puede superar 300.',
            'rio_turbiedad.max' => 'La turbiedad de Río no puede superar 300.',
            'cisterna_turbiedad.max' => 'La turbiedad de Cisterna no puede superar 10.',
            'filtro_norte_turbiedad.max' => 'La turbiedad de Filtro Línea Norte no puede superar 50.',
            'decantador_norte_turbiedad.min' => 'La turbiedad de Decantador Norte no puede ser menor a 0.',
            'decantador_sur_turbiedad.min' => 'La turbiedad de Decantador Sur no puede ser menor a 0.',
            'rio_turbiedad.min' => 'La turbiedad de Río no puede ser menor a 0.',
            'cisterna_turbiedad.min' => 'La turbiedad de Cisterna no puede ser menor a 0.',
            'filtro_norte_turbiedad.min' => 'La turbiedad de Filtro Línea Norte no puede ser menor a 0.',
            'filtro_sur_turbiedad.min' => 'La turbiedad de Filtro Línea Sur no puede ser menor a 0.',
            'cisterna_cloro.max' => 'El cloro residual de Cisterna no puede superar 3.',
            'cisterna_cloro.min' => 'El cloro residual de Cisterna no puede ser menor a 0.',
        ]);

        // Validaciones cruzadas para los filtros
        if (($request->filled('filtro_norte_turbiedad') || $request->filled('filtro_norte_ph')) && !$request->filled('filtro_norte_select')) {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'filtro_norte_select' => 'Debe seleccionar un filtro de la Línea Norte si ingresa mediciones para esta sección.',
            ]);
        }

        if (($request->filled('filtro_sur_turbiedad') || $request->filled('filtro_sur_ph')) && !$request->filled('filtro_sur_select')) {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'filtro_sur_select' => 'Debe seleccionar un filtro de la Línea Sur si ingresa mediciones para esta sección.',
            ]);
        }

        // Comprobar que al menos se haya cargado una medición
        $hasData = false;
        if ($request->filled('decantador_norte_turbiedad') || $request->filled('decantador_norte_ph')) $hasData = true;
        if ($request->filled('decantador_sur_turbiedad') || $request->filled('decantador_sur_ph')) $hasData = true;
        if ($request->filled('cisterna_turbiedad') || $request->filled('cisterna_ph') || $request->filled('cisterna_cloro')) $hasData = true;
        if ($request->filled('rio_turbiedad') || $request->filled('rio_ph')) $hasData = true;
        if ($request->filled('filtro_norte_select') && ($request->filled('filtro_norte_turbiedad') || $request->filled('filtro_norte_ph'))) $hasData = true;
        if ($request->filled('filtro_sur_select') && ($request->filled('filtro_sur_turbiedad') || $request->filled('filtro_sur_ph'))) $hasData = true;

        if (!$hasData) {
            return back()->with('error', 'Debe completar al menos una medición antes de confirmar.')->withInput();
        }

        $recordsCreated = 0;

        // 1. Decantador Norte
        if ($request->filled('decantador_norte_turbiedad') || $request->filled('decantador_norte_ph')) {
            CalidadAgua::create([
                'user_id' => Auth::id(),
                'lugar' => 'DECANTADOR NORTE',
                'turbiedad' => $request->decantador_norte_turbiedad,
                'ph' => $request->decantador_norte_ph,
            ]);
            $recordsCreated++;
        }

        // 2. Decantador Sur
        if ($request->filled('decantador_sur_turbiedad') || $request->filled('decantador_sur_ph')) {
            CalidadAgua::create([
                'user_id' => Auth::id(),
                'lugar' => 'DECANTADOR SUR',
                'turbiedad' => $request->decantador_sur_turbiedad,
                'ph' => $request->decantador_sur_ph,
            ]);
            $recordsCreated++;
        }

        // 3. Cisterna
        if ($request->filled('cisterna_turbiedad') || $request->filled('cisterna_ph') || $request->filled('cisterna_cloro')) {
            CalidadAgua::create([
                'user_id' => Auth::id(),
                'lugar' => 'CISTERNA',
                'turbiedad' => $request->cisterna_turbiedad,
                'ph' => $request->cisterna_ph,
                'cloro_residual' => $request->cisterna_cloro,
            ]);
            $recordsCreated++;
        }

        // 4. Río
        if ($request->filled('rio_turbiedad') || $request->filled('rio_ph')) {
            CalidadAgua::create([
                'user_id' => Auth::id(),
                'lugar' => 'RIO',
                'turbiedad' => $request->rio_turbiedad,
                'ph' => $request->rio_ph,
            ]);
            $recordsCreated++;
        }

        // 5. Filtro Línea Norte
        if ($request->filled('filtro_norte_select') && ($request->filled('filtro_norte_turbiedad') || $request->filled('filtro_norte_ph'))) {
            CalidadAgua::create([
                'user_id' => Auth::id(),
                'lugar' => 'FILTRO LINEA NORTE',
                'filtro_numero' => $request->filtro_norte_select,
                'turbiedad' => $request->filtro_norte_turbiedad,
                'ph' => $request->filtro_norte_ph,
            ]);
            $recordsCreated++;
        }

        // 6. Filtro Línea Sur
        if ($request->filled('filtro_sur_select') && ($request->filled('filtro_sur_turbiedad') || $request->filled('filtro_sur_ph'))) {
            CalidadAgua::create([
                'user_id' => Auth::id(),
                'lugar' => 'FILTRO LINEA SUR',
                'filtro_numero' => $request->filtro_sur_select,
                'turbiedad' => $request->filtro_sur_turbiedad,
                'ph' => $request->filtro_sur_ph,
            ]);
            $recordsCreated++;
        }

        return back()->with('success', "Se registraron correctamente $recordsCreated mediciones de calidad de agua.");
    }

    // Borrar un registro de calidad de agua
    public function destroyCalidad($id)
    {
        $registro = CalidadAgua::findOrFail($id);

        // Verificar que el registro pertenezca al usuario logueado
        if ($registro->user_id !== Auth::id()) {
            return back()->with('error', 'No tienes permisos para borrar este registro.');
        }

        // Verificar que haya sido cargado hace menos de 2 horas
        if ($registro->created_at->lt(now()->subHours(2))) {
            return back()->with('error', 'No se puede borrar un registro con más de 2 horas de antigüedad.');
        }

        $registro->delete();

        return back()->with('deleted', 'Registro de calidad eliminado correctamente.');
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

        return back()->with('deleted', 'Novedad eliminada correctamente.');
    }

    // Marcar novedades como leídas
    public function marcarLeidas()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $user->novedades_leidas_hasta = now();
        $user->save();

        return back()->with('success', 'Novedades marcadas como leídas.');
    }
}
