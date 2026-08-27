<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laboratorio Central</title>
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <style>
        input[type=number]::-webkit-inner-spin-button, 
        input[type=number]::-webkit-outer-spin-button { 
            -webkit-appearance: none; 
            margin: 0; 
        }
        input[type=number], input[type=text] {
            -moz-appearance: textfield;
        }
        select {
            appearance: none;
            background-image: url("data:image/svg+xml;charset=utf-8,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3E%3Cpath stroke='%239ca3af' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='m6 8 4 4 4-4'/%3E%3C/svg%3E");
            background-position: right 0.5rem center;
            background-repeat: no-repeat;
            background-size: 1.5em 1.5em;
            padding-right: 2.5rem;
        }
        /* Style for toggle switch */
        .toggle-checkbox:checked {
            right: 0;
            border-color: #06b6d4;
        }
        .toggle-checkbox:checked + .toggle-label {
            background-color: #06b6d4;
        }
        /* Botón scroll-to-top */
        #btn-scroll-top {
            position: fixed;
            bottom: 2rem;
            right: 2rem;
            z-index: 9999;
            display: none;
            align-items: center;
            justify-content: center;
            width: 48px;
            height: 48px;
            background: #334155;
            border: 1px solid #64748b;
            border-radius: 50%;
            color: #fff;
            font-size: 1.25rem;
            cursor: pointer;
            box-shadow: 0 4px 24px rgba(0,0,0,0.4);
            transition: background 0.2s, transform 0.2s, opacity 0.3s;
            opacity: 0.85;
        }
        #btn-scroll-top:hover {
            background: #475569;
            transform: translateY(-3px);
            opacity: 1;
        }
        #btn-scroll-top.visible {
            display: flex;
        }
    </style>
</head>
<body class="bg-slate-800 text-slate-200 font-sans min-h-screen p-8">

    <div class="max-w-6xl mx-auto">
        
        <!-- Navegación y Título -->
        <div class="relative flex items-center justify-center mb-12">
            <a href="/menu" class="absolute left-0 top-1/2 -translate-y-1/2 bg-slate-700 hover:bg-slate-600 text-white py-2 px-6 rounded border border-slate-500 transition text-sm font-semibold">
                 ← VOLVER AL MENÚ
            </a>
            <h1 class="text-2xl font-bold text-white tracking-wider text-center m-0">LABORATORIO CENTRAL</h1>
        </div>

        <!-- Alertas Novedades -->
        @if($novedadesRecientes > 0)
            <div class="bg-blue-900/50 border border-blue-500 text-blue-200 px-6 py-4 rounded-xl mb-8 text-center shadow-lg flex items-center justify-center gap-3 cursor-pointer hover:bg-blue-800/50 transition" onclick="document.getElementById('novedades-details').open = true; document.getElementById('novedades-details').scrollIntoView({behavior: 'smooth'})">
                <i class="fa-solid fa-bell text-blue-400 text-xl animate-pulse"></i>
                <span class="font-bold tracking-wide">¡Hay {{ $novedadesRecientes }} novedad(es) del turno anterior sin leer! Haz clic aquí para ir al Boletín.</span>
            </div>
        @endif

        @if(session('success'))
            <div class="bg-emerald-900/50 border border-emerald-500 text-emerald-200 px-4 py-3 rounded mb-6 text-center text-sm font-semibold shadow-md">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="bg-red-900/50 border border-red-500 text-red-200 px-4 py-3 rounded mb-6 text-center text-sm font-semibold shadow-md">
                {{ session('error') }}
            </div>
        @endif

        @if($errors->any())
            <div class="bg-red-900/50 border border-red-500 text-red-200 px-4 py-3 rounded mb-6 text-center text-sm font-semibold shadow-md">
                <ul class="list-disc list-inside">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- 1. ANÁLISIS DE INSUMOS -->
        <details id="details-insumos" class="bg-slate-900/40 rounded-xl border border-slate-700 mb-12 shadow-2xl group overflow-hidden">
            <summary class="list-none cursor-pointer bg-slate-800/80 p-6 flex justify-between items-center text-xl font-bold text-white tracking-wider hover:bg-slate-700/50 transition border-b border-slate-700">
                <span class="text-blue-400">1. ANÁLISIS DE INSUMOS</span>
                <span class="transform transition-transform group-open:rotate-180 text-slate-400">▼</span>
            </summary>
            <div class="p-8">
                <form action="{{ route('laboratorio.storeInsumo') }}" method="POST" onsubmit="const btns = this.querySelectorAll('button[type=submit]'); btns.forEach(b => { b.disabled = true; b.innerHTML = 'GUARDANDO...'; b.classList.add('opacity-50', 'cursor-not-allowed'); });">
                    @csrf
                    <input type="hidden" name="_section" value="insumos">
                    
                    <div class="flex flex-col items-center mb-8">
                        <label class="text-[10px] font-bold mb-2 tracking-wide text-slate-400">SELECCIÓN DE INSUMO</label>
                        <select name="tipo_insumo" class="w-64 bg-slate-900 border border-slate-600 rounded p-2 text-center focus:outline-none focus:border-blue-500 text-[10px] font-bold tracking-wide text-slate-400 uppercase mb-4" required onchange="toggleInsumoFields(this.value)">
                            <option value="">Elegir Insumo</option>
                            @foreach($tiposInsumos as $tipo)
                                <option value="{{ $tipo->id }}" {{ old('tipo_insumo') == $tipo->id ? 'selected' : '' }}>{{ $tipo->nombre }}</option>
                            @endforeach
                        </select>
                        
                        <label class="text-[10px] font-bold mb-2 tracking-wide text-slate-400 mt-2">FECHA</label>
                        <input type="date" name="fecha" value="{{ old('fecha', date('Y-m-d')) }}" class="w-48 bg-slate-900 border border-slate-600 rounded p-2 text-center text-white focus:outline-none focus:border-blue-500 font-mono mb-4" required>
                        
                        <div class="flex items-center mt-2 mb-4">
                            <label class="text-[10px] font-bold tracking-wide text-slate-400 mr-4">PREPARACIÓN ARCHIVO CONTRAMUESTRA</label>
                            <div class="relative inline-block w-10 mr-2 align-middle select-none transition duration-200 ease-in">
                                <input type="checkbox" name="preparacion_archivo_contramuestra" id="toggle-contramuestra" class="toggle-checkbox absolute block w-6 h-6 rounded-full bg-white border-4 appearance-none cursor-pointer border-slate-600" value="1" {{ old('preparacion_archivo_contramuestra') ? 'checked' : '' }}/>
                                <label for="toggle-contramuestra" class="toggle-label block overflow-hidden h-6 rounded-full bg-slate-600 cursor-pointer"></label>
                            </div>
                        </div>
                    </div>


                    <!-- Campos dinámicos -->
                    <div id="campos-insumo" class="grid grid-cols-2 md:grid-cols-4 gap-8 mb-8 text-center items-start border-t border-slate-700 pt-8 {{ old('tipo_insumo') ? '' : 'hidden' }}">
                        @foreach($insumoFields as $field)
                            <div class="{{ $field['classes'] }} {{ $field['show'] ? '' : 'hidden' }} flex flex-col items-center">
                                <label class="text-[10px] font-bold mb-2 tracking-wide text-slate-400">{{ $field['label'] }}</label>
                                @if($field['isText'] ?? false)
                                    <input type="text" name="{{ $field['name'] }}" value="{{ old($field['name']) }}" class="w-24 bg-slate-900 border border-slate-600 rounded p-2 text-center text-white focus:outline-none focus:border-blue-500 font-mono mb-4" placeholder="-">
                                @else
                                    <input type="number" step="0.01" min="{{ $field['min'] ?? 0 }}" @if(isset($field['max'])) max="{{ $field['max'] }}" @endif name="{{ $field['name'] }}" value="{{ old($field['name']) }}" class="w-24 bg-slate-900 border border-slate-600 rounded p-2 text-center text-white focus:outline-none focus:border-blue-500 font-mono mb-4" placeholder="0.00">
                                @endif
                            </div>
                        @endforeach
                    </div>

                    <div class="flex justify-center mb-4">
                        <button type="submit" class="bg-blue-600 hover:bg-blue-500 text-white font-bold py-2 px-12 rounded shadow-lg transition tracking-wide text-sm">
                            CONFIRMAR INSUMO
                        </button>
                    </div>
                </form>

                <!-- Tabla Registros Insumos -->
                <div class="mt-8 bg-slate-900/50 rounded-xl border border-slate-700 p-6 shadow-2xl">
                    <h2 class="text-xl font-bold text-white text-center mb-6 tracking-wider uppercase">REGISTROS DE INSUMOS</h2>
                    <div class="overflow-x-auto">
                        <table class="w-full text-center text-sm text-slate-300 border-collapse border border-slate-700">
                            <thead class="text-xs uppercase bg-slate-800 text-slate-400 tracking-wider">
                                <tr>
                                    <th scope="col" class="py-3 px-4 border border-slate-700">Fecha</th>
                                    <th scope="col" class="py-3 px-4 border border-slate-700">Insumo</th>
                                    <th scope="col" class="py-3 px-4 border border-slate-700">Contramuestra</th>
                                    <th scope="col" class="py-3 px-4 border border-slate-700">Acciones</th>
                                </tr>
                            </thead>
                            <tbody class="font-medium">
                                @forelse($insumos as $index => $registro)
                                    <tr class="hover:bg-slate-800/40 transition insumo-row" data-index="{{ $index }}" style="{{ $index >= 8 ? 'display:none;' : '' }}">
                                        <td class="py-4 px-4 font-mono text-slate-400 border border-slate-700">{{ $registro->fecha }}</td>
                                        <td class="py-4 px-4 text-white font-semibold border border-slate-700 uppercase">{{ $registro->nombre_insumo }}</td>
                                        <td class="py-4 px-4 border border-slate-700">
                                            @if($registro->preparacion_archivo_contramuestra)
                                                <span class="text-emerald-400 font-bold">Sí</span>
                                            @else
                                                <span class="text-red-400 font-bold">No</span>
                                            @endif
                                        </td>
                                        <td class="py-4 px-4 border border-slate-700">
                                            <button type="button" onclick="toggleDetalle('detail-insumo-{{ $index }}')" class="bg-blue-600/85 hover:bg-blue-600 text-white py-1 px-3 rounded text-xs font-bold transition shadow-sm mx-1">Ver</button>
                                            <button type="button" 
                                                    onclick="confirmarEliminar('delete-insumo-form', '{{ route('laboratorio.destroyInsumo', ['tipo' => $registro->tipo_insumo, 'id' => $registro->id]) }}', '¿Seguro que deseas borrar este insumo?')"
                                                    class="bg-red-600/85 hover:bg-red-600 text-white py-1 px-3 rounded text-xs font-bold transition shadow-sm mx-1">
                                                Borrar
                                            </button>
                                        </td>
                                    </tr>
                                    <tr id="detail-insumo-{{ $index }}" class="bg-slate-800/60" style="display:none;">
                                        <td colspan="4" class="p-4 border border-slate-700 text-left">
                                            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                                                @foreach($medicionesConfigInsumos as $config)
                                                    @if($config->insumo_id == $registro->tipo_insumo && !$config->tipoMedicion?->es_booleano)
                                                        @php $val = $registro->{'medicion_'.$config->id} ?? '-'; @endphp
                                                        <div class="text-xs">
                                                            <span class="text-slate-400 font-bold block">{{ mb_strtoupper($config->tipoMedicion->nombre) }}</span>
                                                            <span class="text-white">{{ $val }}</span>
                                                        </div>
                                                    @endif
                                                @endforeach
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="4" class="py-8 text-center text-slate-500 font-semibold border border-slate-700">No hay registros cargados todavía.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    
                    @if(count($insumos) > 8)
                    <div class="flex justify-center items-center mt-6 space-x-2 text-sm font-bold text-slate-300" id="insumo-pagination">
                        <button type="button" onclick="changePage('insumo', -1, {{ count($insumos) }})" class="bg-slate-800 hover:bg-slate-700 px-3 py-1 rounded border border-slate-600 transition">&lt;</button>
                        <span id="insumo-page-indicator" class="px-4 text-blue-400 font-mono">Página 1 / {{ ceil(count($insumos) / 8) }}</span>
                        <button type="button" onclick="changePage('insumo', 1, {{ count($insumos) }})" class="bg-slate-800 hover:bg-slate-700 px-3 py-1 rounded border border-slate-600 transition">&gt;</button>
                    </div>
                    @endif
                </div>
            </div>
        </details>

        <!-- 2. TRATAMIENTO (AGUA CRUDA) -->
        <details id="details-cruda" class="bg-slate-900/40 rounded-xl border border-slate-700 mb-12 shadow-2xl group overflow-hidden">
            <summary class="list-none cursor-pointer bg-slate-800/80 p-6 flex justify-between items-center text-xl font-bold text-white tracking-wider hover:bg-slate-700/50 transition border-b border-slate-700">
                <span class="text-blue-400">2. TRATAMIENTO (Agua Cruda)</span>
                <span class="transform transition-transform group-open:rotate-180 text-slate-400">▼</span>
            </summary>
            <div class="p-8">
                <form action="{{ route('laboratorio.storeAguaCruda') }}" method="POST" onsubmit="const btns = this.querySelectorAll('button[type=submit]'); btns.forEach(b => { b.disabled = true; b.innerHTML = 'GUARDANDO...'; b.classList.add('opacity-50', 'cursor-not-allowed'); });">
                    @csrf
                    <input type="hidden" name="_section" value="cruda">
                    
                    <div class="flex flex-col items-center mb-8">
                        <label class="text-[10px] font-bold mb-2 tracking-wide text-slate-400 mt-2">FECHA</label>
                        <input type="date" name="fecha" value="{{ old('fecha', date('Y-m-d')) }}" class="w-48 bg-slate-900 border border-slate-600 rounded p-2 text-center text-white focus:outline-none focus:border-blue-500 font-mono mb-4" required>
                    </div>

                    @foreach($categoriasAguaCruda as $nombre => $categoria)
                        @if($categoria['mediciones']->count() > 0)
                            <label class="text-xs font-bold mb-4 tracking-wide text-slate-400 uppercase text-center w-full block border-b border-slate-700 pb-2">{{ $nombre }}</label>
                            <div class="grid {{ $categoria['clases_grid'] }} gap-8 mb-8 text-center items-start">
                                @foreach($categoria['mediciones'] as $config)
                                    <div class="flex flex-col items-center">
                                        <label class="text-[10px] font-bold mb-2 tracking-wide text-slate-400">{{ mb_strtoupper($config->tipoMedicion->nombre) }}</label>
                                        @if($config->isText)
                                            <input type="text" name="medicion_{{ $config->id }}" value="{{ old('medicion_'.$config->id) }}" class="w-24 bg-slate-900 border border-slate-600 rounded p-2 text-center text-white focus:outline-none focus:border-blue-500 font-mono mb-4" placeholder="-">
                                        @else
                                            <input type="number" step="0.01" min="{{ $config->min ?? 0 }}" @if($config->max) max="{{ $config->max }}" @endif name="medicion_{{ $config->id }}" value="{{ old('medicion_'.$config->id) }}" class="w-24 bg-slate-900 border border-slate-600 rounded p-2 text-center text-white focus:outline-none focus:border-blue-500 font-mono mb-4" placeholder="0.00">
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    @endforeach

                    <div class="flex justify-center mb-4">
                        <button type="submit" class="bg-blue-600 hover:bg-blue-500 text-white font-bold py-2 px-12 rounded shadow-lg transition tracking-wide text-sm">
                            CONFIRMAR AGUA CRUDA
                        </button>
                    </div>
                </form>

                <!-- Tabla Registros Agua Cruda -->
                <div class="mt-8 bg-slate-900/50 rounded-xl border border-slate-700 p-6 shadow-2xl">
                    <h2 class="text-xl font-bold text-white text-center mb-6 tracking-wider uppercase">REGISTROS AGUA CRUDA</h2>
                    <div class="overflow-x-auto">
                        <table class="w-full text-center text-sm text-slate-300 border-collapse border border-slate-700">
                            <thead class="text-xs uppercase bg-slate-800 text-slate-400 tracking-wider">
                                <tr>
                                    <th scope="col" class="py-3 px-4 border border-slate-700">Fecha</th>
                                    @foreach($medicionesConfigAguaCruda->take(2) as $col)
                                        <th scope="col" class="py-3 px-4 border border-slate-700">{{ $col->tipoMedicion->nombre }}</th>
                                    @endforeach
                                    <th scope="col" class="py-3 px-4 border border-slate-700">Acciones</th>
                                </tr>
                            </thead>
                            <tbody class="font-medium">
                                @forelse($aguaCruda as $index => $registro)
                                    <tr class="hover:bg-slate-800/40 transition cruda-row" data-index="{{ $index }}" style="{{ $index >= 8 ? 'display:none;' : '' }}">
                                        <td class="py-4 px-4 font-mono text-slate-400 border border-slate-700">{{ $registro->fecha }}</td>
                                        @foreach($medicionesConfigAguaCruda->take(2) as $col)
                                            <td class="py-4 px-4 text-blue-400 font-mono border border-slate-700">{{ $registro->{'medicion_'.$col->id} ?? '-' }}</td>
                                        @endforeach
                                        <td class="py-4 px-4 border border-slate-700">
                                            <button type="button" onclick="toggleDetalle('detail-cruda-{{ $index }}')" class="bg-blue-600/85 hover:bg-blue-600 text-white py-1 px-3 rounded text-xs font-bold transition shadow-sm mx-1">Ver</button>
                                            <button type="button" 
                                                    onclick="confirmarEliminar('delete-cruda-form', '{{ route('laboratorio.destroyAguaCruda', $registro->id) }}', '¿Seguro que deseas borrar este registro?')"
                                                    class="bg-red-600/85 hover:bg-red-600 text-white py-1 px-3 rounded text-xs font-bold transition shadow-sm mx-1">
                                                Borrar
                                            </button>
                                        </td>
                                    </tr>
                                    <tr id="detail-cruda-{{ $index }}" class="bg-slate-800/60" style="display:none;">
                                        <td colspan="4" class="p-4 border border-slate-700 text-left">
                                            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                                                @foreach($medicionesConfigAguaCruda as $config)
                                                    @php $val = $registro->{'medicion_'.$config->id} ?? '-'; @endphp
                                                    <div class="text-xs">
                                                        <span class="text-slate-400 font-bold block">{{ mb_strtoupper($config->tipoMedicion->nombre) }}</span>
                                                        <span class="text-white">{{ $val }}</span>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="4" class="py-8 text-center text-slate-500 font-semibold border border-slate-700">No hay registros cargados todavía.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    @if(count($aguaCruda) > 8)
                    <div class="flex justify-center items-center mt-6 space-x-2 text-sm font-bold text-slate-300" id="cruda-pagination">
                        <button type="button" onclick="changePage('cruda', -1, {{ count($aguaCruda) }})" class="bg-slate-800 hover:bg-slate-700 px-3 py-1 rounded border border-slate-600 transition">&lt;</button>
                        <span id="cruda-page-indicator" class="px-4 text-blue-400 font-mono">Página 1 / {{ ceil(count($aguaCruda) / 8) }}</span>
                        <button type="button" onclick="changePage('cruda', 1, {{ count($aguaCruda) }})" class="bg-slate-800 hover:bg-slate-700 px-3 py-1 rounded border border-slate-600 transition">&gt;</button>
                    </div>
                    @endif
                </div>
            </div>
        </details>

        <!-- 3. PRODUCTO TERMINADO -->
        <details id="details-producto" class="bg-slate-900/40 rounded-xl border border-slate-700 mb-12 shadow-2xl group overflow-hidden">
            <summary class="list-none cursor-pointer bg-slate-800/80 p-6 flex justify-between items-center text-xl font-bold text-white tracking-wider hover:bg-slate-700/50 transition border-b border-slate-700">
                <span class="text-blue-400">3. PRODUCTO TERMINADO (Agua Potable Planta)</span>
                <span class="transform transition-transform group-open:rotate-180 text-slate-400">▼</span>
            </summary>
            <div class="p-8">
                <form action="{{ route('laboratorio.storeProductoTerminado') }}" method="POST" onsubmit="const btns = this.querySelectorAll('button[type=submit]'); btns.forEach(b => { b.disabled = true; b.innerHTML = 'GUARDANDO...'; b.classList.add('opacity-50', 'cursor-not-allowed'); });">
                    @csrf
                    <input type="hidden" name="_section" value="producto">
                    
                    <div class="flex flex-col items-center mb-8">
                        <label class="text-[10px] font-bold mb-2 tracking-wide text-slate-400 mt-2">FECHA</label>
                        <input type="date" name="fecha" value="{{ old('fecha', date('Y-m-d')) }}" class="w-48 bg-slate-900 border border-slate-600 rounded p-2 text-center text-white focus:outline-none focus:border-blue-500 font-mono mb-4" required>
                    </div>

                    @foreach($categoriasProducto as $nombre => $categoria)
                        @if($categoria['mediciones']->count() > 0)
                            <label class="text-xs font-bold mb-4 tracking-wide text-slate-400 uppercase text-center w-full block border-b border-slate-700 pb-2">{{ $nombre }}</label>
                            <div class="grid {{ $categoria['clases_grid'] }} gap-8 mb-8 text-center items-start">
                                @foreach($categoria['mediciones'] as $config)
                                    <div class="flex flex-col items-center">
                                        <label class="text-[10px] font-bold mb-2 tracking-wide text-slate-400">{{ mb_strtoupper($config->tipoMedicion->nombre) }}</label>
                                        @if($config->isText)
                                            <input type="text" name="medicion_{{ $config->id }}" value="{{ old('medicion_'.$config->id) }}" class="w-24 bg-slate-900 border border-slate-600 rounded p-2 text-center text-white focus:outline-none focus:border-blue-500 font-mono mb-4" placeholder="-">
                                        @else
                                            <input type="number" step="0.01" min="{{ $config->min ?? 0 }}" @if($config->max) max="{{ $config->max }}" @endif name="medicion_{{ $config->id }}" value="{{ old('medicion_'.$config->id) }}" class="w-24 bg-slate-900 border border-slate-600 rounded p-2 text-center text-white focus:outline-none focus:border-blue-500 font-mono mb-4" placeholder="0.00">
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    @endforeach

                    <div class="flex justify-center mb-4">
                        <button type="submit" class="bg-blue-600 hover:bg-blue-500 text-white font-bold py-2 px-12 rounded shadow-lg transition tracking-wide text-sm">
                            CONFIRMAR PRODUCTO TERMINADO
                        </button>
                    </div>
                </form>

                <!-- Tabla Registros Producto -->
                <div class="mt-8 bg-slate-900/50 rounded-xl border border-slate-700 p-6 shadow-2xl">
                    <h2 class="text-xl font-bold text-white text-center mb-6 tracking-wider uppercase">REGISTROS PRODUCTO TERMINADO</h2>
                    <div class="overflow-x-auto">
                        <table class="w-full text-center text-sm text-slate-300 border-collapse border border-slate-700">
                            <thead class="text-xs uppercase bg-slate-800 text-slate-400 tracking-wider">
                                <tr>
                                    <th scope="col" class="py-3 px-4 border border-slate-700">Fecha</th>
                                    @foreach($medicionesConfigProducto->take(2) as $col)
                                        <th scope="col" class="py-3 px-4 border border-slate-700">{{ $col->tipoMedicion->nombre }}</th>
                                    @endforeach
                                    <th scope="col" class="py-3 px-4 border border-slate-700">Acciones</th>
                                </tr>
                            </thead>
                            <tbody class="font-medium">
                                @forelse($productoTerminado as $index => $registro)
                                    <tr class="hover:bg-slate-800/40 transition producto-row" data-index="{{ $index }}" style="{{ $index >= 8 ? 'display:none;' : '' }}">
                                        <td class="py-4 px-4 font-mono text-slate-400 border border-slate-700">{{ $registro->fecha }}</td>
                                        @foreach($medicionesConfigProducto->take(2) as $col)
                                            <td class="py-4 px-4 text-blue-400 font-mono border border-slate-700">{{ $registro->{'medicion_'.$col->id} ?? '-' }}</td>
                                        @endforeach
                                        <td class="py-4 px-4 border border-slate-700">
                                            <button type="button" onclick="toggleDetalle('detail-producto-{{ $index }}')" class="bg-blue-600/85 hover:bg-blue-600 text-white py-1 px-3 rounded text-xs font-bold transition shadow-sm mx-1">Ver</button>
                                            <button type="button" 
                                                    onclick="confirmarEliminar('delete-producto-form', '{{ route('laboratorio.destroyProductoTerminado', $registro->id) }}', '¿Seguro que deseas borrar este registro?')"
                                                    class="bg-red-600/85 hover:bg-red-600 text-white py-1 px-3 rounded text-xs font-bold transition shadow-sm mx-1">
                                                Borrar
                                            </button>
                                        </td>
                                    </tr>
                                    <tr id="detail-producto-{{ $index }}" class="bg-slate-800/60" style="display:none;">
                                        <td colspan="4" class="p-4 border border-slate-700 text-left">
                                            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                                                @foreach($medicionesConfigProducto as $config)
                                                    @php $val = $registro->{'medicion_'.$config->id} ?? '-'; @endphp
                                                    <div class="text-xs">
                                                        <span class="text-slate-400 font-bold block">{{ mb_strtoupper($config->tipoMedicion->nombre) }}</span>
                                                        <span class="text-white">{{ $val }}</span>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="4" class="py-8 text-center text-slate-500 font-semibold border border-slate-700">No hay registros cargados todavía.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    @if(count($productoTerminado) > 8)
                    <div class="flex justify-center items-center mt-6 space-x-2 text-sm font-bold text-slate-300" id="producto-pagination">
                        <button type="button" onclick="changePage('producto', -1, {{ count($productoTerminado) }})" class="bg-slate-800 hover:bg-slate-700 px-3 py-1 rounded border border-slate-600 transition">&lt;</button>
                        <span id="producto-page-indicator" class="px-4 text-blue-400 font-mono">Página 1 / {{ ceil(count($productoTerminado) / 8) }}</span>
                        <button type="button" onclick="changePage('producto', 1, {{ count($productoTerminado) }})" class="bg-slate-800 hover:bg-slate-700 px-3 py-1 rounded border border-slate-600 transition">&gt;</button>
                    </div>
                    @endif
                </div>
            </div>
        </details>

        <!-- 4. POZOS -->
        <details id="details-pozos" class="bg-slate-900/40 rounded-xl border border-slate-700 mb-12 shadow-2xl group overflow-hidden">
            <summary class="list-none cursor-pointer bg-slate-800/80 p-6 flex justify-between items-center text-xl font-bold text-white tracking-wider hover:bg-slate-700/50 transition border-b border-slate-700">
                <span class="text-blue-400">4. POZOS DE EXTRACCIÓN</span>
                <span class="transform transition-transform group-open:rotate-180 text-slate-400">▼</span>
            </summary>
            <div class="p-8">
                <form action="{{ route('laboratorio.storePozo') }}" method="POST" onsubmit="const btns = this.querySelectorAll('button[type=submit]'); btns.forEach(b => { b.disabled = true; b.innerHTML = 'GUARDANDO...'; b.classList.add('opacity-50', 'cursor-not-allowed'); });">
                    @csrf
                    <input type="hidden" name="_section" value="pozos">
                    
                    <div class="flex flex-col items-center mb-8">
                        <label class="text-[10px] font-bold mb-2 tracking-wide text-slate-400">SELECCIÓN DE POZO</label>
                        <select name="pozo_numero" class="w-48 bg-slate-900 border border-slate-600 rounded p-2 text-center focus:outline-none focus:border-blue-500 text-[10px] font-bold tracking-wide text-slate-400 uppercase mb-4" required onchange="togglePozoFields(this.value)">
                            <option value="">Elegir Pozo</option>
                            @foreach($tiposPozos as $pozo)
                                <option value="{{ $pozo->id }}" {{ old('pozo_numero') == $pozo->id ? 'selected' : '' }}>{{ $pozo->nombre }}</option>
                            @endforeach
                        </select>
                        
                        <label class="text-[10px] font-bold mb-2 tracking-wide text-slate-400 mt-2">FECHA</label>
                        <input type="date" name="fecha" value="{{ old('fecha', date('Y-m-d')) }}" class="w-48 bg-slate-900 border border-slate-600 rounded p-2 text-center text-white focus:outline-none focus:border-blue-500 font-mono mb-4" required>
                    </div>

                    <div class="grid grid-cols-2 gap-8 mb-8 text-center items-start justify-center max-w-lg mx-auto">
                        @foreach($medicionesConfigPozos as $config)
                            <div class="flex flex-col items-center f-pozo-{{ $config->pozo_id }} {{ old('pozo_numero') == $config->pozo_id ? '' : 'hidden' }}">
                                <label class="text-[10px] font-bold mb-2 tracking-wide text-slate-400">{{ mb_strtoupper($config->tipoMedicion->nombre) }}</label>
                                @if($config->isText)
                                    <input type="text" name="medicion_{{ $config->id }}" value="{{ old('medicion_'.$config->id) }}" class="w-24 bg-slate-900 border border-slate-600 rounded p-2 text-center text-white focus:outline-none focus:border-blue-500 font-mono mb-4" placeholder="-">
                                @else
                                    <input type="number" step="0.01" min="{{ $config->min ?? 0 }}" @if($config->max) max="{{ $config->max }}" @endif name="medicion_{{ $config->id }}" value="{{ old('medicion_'.$config->id) }}" class="w-24 bg-slate-900 border border-slate-600 rounded p-2 text-center text-white focus:outline-none focus:border-blue-500 font-mono mb-4" placeholder="0.00">
                                @endif
                            </div>
                        @endforeach
                    </div>

                    <div class="flex justify-center mb-4">
                        <button type="submit" class="bg-blue-600 hover:bg-blue-500 text-white font-bold py-2 px-12 rounded shadow-lg transition tracking-wide text-sm">
                            CONFIRMAR POZO
                        </button>
                    </div>
                </form>

                <!-- Tabla Registros Pozos -->
                <div class="mt-8 bg-slate-900/50 rounded-xl border border-slate-700 p-6 shadow-2xl">
                    <h2 class="text-xl font-bold text-white text-center mb-6 tracking-wider uppercase">REGISTROS POZOS</h2>
                    <div class="overflow-x-auto">
                        <table class="w-full text-center text-sm text-slate-300 border-collapse border border-slate-700">
                            <thead class="text-xs uppercase bg-slate-800 text-slate-400 tracking-wider">
                                <tr>
                                    <th scope="col" class="py-3 px-4 border border-slate-700">Fecha</th>
                                    <th scope="col" class="py-3 px-4 border border-slate-700">Pozo Número</th>
                                    <th scope="col" class="py-3 px-4 border border-slate-700">Acciones</th>
                                </tr>
                            </thead>
                            <tbody class="font-medium">
                                @forelse($pozos as $index => $registro)
                                    <tr class="hover:bg-slate-800/40 transition pozo-row" data-index="{{ $index }}" style="{{ $index >= 8 ? 'display:none;' : '' }}">
                                        <td class="py-4 px-4 font-mono text-slate-400 border border-slate-700">{{ $registro->fecha }}</td>
                                        <td class="py-4 px-4 text-white font-bold border border-slate-700">Pozo {{ $registro->pozo_numero }}</td>
                                        <td class="py-4 px-4 border border-slate-700">
                                            <button type="button" onclick="toggleDetalle('detail-pozo-{{ $index }}')" class="bg-blue-600/85 hover:bg-blue-600 text-white py-1 px-3 rounded text-xs font-bold transition shadow-sm mx-1">Ver</button>
                                            <button type="button" 
                                                    onclick="confirmarEliminar('delete-pozo-form', '{{ route('laboratorio.destroyPozo', $registro->id) }}', '¿Seguro que deseas borrar este registro?')"
                                                    class="bg-red-600/85 hover:bg-red-600 text-white py-1 px-3 rounded text-xs font-bold transition shadow-sm mx-1">
                                                Borrar
                                            </button>
                                        </td>
                                    </tr>
                                    <tr id="detail-pozo-{{ $index }}" class="bg-slate-800/60" style="display:none;">
                                        <td colspan="3" class="p-4 border border-slate-700 text-left">
                                            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                                                @foreach($medicionesConfigPozos as $config)
                                                    @if($config->pozo_id == $registro->pozo_id)
                                                        @php $val = $registro->{'medicion_'.$config->id} ?? '-'; @endphp
                                                        <div class="text-xs">
                                                            <span class="text-slate-400 font-bold block">{{ mb_strtoupper($config->tipoMedicion->nombre) }}</span>
                                                            <span class="text-white">{{ $val }}</span>
                                                        </div>
                                                    @endif
                                                @endforeach
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="3" class="py-8 text-center text-slate-500 font-semibold border border-slate-700">No hay registros cargados todavía.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    @if(count($pozos) > 8)
                    <div class="flex justify-center items-center mt-6 space-x-2 text-sm font-bold text-slate-300" id="pozo-pagination">
                        <button type="button" onclick="changePage('pozo', -1, {{ count($pozos) }})" class="bg-slate-800 hover:bg-slate-700 px-3 py-1 rounded border border-slate-600 transition">&lt;</button>
                        <span id="pozo-page-indicator" class="px-4 text-blue-400 font-mono">Página 1 / {{ ceil(count($pozos) / 8) }}</span>
                        <button type="button" onclick="changePage('pozo', 1, {{ count($pozos) }})" class="bg-slate-800 hover:bg-slate-700 px-3 py-1 rounded border border-slate-600 transition">&gt;</button>
                    </div>
                    @endif
                </div>
            </div>
        </details>

        <!-- 5. NOVEDADES Y COMENTARIOS DEL TURNO -->
        <details id="novedades-details" class="bg-slate-900/40 rounded-xl border border-slate-700 mb-12 shadow-2xl group overflow-hidden">
            <summary class="list-none cursor-pointer bg-slate-800/80 p-6 flex justify-between items-center text-xl font-bold text-white tracking-wider hover:bg-slate-700/50 transition border-b border-slate-700">
                <span class="text-blue-400">5. NOVEDADES Y COMENTARIOS DEL TURNO</span>
                <span class="transform transition-transform group-open:rotate-180 text-slate-400">▼</span>
            </summary>
            <div class="p-8">
                <form action="{{ route('laboratorio.storeNovedad') }}" method="POST" class="mb-12" onsubmit="const btns = this.querySelectorAll('button[type=submit]'); btns.forEach(b => { b.disabled = true; b.innerHTML = 'GUARDANDO...'; b.classList.add('opacity-50', 'cursor-not-allowed'); });">
                    @csrf
                    <input type="hidden" name="_section" value="novedades">
                    <label class="block text-sm font-bold text-slate-300 mb-3 tracking-wide">REGISTRAR NUEVA NOVEDAD (Máx. 1000 caracteres)</label>
                    <textarea name="mensaje" rows="3" class="w-full bg-slate-900 border border-slate-600 rounded p-4 text-white focus:outline-none focus:border-blue-500 mb-4 resize-none" placeholder="Escribe aquí cualquier comentario importante o novedad del turno para que el próximo personal esté enterado..." required></textarea>
                    
                    <div class="flex justify-center mb-12">
                        <button type="submit" class="bg-blue-600 hover:bg-blue-500 text-white font-bold py-2 px-12 rounded shadow-lg transition tracking-wide text-sm">
                            GUARDAR NOVEDAD
                        </button>
                    </div>
                </form>

                <!-- Listado y boletín -->
                <div class="bg-slate-900/50 rounded border border-slate-700 p-6">
                    <div class="flex justify-between items-center mb-6">
                        <h3 class="text-lg font-bold text-blue-400 tracking-widest text-center flex-grow">ÚLTIMAS NOVEDADES</h3>
                        @if($novedadesRecientes > 0)
                        <form action="{{ route('laboratorio.marcarLeidas') }}" method="POST" onsubmit="const btn = this.querySelector('button'); btn.disabled = true; btn.innerHTML = 'MARCANDO...'; btn.classList.add('opacity-50', 'cursor-not-allowed');">
                            @csrf
                            <button type="submit" class="bg-blue-600/80 hover:bg-blue-500 text-white font-bold py-1 px-4 rounded shadow-sm transition text-xs tracking-wide flex items-center gap-2 border border-blue-500">
                                <i class="fa-solid fa-check-double"></i> MARCAR LEÍDAS
                            </button>
                        </form>
                        @endif
                    </div>
                    
                    <div class="space-y-4 max-h-96 overflow-y-auto pr-2">
                        @forelse($ultimasNovedades as $novedad)
                            <div class="bg-slate-800 p-4 rounded-lg border border-slate-700">
                                <div class="flex justify-between items-center mb-2">
                                    <span class="font-bold text-white">{{ $novedad->user->name ?? 'N/A' }}</span>
                                    <div class="flex items-center gap-3">
                                        <span class="text-xs text-slate-400 font-mono">{{ $novedad->created_at->format('d/m/Y H:i') }}</span>
                                        @if(auth()->id() == $novedad->user_id && $novedad->created_at->gt(now()->subHours(2)))
                                            <button type="button" 
                                                    onclick="confirmarEliminar('delete-novedad-form', '{{ route('laboratorio.destroyNovedad', $novedad->id) }}', '¿Seguro que deseas borrar esta novedad?')"
                                                    class="bg-red-600/85 hover:bg-red-600 text-white py-1 px-3 rounded text-xs font-bold transition shadow-sm flex items-center gap-1">
                                                <i class="fa-solid fa-trash text-[10px]"></i> Borrar
                                            </button>
                                        @endif
                                    </div>
                                </div>
                                <p class="text-slate-300 text-sm whitespace-pre-wrap">{{ $novedad->mensaje }}</p>
                            </div>
                        @empty
                            <p class="text-center text-slate-500 font-semibold py-4">No hay novedades registradas.</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </details>

    </div>

    <!-- Botón Volver Arriba -->
    <button id="btn-scroll-top" title="Volver al inicio" onclick="window.scrollTo({top:0,behavior:'smooth'})">
        <i class="fa-solid fa-chevron-up"></i>
    </button>

    <!-- Formularios ocultos para eliminar -->
    <form id="delete-insumo-form" method="POST" style="display: none;">
        @csrf @method('DELETE')
    </form>
    <form id="delete-cruda-form" method="POST" style="display: none;">
        @csrf @method('DELETE')
    </form>
    <form id="delete-producto-form" method="POST" style="display: none;">
        @csrf @method('DELETE')
    </form>
    <form id="delete-pozo-form" method="POST" style="display: none;">
        @csrf @method('DELETE')
    </form>
    <form id="delete-novedad-form" method="POST" style="display: none;">
        @csrf @method('DELETE')
    </form>

    <script>
        const SwalCustom = Swal.mixin({
            background: '#1e293b', 
            color: '#f8fafc', 
            confirmButtonColor: '#2563eb', 
            denyButtonColor: '#475569', 
            cancelButtonColor: '#dc2626', 
            customClass: {
                popup: 'border border-slate-700 rounded-2xl shadow-2xl',
                title: 'text-[18px] text-white font-bold tracking-wide',
                htmlContainer: 'text-slate-300 font-medium text-sm',
                confirmButton: 'px-6 py-2.5 rounded-lg font-semibold text-sm transition',
                cancelButton: 'px-6 py-2.5 rounded-lg font-semibold text-sm transition',
                denyButton: 'px-6 py-2.5 rounded-lg font-semibold text-sm transition'
            },
            buttonsStyling: true
        });

        function confirmarEliminar(formId, actionUrl, mensaje = '¿Seguro que deseas borrar este registro?') {
            SwalCustom.fire({
                title: '¿Confirmar eliminación?',
                text: mensaje,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Sí, borrar',
                cancelButtonText: 'Cancelar',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    const form = document.getElementById(formId);
                    form.action = actionUrl;
                    form.submit();
                }
            });
        }

        function toggleInsumoFields(insumoId) {
            document.querySelectorAll('[class*="f-insumo-"]').forEach(el => el.classList.add('hidden'));
            if (insumoId) {
                document.querySelectorAll('.f-insumo-' + insumoId).forEach(el => el.classList.remove('hidden'));
                document.getElementById('campos-insumo').classList.remove('hidden');
            } else {
                document.getElementById('campos-insumo').classList.add('hidden');
            }
        }

        function togglePozoFields(pozoId) {
            document.querySelectorAll('[class*="f-pozo-"]').forEach(el => el.classList.add('hidden'));
            if (pozoId) {
                document.querySelectorAll('.f-pozo-' + pozoId).forEach(el => el.classList.remove('hidden'));
            }
        }

        // Si hay error en un select, forzamos mostrar los campos
        window.onload = function() {
            const tipo = document.querySelector('select[name="tipo_insumo"]').value;
            if(tipo) toggleInsumoFields(tipo);
            const pozo = document.querySelector('select[name="pozo_numero"]').value;
            if(pozo) togglePozoFields(pozo);
        };

        // Restaurar estado de bloques
        document.addEventListener('DOMContentLoaded', () => {
            const detailsElements = document.querySelectorAll('details');
            detailsElements.forEach(detail => {
                if (!detail.id) return;
                
                if (detail.open) {
                    sessionStorage.setItem('lab_state_' + detail.id, 'open');
                } else {
                    const state = sessionStorage.getItem('lab_state_' + detail.id);
                    if (state === 'open') { detail.open = true; }
                }
                
                detail.addEventListener('toggle', () => {
                    if (detail.open) {
                        sessionStorage.setItem('lab_state_' + detail.id, 'open');
                    } else {
                        sessionStorage.removeItem('lab_state_' + detail.id);
                    }
                });
            });

            // Alertas PHP a SweetAlert
            @if($errors->any())
                @php
                    $errorList = '<ul class="text-left list-disc list-inside space-y-1 font-mono text-xs text-red-300">';
                    foreach($errors->all() as $error) {
                        $errorList .= '<li>' . e($error) . '</li>';
                    }
                    $errorList .= '</ul>';
                    // Detectar qué sección generó el error via campo hidden _section
                    $sectionMap = [
                        'insumos'   => 'details-insumos',
                        'cruda'     => 'details-cruda',
                        'producto'  => 'details-producto',
                        'pozos'     => 'details-pozos',
                        'novedades' => 'novedades-details',
                    ];
                    $errorSection = $sectionMap[old('_section')] ?? null;
                @endphp
                SwalCustom.fire({
                    title: 'Faltan completar o corregir datos',
                    html: '{!! $errorList !!}',
                    icon: 'error',
                    confirmButtonText: 'Aceptar'
                }).then(function() {
                    @if($errorSection)
                        var seccion = document.getElementById('{{ $errorSection }}');
                        if (seccion) {
                            seccion.open = true;
                            setTimeout(function() {
                                seccion.scrollIntoView({ behavior: 'smooth', block: 'start' });
                            }, 100);
                        }
                    @endif
                });
            @elseif(session('success'))
                SwalCustom.fire({
                    title: '¡Guardado!',
                    text: "{{ session('success') }}",
                    icon: 'success',
                    confirmButtonText: 'Aceptar'
                });
            @elseif(session('deleted'))
                SwalCustom.fire({
                    title: '¡Eliminado!',
                    text: "{{ session('deleted') }}",
                    icon: 'success',
                    confirmButtonText: 'Aceptar'
                });
            @elseif(session('error'))
                SwalCustom.fire({
                    title: '¡Error!',
                    text: "{{ session('error') }}",
                    icon: 'error',
                    confirmButtonText: 'Aceptar'
                });
            @endif
        });

        // Paginación
        const pages = {
            'insumo': 1,
            'cruda': 1,
            'producto': 1,
            'pozo': 1
        };
        const itemsPerPage = 8;

        function toggleDetalle(id) {
            const el = document.getElementById(id);
            if(el) {
                if(el.style.display === 'none') {
                    el.style.display = 'table-row';
                } else {
                    el.style.display = 'none';
                }
            }
        }

        function changePage(tableId, direction, totalItems) {
            const totalPages = Math.ceil(totalItems / itemsPerPage);
            pages[tableId] += direction;
            
            if (pages[tableId] < 1) pages[tableId] = 1;
            if (pages[tableId] > totalPages) pages[tableId] = totalPages;

            const indicator = document.getElementById(tableId + '-page-indicator');
            if(indicator) indicator.innerText = `Página ${pages[tableId]} / ${totalPages}`;

            const rows = document.querySelectorAll(`.${tableId}-row`);
            rows.forEach(row => {
                const index = parseInt(row.getAttribute('data-index'));
                const start = (pages[tableId] - 1) * itemsPerPage;
                const end = start + itemsPerPage - 1;
                
                if (index >= start && index <= end) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
                
                // Hide any detail row when paginating
                const detail = document.getElementById(`detail-${tableId}-${index}`);
                if(detail) detail.style.display = 'none';
            });
        }

        // Botón scroll-to-top: mostrar cuando se baja más de 300px
        (function() {
            var btn = document.getElementById('btn-scroll-top');
            window.addEventListener('scroll', function() {
                if (window.scrollY > 300) {
                    btn.classList.add('visible');
                } else {
                    btn.classList.remove('visible');
                }
            }, { passive: true });
        })();
    </script>
</body>
</html>
