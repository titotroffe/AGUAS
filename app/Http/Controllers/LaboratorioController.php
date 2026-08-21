<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\LaboratorioInsumo;
use App\Models\LaboratorioAguaCruda;
use App\Models\LaboratorioProductoTerminado;
use App\Models\LaboratorioPozo;
use App\Models\Novedad;
use Illuminate\Support\Facades\Auth;

class LaboratorioController extends Controller
{
    public function index()
    {
        // EAV Records for Insumos (modulo_id = 1)
        $valores = \App\Models\LabValor::with(['medicion.insumo', 'medicion.tipoMedicion'])
            ->whereHas('medicion', function($q) {
                $q->where('modulo_id', 1);
            })
            ->orderBy('fecha', 'desc')
            ->get();
            
        $insumosAgrupados = $valores->groupBy(function($valor) {
            return $valor->fecha . '_' . $valor->medicion->insumo_id;
        })->map(function($grupo) {
            $first = $grupo->first();
            $contramuestra = $grupo->firstWhere('medicion.tipo_medicion_id', 10);
            return (object) [
                'id' => $first->fecha . '_' . $first->medicion->insumo_id,
                'fecha' => $first->fecha,
                'insumo_id' => $first->medicion->insumo_id,
                'nombre_insumo' => $first->medicion->insumo->nombre,
                'tipo_insumo' => $first->medicion->insumo_id,
                'preparacion_archivo_contramuestra' => $contramuestra ? ($contramuestra->valor === '1') : false,
            ];
        })->take(24)->values();
        
        $insumos = $insumosAgrupados;

        $aguaCruda = LaboratorioAguaCruda::orderBy('created_at', 'desc')->take(24)->get();
        $productoTerminado = LaboratorioProductoTerminado::orderBy('created_at', 'desc')->take(24)->get();
        $pozos = LaboratorioPozo::orderBy('created_at', 'desc')->take(24)->get();

        $ultimasNovedades = Novedad::with('user')->latest()->take(10)->get();

        $unreadQuery = Novedad::where('user_id', '!=', Auth::id())
            ->where('created_at', '>=', now()->subHours(16));
            
        if (Auth::user()->novedades_leidas_hasta) {
            $unreadQuery->where('created_at', '>', Auth::user()->novedades_leidas_hasta);
        }

        $novedadesRecientes = $unreadQuery->count();

        // Fetch dynamic fields from EAV setup
        $medicionesConfig = \App\Models\LabMedicion::with('tipoMedicion', 'insumo')
            ->where('modulo_id', 1)
            ->where('activo', true)
            ->get();
            
        $tiposInsumos = \App\Models\LabInsumo::all();
        
        $insumoFields = [];
        foreach ($medicionesConfig as $config) {
            if ($config->tipo_medicion_id == 10) continue; // Exclude contramuestra since it's a checkbox in UI
            
            $insumoFields[] = [
                'name' => 'medicion_' . $config->id,
                'label' => mb_strtoupper($config->tipoMedicion->nombre),
                'classes' => 'f-insumo-' . $config->insumo_id,
                'show' => old('tipo_insumo') == $config->insumo_id,
            ];
        }

        return view('laboratorio.index', compact('tiposInsumos', 'insumoFields', 'insumos', 'aguaCruda', 'productoTerminado', 'pozos', 'ultimasNovedades', 'novedadesRecientes'));
    }

    public function storeInsumo(Request $request)
    {
        $tipo = $request->input('tipo_insumo'); // Now it's the ID
        $fecha = $request->input('fecha');
        $observaciones = $request->input('observaciones');
        
        $configuraciones = \App\Models\LabMedicion::where('modulo_id', 1)
            ->where('insumo_id', $tipo)
            ->where('activo', true)
            ->get();
            
        foreach ($configuraciones as $config) {
            $valorStr = null;
            
            if ($config->tipo_medicion_id == 10) {
                $valorStr = $request->has('preparacion_archivo_contramuestra') ? '1' : '0';
            } else {
                $inputName = 'medicion_' . $config->id;
                if ($request->has($inputName) && $request->input($inputName) !== null) {
                    $valorStr = $request->input($inputName);
                }
            }
            
            if ($valorStr !== null) {
                \App\Models\LabValor::create([
                    'fecha' => $fecha,
                    'medicion_id' => $config->id,
                    'valor' => (string) $valorStr,
                    'observaciones' => $observaciones
                ]);
            }
        }

        return redirect()->route('laboratorio.index')->with('success', 'Registro de Insumo guardado correctamente.');
    }

    public function storeAguaCruda(Request $request)
    {
        LaboratorioAguaCruda::create($request->all());
        return redirect()->route('laboratorio.index')->with('success', 'Registro de Agua Cruda guardado correctamente.');
    }

    public function storeProductoTerminado(Request $request)
    {
        LaboratorioProductoTerminado::create($request->all());
        return redirect()->route('laboratorio.index')->with('success', 'Registro de Producto Terminado guardado correctamente.');
    }

    public function storePozo(Request $request)
    {
        LaboratorioPozo::create($request->all());
        return redirect()->route('laboratorio.index')->with('success', 'Registro de Pozo guardado correctamente.');
    }
    
    public function destroyInsumo($tipo, $id)
    {
        // Here $id is "fecha_insumoId", e.g., "2026-08-20_1"
        $parts = explode('_', $id);
        if (count($parts) == 2) {
            $fecha = $parts[0];
            $insumo_id = $parts[1];
            
            // Delete all values for this date and insumo
            $medicionesIds = \App\Models\LabMedicion::where('modulo_id', 1)
                ->where('insumo_id', $insumo_id)
                ->pluck('id');
                
            \App\Models\LabValor::whereIn('medicion_id', $medicionesIds)
                ->where('fecha', $fecha)
                ->delete();
        }

        return redirect()->route('laboratorio.index')->with('success', 'Registro eliminado correctamente.');
    }
    
    public function destroyAguaCruda($id)
    {
        LaboratorioAguaCruda::findOrFail($id)->delete();
        return redirect()->route('laboratorio.index')->with('success', 'Registro eliminado correctamente.');
    }
    
    public function destroyProductoTerminado($id)
    {
        LaboratorioProductoTerminado::findOrFail($id)->delete();
        return redirect()->route('laboratorio.index')->with('success', 'Registro eliminado correctamente.');
    }
    
    public function destroyPozo($id)
    {
        LaboratorioPozo::findOrFail($id)->delete();
        return redirect()->route('laboratorio.index')->with('success', 'Registro eliminado correctamente.');
    }

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

    public function marcarLeidas()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $user->novedades_leidas_hasta = now();
        $user->save();

        return back()->with('success', 'Novedades marcadas como leídas.');
    }
}
