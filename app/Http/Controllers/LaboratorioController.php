<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Novedad;
use Illuminate\Support\Facades\Auth;

class LaboratorioController extends Controller
{
    public function index()
    {
        // ---------------------------------------------------------
        // 1. INSUMOS (modulo_id = 1)
        // ---------------------------------------------------------
        $valoresInsumos = \App\Models\LabValor::with(['medicion.insumo', 'medicion.tipoMedicion'])
            ->whereHas('medicion', function($q) { $q->where('modulo_id', 1); })
            ->orderBy('fecha', 'desc')
            ->get();
            
        $insumosAgrupados = $valoresInsumos->groupBy(function($valor) {
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

        $medicionesConfigInsumos = \App\Models\LabMedicion::with('tipoMedicion', 'insumo')
            ->where('modulo_id', 1)->where('activo', true)->get();
        $tiposInsumos = \App\Models\LabInsumo::all();
        $insumoFields = [];
        foreach ($medicionesConfigInsumos as $config) {
            if ($config->tipo_medicion_id == 10) continue; 
            $insumoFields[] = [
                'name' => 'medicion_' . $config->id,
                'label' => mb_strtoupper($config->tipoMedicion->nombre),
                'classes' => 'f-insumo-' . $config->insumo_id,
                'show' => old('tipo_insumo') == $config->insumo_id,
            ];
        }

        // ---------------------------------------------------------
        // 2. AGUA CRUDA (modulo_id = 2)
        // ---------------------------------------------------------
        $valoresAguaCruda = \App\Models\LabValor::with(['medicion.tipoMedicion'])
            ->whereHas('medicion', function($q) { $q->where('modulo_id', 2); })
            ->orderBy('fecha', 'desc')->get();
            
        $aguaCruda = $valoresAguaCruda->groupBy('fecha')->map(function($grupo) {
            $first = $grupo->first();
            $obj = (object) ['id' => $first->fecha, 'fecha' => $first->fecha];
            foreach ($grupo as $v) {
                $prop = 'medicion_' . $v->medicion_id;
                $obj->$prop = $v->valor;
            }
            return $obj;
        })->take(24)->values();
        
        $medicionesConfigAguaCruda = \App\Models\LabMedicion::with('tipoMedicion')
            ->where('modulo_id', 2)->where('activo', true)->get();

        // ---------------------------------------------------------
        // 3. PRODUCTO TERMINADO (modulo_id = 3)
        // ---------------------------------------------------------
        $valoresProducto = \App\Models\LabValor::with(['medicion.tipoMedicion'])
            ->whereHas('medicion', function($q) { $q->where('modulo_id', 3); })
            ->orderBy('fecha', 'desc')->get();
            
        $productoTerminado = $valoresProducto->groupBy('fecha')->map(function($grupo) {
            $first = $grupo->first();
            $obj = (object) ['id' => $first->fecha, 'fecha' => $first->fecha];
            foreach ($grupo as $v) {
                $prop = 'medicion_' . $v->medicion_id;
                $obj->$prop = $v->valor;
            }
            return $obj;
        })->take(24)->values();
        
        $medicionesConfigProducto = \App\Models\LabMedicion::with('tipoMedicion')
            ->where('modulo_id', 3)->where('activo', true)->get();

        // ---------------------------------------------------------
        // 4. POZOS (modulo_id = 4)
        // ---------------------------------------------------------
        $valoresPozos = \App\Models\LabValor::with(['medicion.tipoMedicion', 'medicion.pozo'])
            ->whereHas('medicion', function($q) { $q->where('modulo_id', 4); })
            ->orderBy('fecha', 'desc')->get();
            
        $pozos = $valoresPozos->groupBy(function($v) { return $v->fecha . '_' . $v->medicion->pozo_id; })->map(function($grupo) {
            $first = $grupo->first();
            $obj = (object) [
                'id' => $first->fecha . '_' . $first->medicion->pozo_id,
                'fecha' => $first->fecha,
                'pozo_id' => $first->medicion->pozo_id,
                'pozo_numero' => $first->medicion->pozo->nombre,
            ];
            foreach ($grupo as $v) {
                $prop = 'medicion_' . $v->medicion_id;
                $obj->$prop = $v->valor;
            }
            return $obj;
        })->take(24)->values();
        
        $tiposPozos = \App\Models\LabPozo::where('activo', true)->get();
        $medicionesConfigPozos = \App\Models\LabMedicion::with('tipoMedicion', 'pozo')
            ->where('modulo_id', 4)->where('activo', true)->get();

        // ---------------------------------------------------------
        // Novedades
        // ---------------------------------------------------------
        $ultimasNovedades = Novedad::with('user')->latest()->take(10)->get();
        $unreadQuery = Novedad::where('user_id', '!=', Auth::id())
            ->where('created_at', '>=', now()->subHours(16));
        if (Auth::user()->novedades_leidas_hasta) {
            $unreadQuery->where('created_at', '>', Auth::user()->novedades_leidas_hasta);
        }
        $novedadesRecientes = $unreadQuery->count();

        return view('laboratorio.index', compact(
            'tiposInsumos', 'insumoFields', 'insumos', 'medicionesConfigInsumos',
            'aguaCruda', 'medicionesConfigAguaCruda', 
            'productoTerminado', 'medicionesConfigProducto', 
            'pozos', 'tiposPozos', 'medicionesConfigPozos',
            'ultimasNovedades', 'novedadesRecientes'
        ));
    }

    // ---------------------------------------------------------
    // STORE METHODS
    // ---------------------------------------------------------

    public function storeInsumo(Request $request)
    {
        $tipo = $request->input('tipo_insumo'); 
        $fecha = $request->input('fecha');
        $observaciones = $request->input('observaciones');
        
        $configuraciones = \App\Models\LabMedicion::where('modulo_id', 1)
            ->where('insumo_id', $tipo)->where('activo', true)->get();
            
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
                \App\Models\LabValor::create(['fecha' => $fecha, 'medicion_id' => $config->id, 'valor' => (string) $valorStr, 'observaciones' => $observaciones]);
            }
        }

        return redirect()->route('laboratorio.index')->with('success', 'Registro de Insumo guardado correctamente.');
    }

    public function storeAguaCruda(Request $request)
    {
        $fecha = $request->input('fecha');
        $configuraciones = \App\Models\LabMedicion::where('modulo_id', 2)->where('activo', true)->get();
        foreach ($configuraciones as $config) {
            $inputName = 'medicion_' . $config->id;
            if ($request->has($inputName) && $request->input($inputName) !== null) {
                \App\Models\LabValor::create(['fecha' => $fecha, 'medicion_id' => $config->id, 'valor' => (string) $request->input($inputName)]);
            }
        }
        return redirect()->route('laboratorio.index')->with('success', 'Registro de Agua Cruda guardado correctamente.');
    }

    public function storeProductoTerminado(Request $request)
    {
        $fecha = $request->input('fecha');
        $configuraciones = \App\Models\LabMedicion::where('modulo_id', 3)->where('activo', true)->get();
        foreach ($configuraciones as $config) {
            $inputName = 'medicion_' . $config->id;
            if ($request->has($inputName) && $request->input($inputName) !== null) {
                \App\Models\LabValor::create(['fecha' => $fecha, 'medicion_id' => $config->id, 'valor' => (string) $request->input($inputName)]);
            }
        }
        return redirect()->route('laboratorio.index')->with('success', 'Registro de Producto Terminado guardado correctamente.');
    }

    public function storePozo(Request $request)
    {
        $fecha = $request->input('fecha');
        $pozo_id = $request->input('pozo_numero'); // Now sending ID
        $configuraciones = \App\Models\LabMedicion::where('modulo_id', 4)->where('pozo_id', $pozo_id)->where('activo', true)->get();
        foreach ($configuraciones as $config) {
            $inputName = 'medicion_' . $config->id;
            if ($request->has($inputName) && $request->input($inputName) !== null) {
                \App\Models\LabValor::create(['fecha' => $fecha, 'medicion_id' => $config->id, 'valor' => (string) $request->input($inputName)]);
            }
        }
        return redirect()->route('laboratorio.index')->with('success', 'Registro de Pozo guardado correctamente.');
    }
    
    // ---------------------------------------------------------
    // DESTROY METHODS
    // ---------------------------------------------------------

    public function destroyInsumo($tipo, $id)
    {
        $parts = explode('_', $id);
        if (count($parts) == 2) {
            $fecha = $parts[0];
            $insumo_id = $parts[1];
            $medicionesIds = \App\Models\LabMedicion::where('modulo_id', 1)->where('insumo_id', $insumo_id)->pluck('id');
            \App\Models\LabValor::whereIn('medicion_id', $medicionesIds)->where('fecha', $fecha)->delete();
        }
        return redirect()->route('laboratorio.index')->with('success', 'Registro eliminado correctamente.');
    }
    
    public function destroyAguaCruda($id)
    {
        // $id is just the date, e.g. "2026-08-20"
        $medicionesIds = \App\Models\LabMedicion::where('modulo_id', 2)->pluck('id');
        \App\Models\LabValor::whereIn('medicion_id', $medicionesIds)->where('fecha', $id)->delete();
        return redirect()->route('laboratorio.index')->with('success', 'Registro eliminado correctamente.');
    }
    
    public function destroyProductoTerminado($id)
    {
        $medicionesIds = \App\Models\LabMedicion::where('modulo_id', 3)->pluck('id');
        \App\Models\LabValor::whereIn('medicion_id', $medicionesIds)->where('fecha', $id)->delete();
        return redirect()->route('laboratorio.index')->with('success', 'Registro eliminado correctamente.');
    }
    
    public function destroyPozo($id)
    {
        // $id is "fecha_pozoId"
        $parts = explode('_', $id);
        if (count($parts) == 2) {
            $fecha = $parts[0];
            $pozo_id = $parts[1];
            $medicionesIds = \App\Models\LabMedicion::where('modulo_id', 4)->where('pozo_id', $pozo_id)->pluck('id');
            \App\Models\LabValor::whereIn('medicion_id', $medicionesIds)->where('fecha', $fecha)->delete();
        }
        return redirect()->route('laboratorio.index')->with('success', 'Registro eliminado correctamente.');
    }

    public function storeNovedad(Request $request)
    {
        $request->validate(['mensaje' => 'required|string|max:1000'], ['mensaje.required' => 'Debe escribir un mensaje para la novedad.', 'mensaje.max' => 'La novedad es demasiado larga (máximo 1000 caracteres).']);
        Novedad::create(['user_id' => Auth::id(), 'mensaje' => $request->mensaje]);
        return back()->with('success', 'Novedad registrada correctamente.');
    }

    public function destroyNovedad($id)
    {
        $novedad = Novedad::findOrFail($id);
        if ($novedad->user_id !== Auth::id()) { return back()->with('error', 'No tienes permisos para borrar esta novedad.'); }
        if ($novedad->created_at->lt(now()->subHours(2))) { return back()->with('error', 'No se puede borrar una novedad con más de 2 horas de antigüedad.'); }
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
