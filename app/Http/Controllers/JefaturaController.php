<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\RegistroPresion;
use App\Models\CalidadAgua;
use App\Models\RegistroFiltro;
use App\Models\NivelQuimico;
use Carbon\Carbon;

class JefaturaController extends Controller
{
    public function index(Request $request)
    {
        $calidadFechaInicio = $request->input('calidad_fecha_inicio', Carbon::today()->subDays(7)->format('Y-m-d'));
        $calidadFechaFin = $request->input('calidad_fecha_fin', Carbon::today()->format('Y-m-d'));
        
        $presionesFechaInicio = $request->input('presiones_fecha_inicio', Carbon::today()->subDays(7)->format('Y-m-d'));
        $presionesFechaFin = $request->input('presiones_fecha_fin', Carbon::today()->format('Y-m-d'));

        // 1. Datos de Presiones (últimos 30 registros, orden cronológico)
        $presiones = RegistroPresion::orderBy('created_at', 'desc')->take(30)->get()->reverse()->values();

        // 2. Datos de Calidad de Agua (últimos 100 registros para ver mejor la correlación)
        $calidadAgua = CalidadAgua::orderBy('created_at', 'desc')->take(100)->get()->reverse()->values();
        
        // 2b. Últimos registros por lugar de Calidad de Agua
        $ultimosPorLugar = CalidadAgua::with('user')->orderBy('created_at', 'desc')->get()->unique(function ($item) {
            return $item->lugar . ($item->filtro_numero ? '-' . $item->filtro_numero : '');
        })->values();

        // 3. Niveles de Químicos (obtener el último registro de cada químico y tanque)
        $quimicosTipos = ['cloro', 'poliamina', 'sulfato'];
        $nivelesQuimicos = [];
        
        foreach ($quimicosTipos as $q) {
            $nivelesQuimicos[] = [
                'quimico' => $q,
                'tanque_principal' => NivelQuimico::where('quimico', $q)->where('tipo_tanque', 'principal')->latest()->value('nivel'),
                'tanque_auxiliar' => NivelQuimico::where('quimico', $q)->where('tipo_tanque', 'auxiliar')->latest()->value('nivel')
            ];
        }
        
        // 3b. Historial de Químicos (últimos 100 registros)
        $historialQuimicos = NivelQuimico::orderBy('created_at', 'desc')->take(100)->get()->reverse()->values();

        // 4. Lavado de Filtros (conteo de las últimas veces lavadas)
        // Sumaremos cuántas veces se lavó cada filtro en los últimos 50 registros
        $filtrosRaw = RegistroFiltro::orderBy('created_at', 'desc')->take(50)->get();
        $conteoFiltros = [
            'Norte 1' => $filtrosRaw->where('norte_1', true)->count(),
            'Norte 2' => $filtrosRaw->where('norte_2', true)->count(),
            'Norte 3' => $filtrosRaw->where('norte_3', true)->count(),
            'Sur 1' => $filtrosRaw->where('sur_1', true)->count(),
            'Sur 2' => $filtrosRaw->where('sur_2', true)->count(),
            'Sur 3' => $filtrosRaw->where('sur_3', true)->count(),
        ];

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
            'presionesFechaFin'
        ));
    }
}
