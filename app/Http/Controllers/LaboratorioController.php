<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Novedad;
use App\Models\LabValor;
use App\Models\LabMedicion;
use App\Models\LabInsumo;
use App\Models\LabPozo;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class LaboratorioController extends Controller
{
    public function index()
    {
        // ---------------------------------------------------------
        // 1. INSUMOS (modulo_id = 1)
        // ---------------------------------------------------------
        $valoresInsumos = LabValor::with(['medicion.insumo', 'medicion.tipoMedicion'])
            ->whereHas('medicion', function($q) { $q->where('modulo_id', 1); })
            ->orderBy('fecha', 'desc')
            ->get();
            
        $insumosAgrupados = $valoresInsumos->groupBy(function($valor) {
            return $valor->fecha . '_' . $valor->medicion->insumo_id;
        })->map(function($grupo) {
            $first = $grupo->first();
            $contramuestra = $grupo->firstWhere('medicion.tipoMedicion.es_booleano', true);
            $obj = (object) [
                'id' => $first->fecha . '_' . $first->medicion->insumo_id,
                'fecha' => $first->fecha,
                'insumo_id' => $first->medicion->insumo_id,
                'nombre_insumo' => $first->medicion->insumo->nombre,
                'tipo_insumo' => $first->medicion->insumo_id,
                'preparacion_archivo_contramuestra' => $contramuestra ? ($contramuestra->valor === '1') : false,
                'observaciones' => $grupo->firstWhere('observaciones', '!=', null)?->observaciones ?? $first->observaciones,
            ];
            foreach ($grupo as $v) {
                $prop = 'medicion_' . $v->medicion_id;
                $obj->$prop = $v->valor;
            }
            return $obj;
        })->take(24)->values();
        $insumos = $insumosAgrupados;

        $medicionesConfigInsumos = LabMedicion::with('tipoMedicion', 'insumo')
            ->where('modulo_id', 1)->where('activo', true)->get();
        $tiposInsumos = LabInsumo::all();
        $insumoFields = [];
        foreach ($medicionesConfigInsumos as $config) {
            if ($config->tipoMedicion?->es_booleano) continue; 
            $insumoFields[] = [
                'name' => 'medicion_' . $config->id,
                'label' => mb_strtoupper($config->tipoMedicion->nombre),
                'classes' => 'f-insumo-' . $config->insumo_id,
                'show' => old('tipo_insumo') == $config->insumo_id,
                'isText' => $config->tipoMedicion?->es_texto ?? false,
                'min' => $config->min,
                'max' => $config->max,
            ];
        }

        // ---------------------------------------------------------
        // 2. AGUA CRUDA (modulo_id = 2)
        // ---------------------------------------------------------
        $valoresAguaCruda = LabValor::with(['medicion.tipoMedicion'])
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
        
        $medicionesConfigAguaCruda = LabMedicion::with('tipoMedicion')
            ->where('modulo_id', 2)->where('activo', true)->get();
        
        $categoriasAguaCruda = $medicionesConfigAguaCruda
            ->groupBy(fn($c) => $c->tipoMedicion->categoria ?? 'FISICOQUÍMICO')
            ->map(fn($mediciones) => [
                'clases_grid' => 'grid-cols-2 md:grid-cols-4',
                'mediciones' => $mediciones
            ]);

        // ---------------------------------------------------------
        // 3. PRODUCTO TERMINADO (modulo_id = 3)
        // ---------------------------------------------------------
        $valoresProducto = LabValor::with(['medicion.tipoMedicion'])
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
        
        $medicionesConfigProducto = LabMedicion::with('tipoMedicion')
            ->where('modulo_id', 3)->where('activo', true)->get();
        
        $categoriasProducto = $medicionesConfigProducto
            ->groupBy(fn($c) => $c->tipoMedicion->categoria ?? 'FISICOQUÍMICO')
            ->map(fn($mediciones) => [
                'clases_grid' => 'grid-cols-2 md:grid-cols-4',
                'mediciones' => $mediciones
            ]);

        // ---------------------------------------------------------
        // 4. POZOS (modulo_id = 4)
        // ---------------------------------------------------------
        $valoresPozos = LabValor::with(['medicion.tipoMedicion', 'medicion.pozo'])
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
        
        $tiposPozos = LabPozo::where('activo', true)->get();
        $medicionesConfigPozos = LabMedicion::with('tipoMedicion', 'pozo')
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
            'aguaCruda', 'medicionesConfigAguaCruda', 'categoriasAguaCruda',
            'productoTerminado', 'medicionesConfigProducto', 'categoriasProducto',
            'pozos', 'tiposPozos', 'medicionesConfigPozos',
            'ultimasNovedades', 'novedadesRecientes'
        ));
    }

    private function buildValidationRulesAndAttributes($configuraciones, Request $request, array $initialRules = [])
    {
        $rules = $initialRules;
        $customAttributes = [];
        $filledCount = 0;

        foreach ($configuraciones as $config) {
            $inputName = 'medicion_' . $config->id;
            $customAttributes[$inputName] = $config->tipoMedicion->nombre;

            if ($config->tipoMedicion?->es_booleano) {
                $rules[$inputName] = 'nullable';
            } elseif ($config->tipoMedicion?->es_texto) {
                $rules[$inputName] = 'nullable|string|max:100';
                if ($request->filled($inputName)) {
                    $filledCount++;
                }
            } else {
                $min = $config->min ?? 0;
                $max = $config->max ?? 1000;
                $rules[$inputName] = "nullable|numeric|between:$min,$max";
                if ($request->filled($inputName)) {
                    $filledCount++;
                }
            }
        }

        return [$rules, $customAttributes, $filledCount];
    }

    // ---------------------------------------------------------
    // STORE METHODS
    // ---------------------------------------------------------

    public function storeInsumo(Request $request)
    {
        $tipo = $request->input('tipo_insumo'); 
        $fecha = $request->input('fecha');
        
        $configuraciones = LabMedicion::with('tipoMedicion')
            ->where('modulo_id', 1)
            ->where('insumo_id', $tipo)
            ->where('activo', true)
            ->get();

        [$rules, $customAttributes, $filledCount] = $this->buildValidationRulesAndAttributes(
            $configuraciones,
            $request,
            [
                'tipo_insumo' => 'required|exists:lab_insumos,id',
                'fecha' => 'required|date',
                'observaciones' => 'nullable|string|max:1000',
            ]
        );
        $customAttributes['tipo_insumo'] = 'Insumo';
        $customAttributes['fecha'] = 'Fecha';
        $customAttributes['observaciones'] = 'Observaciones';

        $validator = Validator::make($request->all(), $rules, [], $customAttributes);

        $validator->after(function ($validator) use ($filledCount, $tipo) {
            if ($tipo && $filledCount === 0) {
                $validator->errors()->add('mediciones', 'Debe cargar al menos una medición para el insumo.');
            }
        });

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $observaciones = $request->input('observaciones');
        
        foreach ($configuraciones as $config) {
            $valorStr = null;
            if ($config->tipoMedicion?->es_booleano) {
                $valorStr = $request->has('preparacion_archivo_contramuestra') ? '1' : '0';
            } else {
                $inputName = 'medicion_' . $config->id;
                if ($request->has($inputName) && $request->input($inputName) !== null) {
                    $valorStr = $request->input($inputName);
                }
            }
            if ($valorStr !== null) {
                LabValor::create(['fecha' => $fecha, 'medicion_id' => $config->id, 'valor' => (string) $valorStr, 'observaciones' => $observaciones]);
            }
        }

        return redirect()->route('laboratorio.index')->with('success', 'Registro de Insumo guardado correctamente.');
    }

    public function storeAguaCruda(Request $request)
    {
        $fecha = $request->input('fecha');
        
        $configuraciones = LabMedicion::with('tipoMedicion')->where('modulo_id', 2)->where('activo', true)->get();

        [$rules, $customAttributes, $filledCount] = $this->buildValidationRulesAndAttributes(
            $configuraciones,
            $request,
            ['fecha' => 'required|date']
        );
        $customAttributes['fecha'] = 'Fecha';

        $validator = Validator::make($request->all(), $rules, [], $customAttributes);

        $validator->after(function ($validator) use ($filledCount) {
            if ($filledCount === 0) {
                $validator->errors()->add('mediciones', 'Debe cargar al menos una medición para agua cruda.');
            }
        });

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        foreach ($configuraciones as $config) {
            $inputName = 'medicion_' . $config->id;
            if ($request->has($inputName) && $request->input($inputName) !== null) {
                LabValor::create(['fecha' => $fecha, 'medicion_id' => $config->id, 'valor' => (string) $request->input($inputName)]);
            }
        }
        return redirect()->route('laboratorio.index')->with('success', 'Registro de Agua Cruda guardado correctamente.');
    }

    public function storeProductoTerminado(Request $request)
    {
        $fecha = $request->input('fecha');
        
        $configuraciones = LabMedicion::with('tipoMedicion')->where('modulo_id', 3)->where('activo', true)->get();

        [$rules, $customAttributes, $filledCount] = $this->buildValidationRulesAndAttributes(
            $configuraciones,
            $request,
            ['fecha' => 'required|date']
        );
        $customAttributes['fecha'] = 'Fecha';

        $validator = Validator::make($request->all(), $rules, [], $customAttributes);

        $validator->after(function ($validator) use ($filledCount) {
            if ($filledCount === 0) {
                $validator->errors()->add('mediciones', 'Debe cargar al menos una medición para producto terminado.');
            }
        });

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        foreach ($configuraciones as $config) {
            $inputName = 'medicion_' . $config->id;
            if ($request->has($inputName) && $request->input($inputName) !== null) {
                LabValor::create(['fecha' => $fecha, 'medicion_id' => $config->id, 'valor' => (string) $request->input($inputName)]);
            }
        }
        return redirect()->route('laboratorio.index')->with('success', 'Registro de Producto Terminado guardado correctamente.');
    }

    public function storePozo(Request $request)
    {
        $fecha = $request->input('fecha');
        $pozo_id = $request->input('pozo_numero');
        
        $configuraciones = LabMedicion::with('tipoMedicion')->where('modulo_id', 4)->where('pozo_id', $pozo_id)->where('activo', true)->get();

        [$rules, $customAttributes, $filledCount] = $this->buildValidationRulesAndAttributes(
            $configuraciones,
            $request,
            [
                'fecha' => 'required|date',
                'pozo_numero' => 'required|exists:lab_pozos,id',
            ]
        );
        $customAttributes['fecha'] = 'Fecha';
        $customAttributes['pozo_numero'] = 'Pozo';

        $validator = Validator::make($request->all(), $rules, [], $customAttributes);

        $validator->after(function ($validator) use ($filledCount, $pozo_id) {
            if ($pozo_id && $filledCount === 0) {
                $validator->errors()->add('mediciones', 'Debe cargar al menos una medición para el pozo.');
            }
        });

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        foreach ($configuraciones as $config) {
            $inputName = 'medicion_' . $config->id;
            if ($request->has($inputName) && $request->input($inputName) !== null) {
                LabValor::create(['fecha' => $fecha, 'medicion_id' => $config->id, 'valor' => (string) $request->input($inputName)]);
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
            $medicionesIds = LabMedicion::where('modulo_id', 1)->where('insumo_id', $insumo_id)->pluck('id');
            LabValor::whereIn('medicion_id', $medicionesIds)->where('fecha', $fecha)->delete();
        }
        return redirect()->route('laboratorio.index')->with('success', 'Registro eliminado correctamente.');
    }
    
    public function destroyAguaCruda($id)
    {
        $medicionesIds = LabMedicion::where('modulo_id', 2)->pluck('id');
        LabValor::whereIn('medicion_id', $medicionesIds)->where('fecha', $id)->delete();
        return redirect()->route('laboratorio.index')->with('success', 'Registro eliminado correctamente.');
    }
    
    public function destroyProductoTerminado($id)
    {
        $medicionesIds = LabMedicion::where('modulo_id', 3)->pluck('id');
        LabValor::whereIn('medicion_id', $medicionesIds)->where('fecha', $id)->delete();
        return redirect()->route('laboratorio.index')->with('success', 'Registro eliminado correctamente.');
    }
    
    public function destroyPozo($id)
    {
        $parts = explode('_', $id);
        if (count($parts) == 2) {
            $fecha = $parts[0];
            $pozo_id = $parts[1];
            $medicionesIds = LabMedicion::where('modulo_id', 4)->where('pozo_id', $pozo_id)->pluck('id');
            LabValor::whereIn('medicion_id', $medicionesIds)->where('fecha', $fecha)->delete();
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
