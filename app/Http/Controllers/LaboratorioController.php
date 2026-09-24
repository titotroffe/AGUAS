<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Novedad;
use App\Models\LabValor;
use App\Models\LabMedicion;
use App\Models\LabInsumo;
use App\Models\LabFrecuencia;
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
            ->where('modulo_id', 1)->where('activo', true)->get()->sortBy(fn($c) => strtolower(\Illuminate\Support\Str::ascii($c->tipoMedicion->nombre)))->values();
        $tiposInsumos = LabInsumo::orderBy('nombre')->get();
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
        
        $frecuenciasAguaCruda = LabFrecuencia::orderBy('nombre')->get();

        $medicionesConfigAguaCruda = LabMedicion::with('tipoMedicion', 'frecuencia')
            ->where('modulo_id', 2)->where('activo', true)->get()->sortBy(fn($c) => strtolower(\Illuminate\Support\Str::ascii($c->tipoMedicion->nombre)))->values();
        
        $categoriasAguaCruda = $medicionesConfigAguaCruda
            ->groupBy('frecuencia_id')
            ->map(function($medicionesFrecuencia) {
                return $medicionesFrecuencia->groupBy(fn($c) => $c->tipoMedicion->categoria ?? 'FISICOQUÍMICO')
                    ->map(fn($mediciones) => [
                        'clases_grid' => 'grid-cols-2 md:grid-cols-4',
                        'mediciones' => $mediciones
                    ]);
            });

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
        
        $frecuenciasProducto = LabFrecuencia::orderBy('nombre')->get();
        
        $medicionesConfigProducto = LabMedicion::with('tipoMedicion', 'frecuencia')
            ->where('modulo_id', 3)->where('activo', true)->get()->sortBy(fn($c) => strtolower(\Illuminate\Support\Str::ascii($c->tipoMedicion->nombre)))->values();
        
        $categoriasProducto = $medicionesConfigProducto
            ->groupBy('frecuencia_id')
            ->map(function($medicionesFrecuencia) {
                return $medicionesFrecuencia->groupBy(fn($c) => $c->tipoMedicion->categoria ?? 'FISICOQUÍMICO')
                    ->map(fn($mediciones) => [
                        'clases_grid' => 'grid-cols-2 md:grid-cols-4',
                        'mediciones' => $mediciones
                    ]);
            });

        // ---------------------------------------------------------
        // 4. AGUA POTABLE DE RED (modulo_id = 5)
        // ---------------------------------------------------------
        $valoresAguaRed = LabValor::with(['medicion.tipoMedicion'])
            ->whereHas('medicion', function($q) { $q->where('modulo_id', 5); })
            ->orderBy('fecha', 'desc')->get();
            
        $aguaRed = $valoresAguaRed->groupBy('fecha')->map(function($grupo) {
            $first = $grupo->first();
            $obj = (object) ['id' => $first->fecha, 'fecha' => $first->fecha];
            foreach ($grupo as $v) {
                $prop = 'medicion_' . $v->medicion_id;
                $obj->$prop = $v->valor;
            }
            return $obj;
        })->take(24)->values();
        
        $frecuenciasAguaRed = LabFrecuencia::orderBy('nombre')->get();
        
        $medicionesConfigAguaRed = LabMedicion::with('tipoMedicion', 'frecuencia')
            ->where('modulo_id', 5)->where('activo', true)->get()->sortBy(fn($c) => strtolower(\Illuminate\Support\Str::ascii($c->tipoMedicion->nombre)))->values();
        
        $categoriasAguaRed = $medicionesConfigAguaRed
            ->groupBy('frecuencia_id')
            ->map(function($medicionesFrecuencia) {
                return $medicionesFrecuencia->groupBy(fn($c) => $c->tipoMedicion->categoria ?? 'FISICOQUÍMICO')
                    ->map(fn($mediciones) => [
                        'clases_grid' => 'grid-cols-2 md:grid-cols-4',
                        'mediciones' => $mediciones
                    ]);
            });

        // ---------------------------------------------------------
        // 5. POZOS (modulo_id = 4)
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
        
        $tiposPozos = LabPozo::where('activo', true)->orderBy('nombre')->get();
        $frecuenciasPozos = LabFrecuencia::whereIn('id', [2, 3])->orderBy('nombre')->get();
        
        $medicionesConfigPozos = LabMedicion::with('tipoMedicion', 'pozo', 'frecuencia')
            ->where('modulo_id', 4)->where('activo', true)->get()->sortBy(fn($c) => strtolower(\Illuminate\Support\Str::ascii($c->tipoMedicion->nombre)))->values();

        $categoriasPozos = $medicionesConfigPozos
            ->groupBy('pozo_id')
            ->map(function($pozoMediciones) {
                return $pozoMediciones->groupBy('frecuencia_id')
                    ->map(function($frecuenciaMediciones) {
                        return $frecuenciaMediciones->groupBy(fn($c) => $c->tipoMedicion->categoria ?? 'FISICOQUÍMICO')
                            ->map(fn($mediciones) => [
                                'clases_grid' => 'grid-cols-2 md:grid-cols-4',
                                'mediciones' => $mediciones
                            ]);
                    });
            });

        // ---------------------------------------------------------
        // 6. CONTROL ESCRITURADO (modulo_id = 6)
        // ---------------------------------------------------------
        $valoresEscriturado = LabValor::with(['medicion.tipoMedicion'])
            ->whereHas('medicion', function($q) { $q->where('modulo_id', 6); })
            ->orderBy('fecha', 'desc')->get();
            
        $controlEscriturado = $valoresEscriturado->groupBy('fecha')->map(function($grupo) {
            $first = $grupo->first();
            $obj = (object) ['id' => $first->fecha, 'fecha' => $first->fecha];
            foreach ($grupo as $v) {
                $prop = 'medicion_' . $v->medicion_id;
                $obj->$prop = $v->valor;
            }
            return $obj;
        })->take(24)->values();
        
        $frecuenciasEscriturado = LabFrecuencia::orderBy('nombre')->get();
        $medicionesConfigEscriturado = LabMedicion::with('tipoMedicion', 'frecuencia')
            ->where('modulo_id', 6)->where('activo', true)->get()->sortBy(fn($c) => strtolower(\Illuminate\Support\Str::ascii($c->tipoMedicion->nombre)))->values();
        
        $categoriasEscriturado = $medicionesConfigEscriturado->groupBy('frecuencia_id')->map(function($medicionesFrecuencia) {
            return $medicionesFrecuencia->groupBy(fn($c) => $c->tipoMedicion->categoria ?? 'FISICOQUÍMICO')
                ->map(fn($mediciones) => ['clases_grid' => 'grid-cols-2 md:grid-cols-4', 'mediciones' => $mediciones]);
        });

        // ---------------------------------------------------------
        // 7. CONTROL DE TANQUES (modulo_id = 7)
        // ---------------------------------------------------------
        $valoresTanques = LabValor::with(['medicion.tipoMedicion'])
            ->whereHas('medicion', function($q) { $q->where('modulo_id', 7); })
            ->orderBy('fecha', 'desc')->get();
            
        $controlTanques = $valoresTanques->groupBy('fecha')->map(function($grupo) {
            $first = $grupo->first();
            $obj = (object) ['id' => $first->fecha, 'fecha' => $first->fecha];
            foreach ($grupo as $v) {
                $prop = 'medicion_' . $v->medicion_id;
                $obj->$prop = $v->valor;
            }
            return $obj;
        })->take(24)->values();
        
        $frecuenciasTanques = LabFrecuencia::orderBy('nombre')->get();
        $medicionesConfigTanques = LabMedicion::with('tipoMedicion', 'frecuencia')
            ->where('modulo_id', 7)->where('activo', true)->get()->sortBy(fn($c) => strtolower(\Illuminate\Support\Str::ascii($c->tipoMedicion->nombre)))->values();
        
        $categoriasTanques = $medicionesConfigTanques->groupBy('frecuencia_id')->map(function($medicionesFrecuencia) {
            return $medicionesFrecuencia->groupBy(fn($c) => $c->tipoMedicion->categoria ?? 'FISICOQUÍMICO')
                ->map(fn($mediciones) => ['clases_grid' => 'grid-cols-2 md:grid-cols-4', 'mediciones' => $mediciones]);
        });

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
            'frecuenciasAguaCruda', 'aguaCruda', 'medicionesConfigAguaCruda', 'categoriasAguaCruda',
            'productoTerminado', 'medicionesConfigProducto', 'categoriasProducto', 'frecuenciasProducto',
            'aguaRed', 'medicionesConfigAguaRed', 'categoriasAguaRed', 'frecuenciasAguaRed',
            'pozos', 'tiposPozos', 'medicionesConfigPozos', 'frecuenciasPozos', 'categoriasPozos',
            'controlEscriturado', 'medicionesConfigEscriturado', 'categoriasEscriturado', 'frecuenciasEscriturado',
            'frecuenciasTanques', 'controlTanques', 'medicionesConfigTanques', 'categoriasTanques',
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
                LabValor::create(['user_id' => Auth::id(), 'fecha' => $fecha, 'medicion_id' => $config->id, 'valor' => (string) $valorStr, 'observaciones' => $observaciones]);
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
                LabValor::create(['user_id' => Auth::id(), 'fecha' => $fecha, 'medicion_id' => $config->id, 'valor' => (string) $request->input($inputName)]);
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
                LabValor::create(['user_id' => Auth::id(), 'fecha' => $fecha, 'medicion_id' => $config->id, 'valor' => (string) $request->input($inputName)]);
            }
        }
        return redirect()->route('laboratorio.index')->with('success', 'Registro de Producto Terminado guardado correctamente.');
    }

    public function storeAguaRed(Request $request)
    {
        $fecha = $request->input('fecha');
        
        $configuraciones = LabMedicion::with('tipoMedicion')->where('modulo_id', 5)->where('activo', true)->get();

        [$rules, $customAttributes, $filledCount] = $this->buildValidationRulesAndAttributes(
            $configuraciones,
            $request,
            ['fecha' => 'required|date']
        );
        $customAttributes['fecha'] = 'Fecha';

        $validator = Validator::make($request->all(), $rules, [], $customAttributes);

        $validator->after(function ($validator) use ($filledCount) {
            if ($filledCount === 0) {
                $validator->errors()->add('mediciones', 'Debe cargar al menos una medición para agua de red.');
            }
        });

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        foreach ($configuraciones as $config) {
            $inputName = 'medicion_' . $config->id;
            if ($request->has($inputName) && $request->input($inputName) !== null) {
                LabValor::create(['user_id' => Auth::id(), 'fecha' => $fecha, 'medicion_id' => $config->id, 'valor' => (string) $request->input($inputName)]);
            }
        }
        return redirect()->route('laboratorio.index')->with('success', 'Registro de Agua de Red guardado correctamente.');
    }

    public function storePozo(Request $request)
    {
        $fecha = $request->input('fecha');
        $pozo_id = $request->input('pozo_numero');
        $frecuencia_id = $request->input('frecuencia_pozos');
        
        $configuraciones = LabMedicion::with('tipoMedicion')->where('modulo_id', 4)->where('pozo_id', $pozo_id)->where('frecuencia_id', $frecuencia_id)->where('activo', true)->get();

        [$rules, $customAttributes, $filledCount] = $this->buildValidationRulesAndAttributes(
            $configuraciones,
            $request,
            [
                'fecha' => 'required|date',
                'pozo_numero' => 'required|exists:lab_pozos,id',
                'frecuencia_pozos' => 'required|exists:lab_frecuencias,id',
            ]
        );
        $customAttributes['fecha'] = 'Fecha';
        $customAttributes['pozo_numero'] = 'Pozo';
        $customAttributes['frecuencia_pozos'] = 'Frecuencia';

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
                LabValor::create(['user_id' => Auth::id(), 'fecha' => $fecha, 'medicion_id' => $config->id, 'valor' => (string) $request->input($inputName)]);
            }
        }
        return redirect()->route('laboratorio.index')->with('success', 'Registro de Pozo guardado correctamente.');
    }

    public function storeControlEscriturado(Request $request)
    {
        $fecha = $request->input('fecha');
        
        $configuraciones = LabMedicion::with('tipoMedicion')->where('modulo_id', 6)->where('activo', true)->get();

        [$rules, $customAttributes, $filledCount] = $this->buildValidationRulesAndAttributes(
            $configuraciones,
            $request,
            ['fecha' => 'required|date']
        );
        $customAttributes['fecha'] = 'Fecha';

        $validator = Validator::make($request->all(), $rules, [], $customAttributes);

        $validator->after(function ($validator) use ($filledCount) {
            if ($filledCount === 0) {
                $validator->errors()->add('mediciones', 'Debe cargar al menos una medición para control escriturado.');
            }
        });

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        foreach ($configuraciones as $config) {
            $inputName = 'medicion_' . $config->id;
            if ($request->has($inputName) && $request->input($inputName) !== null) {
                LabValor::create(['user_id' => Auth::id(), 'fecha' => $fecha, 'medicion_id' => $config->id, 'valor' => (string) $request->input($inputName)]);
            }
        }
        return redirect()->route('laboratorio.index')->with('success', 'Registro de Control Escriturado guardado correctamente.');
    }

    public function storeControlTanques(Request $request)
    {
        $fecha = $request->input('fecha');
        
        $configuraciones = LabMedicion::with('tipoMedicion')->where('modulo_id', 7)->where('activo', true)->get();

        [$rules, $customAttributes, $filledCount] = $this->buildValidationRulesAndAttributes(
            $configuraciones,
            $request,
            ['fecha' => 'required|date']
        );
        $customAttributes['fecha'] = 'Fecha';

        $validator = Validator::make($request->all(), $rules, [], $customAttributes);

        $validator->after(function ($validator) use ($filledCount) {
            if ($filledCount === 0) {
                $validator->errors()->add('mediciones', 'Debe cargar al menos una medición para control de tanques.');
            }
        });

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        foreach ($configuraciones as $config) {
            $inputName = 'medicion_' . $config->id;
            if ($request->has($inputName) && $request->input($inputName) !== null) {
                LabValor::create(['user_id' => Auth::id(), 'fecha' => $fecha, 'medicion_id' => $config->id, 'valor' => (string) $request->input($inputName)]);
            }
        }
        return redirect()->route('laboratorio.index')->with('success', 'Registro de Control de Tanques guardado correctamente.');
    }
    
    // ---------------------------------------------------------
    // DESTROY METHODS
    // ---------------------------------------------------------

    private function validarYBorrarRegistrosLaboratorio($valores)
    {
        if ($valores->isEmpty()) {
            return back()->with('error', 'No se encontraron registros para eliminar.');
        }

        $primerRegistro = $valores->first();

        // Verificar que el registro pertenezca al usuario logueado
        // Si user_id es nulo (registros históricos anteriores a la corrección), no permitimos borrar.
        if ($primerRegistro->user_id !== Auth::id()) {
            return back()->with('error', 'No tienes permisos para borrar este registro.');
        }

        // Verificar que haya sido cargado hace menos de 2 horas
        if ($primerRegistro->created_at->lt(now()->subHours(2))) {
            return back()->with('error', 'No se puede borrar un registro con más de 2 horas de antigüedad.');
        }

        // Usamos el collection de IDs para borrar de forma segura
        LabValor::whereIn('id', $valores->pluck('id'))->delete();

        return redirect()->route('laboratorio.index')->with('deleted', 'Registro eliminado correctamente.');
    }

    public function destroyInsumo($tipo, $id)
    {
        $parts = explode('_', $id);
        if (count($parts) == 2) {
            $fecha = $parts[0];
            $insumo_id = $parts[1];
            $medicionesIds = LabMedicion::where('modulo_id', 1)->where('insumo_id', $insumo_id)->pluck('id');
            $valores = LabValor::whereIn('medicion_id', $medicionesIds)->where('fecha', $fecha)->get();
            return $this->validarYBorrarRegistrosLaboratorio($valores);
        }
        return back()->with('error', 'Formato de ID inválido.');
    }
    
    public function destroyAguaCruda($id)
    {
        $medicionesIds = LabMedicion::where('modulo_id', 2)->pluck('id');
        $valores = LabValor::whereIn('medicion_id', $medicionesIds)->where('fecha', $id)->get();
        return $this->validarYBorrarRegistrosLaboratorio($valores);
    }
    
    public function destroyProductoTerminado($id)
    {
        $medicionesIds = LabMedicion::where('modulo_id', 3)->pluck('id');
        $valores = LabValor::whereIn('medicion_id', $medicionesIds)->where('fecha', $id)->get();
        return $this->validarYBorrarRegistrosLaboratorio($valores);
    }
    
    public function destroyAguaRed($id)
    {
        $medicionesIds = LabMedicion::where('modulo_id', 5)->pluck('id');
        $valores = LabValor::whereIn('medicion_id', $medicionesIds)->where('fecha', $id)->get();
        return $this->validarYBorrarRegistrosLaboratorio($valores);
    }
    
    public function destroyPozo($id)
    {
        $parts = explode('_', $id);
        if (count($parts) == 2) {
            $fecha = $parts[0];
            $pozo_id = $parts[1];
            $medicionesIds = LabMedicion::where('modulo_id', 4)->where('pozo_id', $pozo_id)->pluck('id');
            $valores = LabValor::whereIn('medicion_id', $medicionesIds)->where('fecha', $fecha)->get();
            return $this->validarYBorrarRegistrosLaboratorio($valores);
        }
        return back()->with('error', 'Formato de ID inválido.');
    }

    public function destroyControlEscriturado($id)
    {
        $medicionesIds = LabMedicion::where('modulo_id', 6)->pluck('id');
        $valores = LabValor::whereIn('medicion_id', $medicionesIds)->where('fecha', $id)->get();
        return $this->validarYBorrarRegistrosLaboratorio($valores);
    }

    public function destroyControlTanques($id)
    {
        $medicionesIds = LabMedicion::where('modulo_id', 7)->pluck('id');
        $valores = LabValor::whereIn('medicion_id', $medicionesIds)->where('fecha', $id)->get();
        return $this->validarYBorrarRegistrosLaboratorio($valores);
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
