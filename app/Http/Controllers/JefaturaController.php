<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\RegistroPresion;
use App\Models\CalidadAgua;
use App\Models\RegistroFiltro;
use App\Models\NivelQuimico;
use Carbon\Carbon;
use Illuminate\Support\Facades\Schema;
class JefaturaController extends Controller
{
    public function index(Request $request)
    {
        $calidadFechaInicio = $request->input('calidad_fecha_inicio', Carbon::today()->subDays(7)->format('Y-m-d'));
        $calidadFechaFin = $request->input('calidad_fecha_fin', Carbon::today()->format('Y-m-d'));
        
        $presionesFechaInicio = $request->input('presiones_fecha_inicio', Carbon::today()->subDays(7)->format('Y-m-d'));
        $presionesFechaFin = $request->input('presiones_fecha_fin', Carbon::today()->format('Y-m-d'));

        // 1. Datos de Presiones (últimos 30 registros, orden cronológico)
        $presiones = RegistroPresion::with('user')->orderBy('created_at', 'desc')->take(30)->get()->reverse()->values();

        // 2. Datos de Calidad de Agua (últimos 100 registros para ver mejor la correlación)
        $calidadAgua = CalidadAgua::with('user')->orderBy('created_at', 'desc')->take(100)->get()->reverse()->values();
        
        // 2b. Últimos registros por lugar de Calidad de Agua
        $ultimosIds = CalidadAgua::selectRaw('MAX(id) as id')
            ->groupBy('lugar', 'filtro_numero')
            ->pluck('id');

        $ultimosPorLugar = CalidadAgua::with('user')
            ->whereIn('id', $ultimosIds)
            ->orderBy('created_at', 'desc')
            ->get();

        // 3. Niveles de Químicos (Dinámico desde la BD)
        $quimicosTipos = NivelQuimico::select('quimico')->distinct()->pluck('quimico')->toArray();
        $nivelesQuimicos = [];
        
        foreach ($quimicosTipos as $q) {
            $nivelesQuimicos[] = [
                'quimico' => $q,
                'tanque_principal' => NivelQuimico::where('quimico', $q)->where('tipo_tanque', 'principal')->latest()->value('nivel'),
                'tanque_auxiliar' => NivelQuimico::where('quimico', $q)->where('tipo_tanque', 'auxiliar')->latest()->value('nivel')
            ];
        }
        
        // 3b. Historial de Químicos (últimos 100 registros)
        $historialQuimicos = NivelQuimico::with('user')->orderBy('created_at', 'desc')->take(100)->get()->reverse()->values();

        // 4. Lavado de Filtros (Dinámico desde la BD consultando las columnas)
        $filtrosRaw = RegistroFiltro::with('user')->orderBy('created_at', 'desc')->take(50)->get();
        
        $todasColumnas = Schema::getColumnListing('registro_filtros');
        $columnasExcluidas = ['id', 'user_id', 'created_at', 'updated_at', 'inicio_lavado', 'fin_lavado', 'observaciones'];
        $columnasFiltros = array_diff($todasColumnas, $columnasExcluidas);
        
        $conteoFiltros = [];
        foreach ($columnasFiltros as $columna) {
            $nombreFiltro = ucwords(str_replace('_', ' ', $columna));
            $conteoFiltros[$nombreFiltro] = $filtrosRaw->where($columna, true)->count();
        }

        // 5. Históricos para Tablas
        $queryCalidad = CalidadAgua::with('user')
            ->whereDate('created_at', '>=', $calidadFechaInicio)
            ->whereDate('created_at', '<=', $calidadFechaFin);
            
        if ($request->filled('lugar')) {
            $queryCalidad->where('lugar', $request->lugar);
        }
        
        $historialCalidad = $queryCalidad->orderBy('created_at', 'desc')
            ->paginate(50, ['*'], 'calidad_page')->withQueryString();

        $historialPresiones = RegistroPresion::with('user')
            ->whereDate('created_at', '>=', $presionesFechaInicio)
            ->whereDate('created_at', '<=', $presionesFechaFin)
            ->orderBy('created_at', 'desc')
            ->paginate(50, ['*'], 'presiones_page')->withQueryString();

        // 6. Usuarios pendientes de aprobación
        $usuariosPendientes = \App\Models\User::where('is_approved', false)->orderBy('created_at', 'desc')->get();

        // 7. Todos los empleados (usuarios aprobados)
        $empleados = \App\Models\User::where('is_approved', true)->orderBy('name', 'asc')->get();

        // 8. Empleados dados de baja (SoftDeleted)
        $empleadosDadosDeBaja = \App\Models\User::onlyTrashed()->orderBy('name', 'asc')->get();

        return view('jefatura.index', compact(
            'presiones', 
            'calidadAgua',
            'ultimosPorLugar',
            'nivelesQuimicos', 
            'historialQuimicos',
            'conteoFiltros',
            'historialCalidad',
            'historialPresiones',
            'calidadFechaInicio',
            'calidadFechaFin',
            'presionesFechaInicio',
            'presionesFechaFin',
            'usuariosPendientes',
            'empleados',
            'empleadosDadosDeBaja'
        ));
    }

    public function aprobarUsuario($id)
    {
        $user = \App\Models\User::findOrFail($id);
        $user->is_approved = true;
        $user->save();

        return redirect()->route('jefatura.index')->with('success', "El usuario {$user->name} ha sido aprobado exitosamente.");
    }

    public function rechazarUsuario($id)
    {
        $user = \App\Models\User::findOrFail($id);
        
        // Solo podemos rechazar si aún no está aprobado
        if (!$user->is_approved) {
            $nombre = $user->name;
            $user->forceDelete();
            return redirect()->route('jefatura.index')->with('success', "El usuario {$nombre} ha sido rechazado y eliminado permanentemente.");
        }

        return redirect()->route('jefatura.index')->with('error', "No se puede rechazar a un usuario que ya está aprobado.");
    }

    public function actualizarRol(Request $request, $id)
    {
        $request->validate([
            'role' => 'required|string|in:operador,quimico,jefatura,laboratorio'
        ]);

        $user = \App\Models\User::findOrFail($id);
        
        // Evitar que jefatura se quite sus propios permisos y pierda acceso
        if ($user->id === auth()->id() && $request->role !== 'jefatura') {
             return redirect()->route('jefatura.index')->with('error', "No puedes quitarte los permisos de jefatura a ti mismo.");
        }

        $user->role = $request->role;
        $user->save();

        return redirect()->route('jefatura.index')->with('success', "Rol de {$user->name} actualizado a {$user->role}.");
    }

    public function darDeBaja($id)
    {
        $user = \App\Models\User::findOrFail($id);

        if ($user->id === auth()->id()) {
            return redirect()->route('jefatura.index')->with('error', "No puedes darte de baja a ti mismo.");
        }

        $nombre = $user->name;
        $user->delete();

        return redirect()->route('jefatura.index')->with('success', "El empleado {$nombre} ha sido dado de baja exitosamente.");
    }

    public function reactivarUsuario($id)
    {
        $user = \App\Models\User::onlyTrashed()->findOrFail($id);
        $user->restore();

        return redirect()->route('jefatura.index')->with('success', "El empleado {$user->name} ha sido reactivado y dado de alta exitosamente.");
    }
}
