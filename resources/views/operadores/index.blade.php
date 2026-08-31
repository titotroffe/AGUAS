<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Formulario de Operadores</title>
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <style>
        /* Ocultar las flechas de los input type number para centrar bien los placeholders */
        input[type=number]::-webkit-inner-spin-button, 
        input[type=number]::-webkit-outer-spin-button { 
            -webkit-appearance: none; 
            margin: 0; 
        }
        input[type=number] {
            -moz-appearance: textfield;
        }
        
        input[type="datetime-local"]::-webkit-calendar-picker-indicator {
            cursor: pointer;
        }
        /* Para navegadores que soporten color-scheme */
        input[type="datetime-local"] {
            color-scheme: dark;
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
        
       <div class="flex flex-col md:flex-row md:items-center md:justify-center mb-12 relative gap-6">
            <div class="md:absolute md:left-0 md:top-1/2 md:-translate-y-1/2 flex justify-center">
                <a href="/menu" class="bg-slate-700 hover:bg-slate-600 text-white py-2 px-6 rounded border border-slate-500 transition text-sm font-semibold">
                     ← VOLVER AL MENÚ
                </a>
            </div>
            <h1 class="text-xl md:text-2xl font-bold text-white tracking-wider text-center m-0 w-full">FORMULARIO DE OPERADOR DE TURNO</h1>
        </div>

        @if($novedadesRecientes > 0)
            <div class="bg-blue-900/50 border border-blue-500 text-blue-200 px-6 py-4 rounded-xl mb-8 text-center shadow-lg flex items-center justify-center gap-3 cursor-pointer hover:bg-blue-800/50 transition" onclick="document.getElementById('novedades-details').open = true; document.getElementById('novedades-details').scrollIntoView({behavior: 'smooth'})">
                <i class="fa-solid fa-bell text-blue-400 text-xl animate-pulse"></i>
                <span class="font-bold tracking-wide">¡Hay {{ $novedadesRecientes }} novedad(es) del turno anterior sin leer! Haz clic aquí para ir al Boletín.</span>
            </div>
        @endif

        @if(session('success') && !session('success_presiones') && !session('success_lavados') && !session('success_quimicos') && !session('success_novedades'))
            <div class="bg-emerald-900/50 border border-emerald-500 text-emerald-200 px-4 py-3 rounded mb-6 text-center text-sm font-semibold shadow-md">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error') && !session('error_presiones') && !session('error_lavados') && !session('error_quimicos') && !session('error_novedades'))
            <div class="bg-red-900/50 border border-red-500 text-red-200 px-4 py-3 rounded mb-6 text-center text-sm font-semibold shadow-md">
                {{ session('error') }}
            </div>
        @endif

        @if($errors->any() && !$errors->hasAny(['presion_tanque', 'presion_planta', 'presion_falcon', 'nivel_cisterna', 'norte_1', 'norte_2', 'norte_3', 'sur_1', 'sur_2', 'sur_3', 'inicio_lavado', 'fin_lavado', 'filtros', 'quimico', 'tanque_principal', 'tanque_auxiliar', 'mensaje']))
            <div class="bg-red-900/50 border border-red-500 text-red-200 px-4 py-3 rounded mb-6 text-center text-sm font-semibold shadow-md">
                <ul class="list-disc list-inside">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @php
            $erroresPresiones = $errors->hasAny(['presion_tanque', 'presion_planta', 'presion_falcon', 'nivel_cisterna']) || session('success_presiones') || session('error_presiones');
            $erroresLavados   = $errors->hasAny(['norte_1', 'norte_2', 'norte_3', 'sur_1', 'sur_2', 'sur_3', 'inicio_lavado', 'fin_lavado', 'filtros']) || session('success_lavados') || session('error_lavados');
            $erroresQuimicos  = $errors->hasAny(['quimico', 'tanque_principal', 'tanque_auxiliar']) || session('success_quimicos') || session('error_quimicos');
            $erroresNovedades = $errors->hasAny(['mensaje']) || session('success_novedades') || session('error_novedades');
        @endphp 

        {{-- ══ PANEL BOMBAS Y POZOS ══ --}}
        <x-panel-bombas :readonly="false" :estados="$estadosBombas" />

        <details id="details-presiones" @if($erroresPresiones) open @endif class="bg-slate-900/40 rounded-xl border border-slate-700 mb-12 shadow-2xl group overflow-hidden">
            <summary class="list-none cursor-pointer bg-slate-800/80 p-6 flex justify-between items-center text-xl font-bold text-white tracking-wider hover:bg-slate-700/50 transition border-b border-slate-700">
                <span class="text-blue-400">1. PRESIONES Y NIVELES DE CISTERNA</span>
                <span class="transform transition-transform group-open:rotate-180 text-slate-400">▼</span>
            </summary>
            <div class="p-8">
                @if(session('success_presiones'))
                    <div class="bg-emerald-900/50 border border-emerald-500 text-emerald-200 px-4 py-3 rounded-xl mb-6 text-center text-sm font-semibold shadow-md">
                        {{ session('success_presiones') }}
                    </div>
                @endif
                @if(session('error_presiones') || $errors->hasAny(['presion_tanque', 'presion_planta', 'presion_falcon', 'nivel_cisterna']))
                    <div class="bg-red-900/50 border border-red-500 text-red-200 px-4 py-3 rounded-xl mb-6 text-sm font-semibold shadow-md">
                        <ul class="list-disc list-inside text-left">
                            @if(session('error_presiones'))
                                <li>{{ session('error_presiones') }}</li>
                            @endif
                            @foreach(['presion_tanque', 'presion_planta', 'presion_falcon', 'nivel_cisterna'] as $field)
                                @error($field)
                                    <li>{{ $message }}</li>
                                @enderror
                            @endforeach
                        </ul>
                    </div>
                @endif
            
            <form action="{{ route('operadores.storePresion') }}" method="POST" class="mb-16" onsubmit="const btns = this.querySelectorAll('button[type=submit]'); btns.forEach(b => { b.disabled = true; b.innerHTML = 'GUARDANDO...'; b.classList.add('opacity-50', 'cursor-not-allowed'); });">
                @csrf
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8 mb-8 text-center">
    <div class="flex flex-col items-center">
        <label class="text-xs font-bold mb-2 tracking-wide text-slate-400">BAJADA DE TANQUE</label>
        <input type="number" name="presion_tanque" step="0.01" value="{{ old('presion_tanque') }}" max="26" min="0" class="w-32 bg-slate-900 border border-slate-600 rounded p-2 text-center text-white focus:outline-none focus:border-blue-500 font-mono" placeholder="0.00">
        @error('presion_tanque')
            <span class="text-red-400 text-xs mt-1">{{ $message }}</span>
        @enderror
    </div>
    
    <div class="flex flex-col items-center">
        <label class="text-xs font-bold mb-2 tracking-wide text-slate-400">PLANTA</label>
        <input type="number" name="presion_planta" step="0.01" value="{{ old('presion_planta') }}" max="22" min="0" class="w-32 bg-slate-900 border border-slate-600 rounded p-2 text-center text-white focus:outline-none focus:border-blue-500 font-mono" placeholder="0.00">
        @error('presion_planta')
            <span class="text-red-400 text-xs mt-1">{{ $message }}</span>
        @enderror
    </div>  

    <div class="flex flex-col items-center">
        <label class="text-xs font-bold mb-2 tracking-wide text-slate-400">TANQUE DE FALCON</label>
        <input type="number" name="presion_falcon" step="0.01" value="{{ old('presion_falcon') }}" max="12" min="0" class="w-32 bg-slate-900 border border-slate-600 rounded p-2 text-center text-white focus:outline-none focus:border-blue-500 font-mono" placeholder="0.00">
        @error('presion_falcon')
            <span class="text-red-400 text-xs mt-1">{{ $message }}</span>
        @enderror
    </div>

    <div class="flex flex-col items-center">
        <label class="text-xs font-bold mb-2 tracking-wide text-slate-400">NIVEL DE CISTERNA (%)</label>
        <input type="number" name="nivel_cisterna" step="0.01" value="{{ old('nivel_cisterna') }}" max="100" min="0" class="w-32 bg-slate-900 border border-slate-600 rounded p-2 text-center text-white focus:outline-none focus:border-blue-500 font-mono" placeholder="0.00%">
        @error('nivel_cisterna')
            <span class="text-red-400 text-xs mt-1">{{ $message }}</span>
        @enderror
    </div>
</div>

            <div class="flex justify-center mb-12">
                <button type="submit" class="bg-blue-600 hover:bg-blue-500 text-white font-bold py-2 px-12 rounded shadow-lg transition tracking-wide text-sm">
                    CONFIRMAR PRESIONES
                </button>
            </div>
        </form>

        <div class="mt-8 mb-16 bg-slate-900/50 rounded-xl border border-slate-700 p-6 shadow-2xl">
            <h2 class="text-xl font-bold text-white text-center mb-6 tracking-wider uppercase">
                REGISTROS DE PRESIONES Y NIVEL DE CISTERNA
            </h2>
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-slate-300 border-collapse block md:table border-none md:border md:border-slate-700">
                    <thead class="text-xs uppercase bg-slate-800 text-slate-400 tracking-wider hidden md:table-header-group text-center">
                        <tr>
                            <th scope="col" class="py-3 px-4 border border-slate-700">Fecha y Hora</th>
                            <th scope="col" class="py-3 px-4 border border-slate-700">Operador</th>
                            <th scope="col" class="py-3 px-4 border border-slate-700">Bajada de Tanque</th>
                            <th scope="col" class="py-3 px-4 border border-slate-700">Planta</th>
                            <th scope="col" class="py-3 px-4 border border-slate-700">Tanque de Falcón</th>
                            <th scope="col" class="py-3 px-4 border border-slate-700">Nivel Cisterna</th>
                            <th scope="col" class="py-3 px-4 border border-slate-700">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="font-medium" id="presiones-tbody">
                        @forelse($ultimosRegistros as $index => $registro)
                            <tr class="hover:bg-slate-800/40 transition presion-row block md:table-row border border-slate-700 md:border-none mb-4 md:mb-0 rounded-lg md:rounded-none overflow-hidden bg-slate-900/60 md:bg-transparent" data-index="{{ $index }}" style="{{ $index >= 8 ? 'display:none;' : '' }}">
                                <td data-label="FECHA Y HORA" class="py-3 px-4 font-mono text-slate-400 border-b md:border border-slate-700 flex md:table-cell justify-between items-center text-right md:text-center before:content-[attr(data-label)] before:font-bold before:text-slate-500 before:text-xs md:before:content-none">
                                    <span>{{ $registro->created_at->format('d/m/Y H:i') }}</span>
                                </td>
                                <td data-label="OPERADOR" class="py-3 px-4 text-white font-semibold border-b md:border border-slate-700 flex md:table-cell justify-between items-center text-right md:text-center before:content-[attr(data-label)] before:font-bold before:text-slate-500 before:text-xs md:before:content-none">
                                    <span>{{ $registro->user->name ?? 'N/A' }}</span>
                                </td>
                                <td data-label="BAJADA DE TANQUE" class="py-3 px-4 font-mono text-blue-400 border-b md:border border-slate-700 flex md:table-cell justify-between items-center text-right md:text-center before:content-[attr(data-label)] before:font-bold before:text-slate-500 before:text-xs md:before:content-none">
                                    <span>{{ $registro->presion_tanque !== null ? number_format($registro->presion_tanque, 2) . ' MCA' : '-' }}</span>
                                </td>
                                <td data-label="PLANTA" class="py-3 px-4 font-mono text-blue-400 border-b md:border border-slate-700 flex md:table-cell justify-between items-center text-right md:text-center before:content-[attr(data-label)] before:font-bold before:text-slate-500 before:text-xs md:before:content-none">
                                    <span>{{ $registro->presion_planta !== null ? number_format($registro->presion_planta, 2) . ' MCA' : '-' }}</span>
                                </td>
                                <td data-label="TANQUE DE FALCÓN" class="py-3 px-4 font-mono text-blue-400 border-b md:border border-slate-700 flex md:table-cell justify-between items-center text-right md:text-center before:content-[attr(data-label)] before:font-bold before:text-slate-500 before:text-xs md:before:content-none">
                                    <span>{{ $registro->presion_falcon !== null ? number_format($registro->presion_falcon, 2) . ' MCA' : '-' }}</span>
                                </td>
                                <td data-label="NIVEL CISTERNA" class="py-3 px-4 font-mono text-emerald-400 font-bold border-b md:border border-slate-700 flex md:table-cell justify-between items-center text-right md:text-center before:content-[attr(data-label)] before:font-bold before:text-slate-500 before:text-xs md:before:content-none">
                                    <span>{{ $registro->nivel_cisterna !== null ? number_format($registro->nivel_cisterna, 2) . '% (' . number_format(($registro->nivel_cisterna / 100) * 7, 2) . ' m)' : '-' }}</span>
                                </td>
                                <td data-label="ACCIONES" class="py-3 px-4 md:border border-slate-700 flex md:table-cell justify-between items-center text-right md:text-center before:content-[attr(data-label)] before:font-bold before:text-slate-500 before:text-xs md:before:content-none">
                                    @if(auth()->id() == $registro->user_id && $registro->created_at->gt(now()->subHours(2)))
                                        <button type="button" 
                                                onclick="confirmarEliminar('delete-pressure-form', '{{ route('operadores.destroy', $registro->id) }}', '¿Seguro que deseas borrar este registro de presión?')"
                                                class="bg-red-600/85 hover:bg-red-600 text-white py-1 px-3 rounded text-xs font-bold transition shadow-sm inline-flex items-center gap-1">
                                            <i class="fa-solid fa-trash text-[10px]"></i> Borrar
                                        </button>
                                    @else
                                        <span class="text-slate-500 text-xs">-</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr class="block md:table-row">
                                <td colspan="7" class="py-8 text-center text-slate-500 font-semibold border border-slate-700 block md:table-cell">
                                    No hay registros de presiones cargados todavía.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            @if(count($ultimosRegistros) > 8)
            <div class="flex justify-center items-center mt-6 space-x-2 text-sm font-bold text-slate-300" id="presiones-pagination">
                <button type="button" onclick="changePage(-1)" class="bg-slate-800 hover:bg-slate-700 px-3 py-1 rounded border border-slate-600 transition">&lt;</button>
                <span id="page-indicator" class="px-4 text-blue-400 font-mono">Página 1 / {{ ceil(count($ultimosRegistros) / 8) }}</span>
                <button type="button" onclick="changePage(1)" class="bg-slate-800 hover:bg-slate-700 px-3 py-1 rounded border border-slate-600 transition">&gt;</button>
            </div>
            @endif
        </div>
        </div>
        </details>

        <details id="details-lavados" @if($erroresLavados) open @endif class="bg-slate-900/40 rounded-xl border border-slate-700 mb-12 shadow-2xl group overflow-hidden">
            <summary class="list-none cursor-pointer bg-slate-800/80 p-6 flex justify-between items-center text-xl font-bold text-white tracking-wider hover:bg-slate-700/50 transition border-b border-slate-700">
                <span class="text-blue-400">2. LAVADO DE FILTROS</span>
                <span class="transform transition-transform group-open:rotate-180 text-slate-400">▼</span>
            </summary>
            <div class="p-8">
                @if(session('success_lavados'))
                    <div class="bg-emerald-900/50 border border-emerald-500 text-emerald-200 px-4 py-3 rounded-xl mb-6 text-center text-sm font-semibold shadow-md">
                        {{ session('success_lavados') }}
                    </div>
                @endif
                @if(session('error_lavados') || $errors->hasAny(['norte_1', 'norte_2', 'norte_3', 'sur_1', 'sur_2', 'sur_3', 'inicio_lavado', 'fin_lavado', 'filtros']))
                    <div class="bg-red-900/50 border border-red-500 text-red-200 px-4 py-3 rounded-xl mb-6 text-sm font-semibold shadow-md">
                        <ul class="list-disc list-inside text-left">
                            @if(session('error_lavados'))
                                <li>{{ session('error_lavados') }}</li>
                            @endif
                            @foreach(['inicio_lavado', 'fin_lavado', 'filtros'] as $field)
                                @error($field)
                                    <li>{{ $message }}</li>
                                @enderror
                            @endforeach
                        </ul>
                    </div>
                @endif
            
            <form action="{{ route('operadores.storeFiltro') }}" method="POST" class="mb-16" onsubmit="const btns = this.querySelectorAll('button[type=submit]'); btns.forEach(b => { b.disabled = true; b.innerHTML = 'GUARDANDO...'; b.classList.add('opacity-50', 'cursor-not-allowed'); });">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                
                <div class="flex flex-col items-center">
                    <h3 class="text-sm font-bold mb-6 tracking-wide text-white">LINEA NORTE</h3>
                    <div class="space-y-4 flex flex-col items-start">
                        <label class="flex items-center cursor-pointer">
                            <input type="checkbox" name="norte_1" class="w-5 h-5 rounded border-slate-600 bg-slate-900 text-blue-600 focus:ring-blue-500" {{ old('norte_1') ? 'checked' : '' }}>
                            <span class="ml-3 text-sm font-bold text-slate-300">FILTRO 1</span>
                        </label>
                        <label class="flex items-center cursor-pointer">
                            <input type="checkbox" name="norte_2" class="w-5 h-5 rounded border-slate-600 bg-slate-900 text-blue-600 focus:ring-blue-500" {{ old('norte_2') ? 'checked' : '' }}>
                            <span class="ml-3 text-sm font-bold text-slate-300">FILTRO 2</span>
                        </label>
                        <label class="flex items-center cursor-pointer">
                            <input type="checkbox" name="norte_3" class="w-5 h-5 rounded border-slate-600 bg-slate-900 text-blue-600 focus:ring-blue-500" {{ old('norte_3') ? 'checked' : '' }}>
                            <span class="ml-3 text-sm font-bold text-slate-300">FILTRO 3</span>
                        </label>
                    </div>
                </div>

                <div class="flex flex-col items-center">
                    <h3 class="text-sm font-bold mb-6 tracking-wide text-white">LINEA SUR</h3>
                    <div class="space-y-4 flex flex-col items-start">
                        <label class="flex items-center cursor-pointer">
                            <input type="checkbox" name="sur_1" class="w-5 h-5 rounded border-slate-600 bg-slate-900 text-blue-600 focus:ring-blue-500" {{ old('sur_1') ? 'checked' : '' }}>
                            <span class="ml-3 text-sm font-bold text-slate-300">FILTRO 1</span>
                        </label>
                        <label class="flex items-center cursor-pointer">
                            <input type="checkbox" name="sur_2" class="w-5 h-5 rounded border-slate-600 bg-slate-900 text-blue-600 focus:ring-blue-500" {{ old('sur_2') ? 'checked' : '' }}>
                            <span class="ml-3 text-sm font-bold text-slate-300">FILTRO 2</span>
                        </label>
                        <label class="flex items-center cursor-pointer">
                            <input type="checkbox" name="sur_3" class="w-5 h-5 rounded border-slate-600 bg-slate-900 text-blue-600 focus:ring-blue-500" {{ old('sur_3') ? 'checked' : '' }}>
                            <span class="ml-3 text-sm font-bold text-slate-300">FILTRO 3</span>
                        </label>
                    </div>
                </div>

                <!-- Selector de Fechas de Lavado y Botón -->
<div class="col-span-1 md:col-span-2 flex flex-col items-center mt-8 space-y-6 w-full">
    <div class="flex flex-col sm:flex-row gap-8 w-full sm:w-auto items-center justify-center">
        <div class="flex flex-col items-center">
            <label class="text-xs font-bold mb-2 tracking-wide text-slate-400">INICIO DE LAVADO</label>
            <input type="datetime-local" name="inicio_lavado" required value="{{ old('inicio_lavado') }}" class="w-56 bg-slate-900 border border-slate-600 rounded p-2 text-white focus:outline-none focus:border-blue-500 font-mono text-sm">
            @error('inicio_lavado')
                <span class="text-red-400 text-xs mt-1">{{ $message }}</span>
            @enderror
        </div>
        <div class="flex flex-col items-center">
            <label class="text-xs font-bold mb-2 tracking-wide text-slate-400">FIN DE LAVADO</label>
            <input type="datetime-local" name="fin_lavado" required value="{{ old('fin_lavado') }}" class="w-56 bg-slate-900 border border-slate-600 rounded p-2 text-white focus:outline-none focus:border-blue-500 font-mono text-sm">
            @error('fin_lavado')
                <span class="text-red-400 text-xs mt-1">{{ $message }}</span>
            @enderror
        </div>
    </div>
    
    <button type="submit" class="bg-blue-600 hover:bg-blue-500 text-white font-bold py-2 px-12 rounded shadow-lg transition tracking-wide text-sm">
       CONFIRMAR LAVADO
    </button>
</div>
                </div>
            </form>

        <div class="mt-8 mb-16 bg-slate-900/50 rounded-xl border border-slate-700 p-6 shadow-2xl">
            <h2 class="text-xl font-bold text-white text-center mb-6 tracking-wider uppercase">
                REGISTROS DE LAVADO DE FILTROS
            </h2>
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-slate-300 border-collapse block md:table border-none md:border md:border-slate-700">
                    <thead class="text-xs uppercase bg-slate-800 text-slate-400 tracking-wider hidden md:table-header-group text-center">
                        <tr>
                            <th scope="col" class="py-3 px-4 border border-slate-700">Operador</th>
                            <th scope="col" class="py-3 px-4 border border-slate-700">Inicio de Lavado</th>
                            <th scope="col" class="py-3 px-4 border border-slate-700">Fin de Lavado</th>
                            <th scope="col" class="py-3 px-4 border border-slate-700">Filtros Lavados</th>
                            <th scope="col" class="py-3 px-4 border border-slate-700">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="font-medium">
                        @forelse($ultimosLavados as $lavado)
                            <tr class="hover:bg-slate-800/40 transition block md:table-row border border-slate-700 md:border-none mb-4 md:mb-0 rounded-lg md:rounded-none overflow-hidden bg-slate-900/60 md:bg-transparent">
                                <td data-label="OPERADOR" class="py-3 px-4 text-white font-semibold border-b md:border border-slate-700 flex md:table-cell justify-between items-center text-right md:text-center before:content-[attr(data-label)] before:font-bold before:text-slate-500 before:text-xs md:before:content-none">
                                    <span>{{ $lavado->user->name ?? 'N/A' }}</span>
                                </td>
                                <td data-label="INICIO DE LAVADO" class="py-3 px-4 font-mono text-slate-400 border-b md:border border-slate-700 flex md:table-cell justify-between items-center text-right md:text-center before:content-[attr(data-label)] before:font-bold before:text-slate-500 before:text-xs md:before:content-none">
                                    <span>{{ $lavado->inicio_lavado ? $lavado->inicio_lavado->format('d/m/Y H:i') : '-' }}</span>
                                </td>
                                <td data-label="FIN DE LAVADO" class="py-3 px-4 font-mono text-slate-400 border-b md:border border-slate-700 flex md:table-cell justify-between items-center text-right md:text-center before:content-[attr(data-label)] before:font-bold before:text-slate-500 before:text-xs md:before:content-none">
                                    <span>{{ $lavado->fin_lavado ? $lavado->fin_lavado->format('d/m/Y H:i') : '-' }}</span>
                                </td>
                                <td data-label="FILTROS LAVADOS" class="py-3 px-4 text-emerald-400 border-b md:border border-slate-700 flex md:table-cell justify-between items-center text-right md:text-center before:content-[attr(data-label)] before:font-bold before:text-slate-500 before:text-xs md:before:content-none">
                                    <span>
                                    @php
                                        $filtros = [];
                                        if($lavado->norte_1) $filtros[] = 'Norte 1';
                                        if($lavado->norte_2) $filtros[] = 'Norte 2';
                                        if($lavado->norte_3) $filtros[] = 'Norte 3';
                                        if($lavado->sur_1) $filtros[] = 'Sur 1';
                                        if($lavado->sur_2) $filtros[] = 'Sur 2';
                                        if($lavado->sur_3) $filtros[] = 'Sur 3';
                                    @endphp
                                    {{ empty($filtros) ? 'Ninguno' : implode(', ', $filtros) }}
                                    </span>
                                </td>
                                <td data-label="ACCIONES" class="py-3 px-4 md:border border-slate-700 flex md:table-cell justify-between items-center text-right md:text-center before:content-[attr(data-label)] before:font-bold before:text-slate-500 before:text-xs md:before:content-none">
                                    @if(auth()->id() == $lavado->user_id && $lavado->created_at->gt(now()->subHours(2)))
                                        <button type="button" 
                                                onclick="confirmarEliminar('delete-filter-form', '{{ route('operadores.destroyFiltro', $lavado->id) }}', '¿Seguro que deseas borrar este registro de lavado?')"
                                                class="bg-red-600/85 hover:bg-red-600 text-white py-1 px-3 rounded text-xs font-bold transition shadow-sm inline-flex items-center gap-1">
                                            <i class="fa-solid fa-trash text-[10px]"></i> Borrar
                                        </button>
                                    @else
                                        <span class="text-slate-500 text-xs">-</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr class="block md:table-row">
                                <td colspan="5" class="py-8 text-center text-slate-500 font-semibold border border-slate-700 block md:table-cell">
                                    No hay registros de lavado de filtros todavía.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        </div>
        </details>

        <!-- Formulario oculto para eliminación de presiones (previene formularios anidados en HTML) -->
        <form id="delete-pressure-form" action="" method="POST" class="hidden">
            @csrf
            @method('DELETE')
        </form>

        <form id="delete-filter-form" action="" method="POST" class="hidden">
            @csrf
            @method('DELETE')
        </form>

        <details id="details-quimicos" @if($erroresQuimicos) open @endif class="bg-slate-900/40 rounded-xl border border-slate-700 mb-12 shadow-2xl group overflow-hidden">
            <summary class="list-none cursor-pointer bg-slate-800/80 p-6 flex justify-between items-center text-xl font-bold text-white tracking-wider hover:bg-slate-700/50 transition border-b border-slate-700">
                <span class="text-blue-400">3. NIVELES DE TANQUES QUÍMICOS</span>
                <span class="transform transition-transform group-open:rotate-180 text-slate-400">▼</span>
            </summary>
            <div class="p-8">
                @if(session('success_quimicos'))
                    <div class="bg-emerald-900/50 border border-emerald-500 text-emerald-200 px-4 py-3 rounded-xl mb-6 text-center text-sm font-semibold shadow-md">
                        {{ session('success_quimicos') }}
                    </div>
                @endif
                @if(session('error_quimicos') || $errors->hasAny(['quimico', 'tanque_principal', 'tanque_auxiliar']))
                    <div class="bg-red-900/50 border border-red-500 text-red-200 px-4 py-3 rounded-xl mb-6 text-sm font-semibold shadow-md">
                        <ul class="list-disc list-inside text-left">
                            @if(session('error_quimicos'))
                                <li>{{ session('error_quimicos') }}</li>
                            @endif
                            @foreach(['quimico', 'tanque_principal', 'tanque_auxiliar'] as $field)
                                @error($field)
                                    <li>{{ $message }}</li>
                                @enderror
                            @endforeach
                        </ul>
                    </div>
                @endif
        <form action="{{ route('operadores.storeQuimico') }}" method="POST" class="mb-16" onsubmit="const btn = this.querySelector('button[type=submit]'); btn.disabled = true; btn.innerHTML = 'GUARDANDO...'; btn.classList.add('opacity-50', 'cursor-not-allowed');">
            @csrf
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-12 text-center mb-8">
                <!-- CLORO -->
                <div class="flex flex-col items-center space-y-6">
                    <h3 class="text-lg font-bold text-yellow-400 tracking-widest">CLORO</h3>
                    
                    <div class="w-full max-w-xs bg-slate-850 p-4 rounded border border-slate-700/50">
                        <p class="text-xs font-bold text-slate-300 mb-1">TANQUE PRINCIPAL</p>
                        <p class="text-sm font-medium text-slate-400 mb-3">Nivel actual: <span class="text-white font-mono">{{ $ultimoCloro && $ultimoCloro->tanque_principal !== null ? number_format($ultimoCloro->tanque_principal, 2) . '%' : 'N/A' }}</span></p>
                        <input type="number" name="cloro_principal" value="{{ old('cloro_principal') }}" max="100" min="0" step="0.01" class="w-full bg-slate-900 border-2 focus:border-yellow-500 rounded p-2 text-center text-white focus:outline-none focus:ring-0 focus:border-yellow-500 mb-2 font-mono" placeholder="00.0%">   
                    </div>

                    <div class="w-full max-w-xs bg-slate-850 p-4 rounded border border-slate-700/50">
                        <p class="text-xs font-bold text-slate-300 mb-1">TANQUE AUXILIAR</p>
                        <p class="text-sm font-medium text-slate-400 mb-3">Nivel actual: <span class="text-white font-mono">{{ $ultimoCloro && $ultimoCloro->tanque_auxiliar !== null ? number_format($ultimoCloro->tanque_auxiliar, 2) . '%' : 'N/A' }}</span></p>
                        <input type="number" name="cloro_auxiliar" value="{{ old('cloro_auxiliar') }}" max="100" min="0" step="0.01" class="w-full bg-slate-900 border-2 focus:border-yellow-500 rounded p-2 text-center text-white focus:outline-none focus:ring-0 focus:border-yellow-500 mb-2 font-mono" placeholder="00.0%">
                    </div>
                </div>

                <!-- POLIAMINA -->
                <div class="flex flex-col items-center space-y-6">
                    <h3 class="text-lg font-bold text-emerald-400 tracking-widest">POLIAMINA</h3>
                    
                    <div class="w-full max-w-xs bg-slate-850 p-4 rounded border border-slate-700/50">
                        <p class="text-xs font-bold text-slate-300 mb-1">TANQUE PRINCIPAL</p>
                        <p class="text-sm font-medium text-slate-400 mb-3">Nivel actual: <span class="text-white font-mono">{{ $ultimaPoliamina && $ultimaPoliamina->tanque_principal !== null ? number_format($ultimaPoliamina->tanque_principal, 2) . '%' : 'N/A' }}</span></p>
                        <input type="number" name="poliamina_principal" value="{{ old('poliamina_principal') }}" max="100" min="0" step="0.01" class="w-full bg-slate-900 border-2 rounded p-2 text-center text-white focus:outline-none focus:ring-0 focus:border-emerald-500 mb-2 font-mono" placeholder="00.0%">
                    </div>

                    <div class="w-full max-w-xs bg-slate-850 p-4 rounded border border-slate-700/50">
                        <p class="text-xs font-bold text-slate-300 mb-1">TANQUE AUXILIAR</p>
                        <p class="text-sm font-medium text-slate-400 mb-3">Nivel actual: <span class="text-white font-mono">{{ $ultimaPoliamina && $ultimaPoliamina->tanque_auxiliar !== null ? number_format($ultimaPoliamina->tanque_auxiliar, 2) . '%' : 'N/A' }}</span></p>
                        <input type="number" name="poliamina_auxiliar" value="{{ old('poliamina_auxiliar') }}" step="0.01" class="w-full bg-slate-900 border-2 rounded p-2 text-center text-white focus:outline-none focus:ring-0 focus:border-emerald-500 mb-2 font-mono" placeholder="00.0%">
                    </div>
                </div>

                <!-- SULFATO -->
                <div class="flex flex-col items-center space-y-6">
                    <h3 class="text-lg font-bold text-red-400 tracking-widest">SULFATO</h3>
                    
                    <div class="w-full max-w-xs bg-slate-850 p-4 rounded border border-slate-700/50">
                        <p class="text-xs font-bold text-slate-300 mb-1">TANQUE PRINCIPAL</p>
                        <p class="text-sm font-medium text-slate-400 mb-3">Nivel actual: <span class="text-white font-mono">{{ $ultimoSulfato && $ultimoSulfato->tanque_principal !== null ? number_format($ultimoSulfato->tanque_principal, 2) . '%' : 'N/A' }}</span></p>
                        <input type="number" name="sulfato_principal" value="{{ old('sulfato_principal') }}" max="100" min="0" step="0.01" class="w-full bg-slate-900 border-2 rounded p-2 text-center text-white focus:outline-none focus:ring-0 focus:border-red-500 mb-2 font-mono" placeholder="00.0%">
                    </div>

                    <div class="w-full max-w-xs bg-slate-850 p-4 rounded border border-slate-700/50">
                        <p class="text-xs font-bold text-slate-300 mb-1">TANQUE AUXILIAR</p>
                        <p class="text-sm font-medium text-slate-400 mb-3">Nivel actual: <span class="text-white font-mono">{{ $ultimoSulfato && $ultimoSulfato->tanque_auxiliar !== null ? number_format($ultimoSulfato->tanque_auxiliar, 2) . '%' : 'N/A' }}</span></p>
                        <input type="number" name="sulfato_auxiliar" value="{{ old('sulfato_auxiliar') }}" step="0.01" class="w-full bg-slate-900 border-2 rounded p-2 text-center text-white focus:outline-none focus:ring-0 focus:border-red-500 mb-2 font-mono" placeholder="00.0%">
                    </div>
                </div>
            </div>
            
            <div class="flex justify-center">
                <button type="submit" class="bg-blue-600 hover:bg-blue-500 text-white font-bold py-3 px-12 rounded shadow-lg transition tracking-wide text-sm">
                    ACTUALIZAR NIVELES QUÍMICOS
                </button>
            </div>
        </form>
        </div>
        </details>

        <details id="novedades-details" @if($erroresNovedades) open @endif class="bg-slate-900/40 rounded-xl border border-slate-700 mb-12 shadow-2xl group overflow-hidden">
            <summary class="list-none cursor-pointer bg-slate-800/80 p-6 flex justify-between items-center text-xl font-bold text-white tracking-wider hover:bg-slate-700/50 transition border-b border-slate-700">
                <span class="text-blue-400">4. NOVEDADES Y COMENTARIOS DEL TURNO</span>
                <span class="transform transition-transform group-open:rotate-180 text-slate-400">▼</span>
            </summary>
            <div class="p-8">
                @if(session('success_novedades'))
                    <div class="bg-emerald-900/50 border border-emerald-500 text-emerald-200 px-4 py-3 rounded-xl mb-6 text-center text-sm font-semibold shadow-md">
                        {{ session('success_novedades') }}
                    </div>
                @endif
                @if(session('error_novedades') || $errors->hasAny(['mensaje']))
                    <div class="bg-red-900/50 border border-red-500 text-red-200 px-4 py-3 rounded-xl mb-6 text-sm font-semibold shadow-md">
                        <ul class="list-disc list-inside text-left">
                            @if(session('error_novedades'))
                                <li>{{ session('error_novedades') }}</li>
                            @endif
                            @foreach(['mensaje'] as $field)
                                @error($field)
                                    <li>{{ $message }}</li>
                                @enderror
                            @endforeach
                        </ul>
                    </div>
                @endif
                
                <form action="{{ route('operadores.storeNovedad') }}" method="POST" class="mb-12" onsubmit="const btns = this.querySelectorAll('button[type=submit]'); btns.forEach(b => { b.disabled = true; b.innerHTML = 'GUARDANDO...'; b.classList.add('opacity-50', 'cursor-not-allowed'); });">
                    @csrf
                    <label class="block text-sm font-bold text-slate-300 mb-3 tracking-wide">REGISTRAR NUEVA NOVEDAD (Máx. 1000 caracteres)</label>
                    <textarea name="mensaje" rows="3" class="w-full bg-slate-900 border border-slate-600 rounded p-4 text-white focus:outline-none focus:border-blue-500 mb-4 resize-none" placeholder="Escribe aquí cualquier comentario importante o novedad del turno para que el próximo operador esté enterado..."></textarea>
                    
                    <div class="flex justify-center mb-12">
                        <button type="submit" class="bg-blue-600 hover:bg-blue-500 text-white font-bold py-2 px-12 rounded shadow-lg transition tracking-wide text-sm">
                            GUARDAR NOVEDAD
                        </button>
                    </div>
                </form>

                <div class="bg-slate-900/50 rounded border border-slate-700 p-6">
                    <div class="flex justify-between items-center mb-6">
                        <h3 class="text-lg font-bold text-blue-400 tracking-widest text-center flex-grow">ÚLTIMAS NOVEDADES</h3>
                        @if($novedadesRecientes > 0)
                        <form action="{{ route('operadores.marcarLeidas') }}" method="POST" onsubmit="const btn = this.querySelector('button'); btn.disabled = true; btn.innerHTML = 'MARCANDO...'; btn.classList.add('opacity-50', 'cursor-not-allowed');">
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
                                                    onclick="confirmarEliminar('delete-novedad-form', '{{ route('operadores.destroyNovedad', $novedad->id) }}', '¿Seguro que deseas borrar esta novedad?')"
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

    <!-- Formularios ocultos para eliminar -->
    <form id="delete-pressure-form" method="POST" style="display: none;">
        @csrf
        @method('DELETE')
    </form>
    <form id="delete-filter-form" method="POST" style="display: none;">
        @csrf
        @method('DELETE')
    </form>
    <form id="delete-novedad-form" method="POST" style="display: none;">
        @csrf
        @method('DELETE')
    </form>

    <script>
    // Configuración global de SweetAlert2 con tema Slate
    const SwalCustom = Swal.mixin({
        background: '#1e293b', // slate-800
        color: '#f8fafc', // slate-50
        confirmButtonColor: '#2563eb', // blue-600
        denyButtonColor: '#475569', // slate-600
        cancelButtonColor: '#dc2626', // red-600
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

    // Función para confirmación de eliminación con SweetAlert2
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

    // Restaurar estado de los bloques (abiertos o cerrados) usando sessionStorage y lanzar alertas
    document.addEventListener('DOMContentLoaded', () => {
        const detailsElements = document.querySelectorAll('details');
        detailsElements.forEach(detail => {
            if (!detail.id) return;
            if (detail.open) {
                sessionStorage.setItem('block_state_' + detail.id, 'open');
            } else {
                const state = sessionStorage.getItem('block_state_' + detail.id);
                if (state === 'open') {
                    detail.open = true;
                }
            }
            
            detail.addEventListener('toggle', () => {
                if (detail.open) {
                    sessionStorage.setItem('block_state_' + detail.id, 'open');
                } else {
                    sessionStorage.removeItem('block_state_' + detail.id);
                }
            });
        });

        // Guardar la posición exacta del scroll antes de que la página se recargue
        window.addEventListener('beforeunload', () => {
            sessionStorage.setItem('scroll_position', window.scrollY);
        });

        // Restaurar la posición del scroll si venimos de una recarga/submit
        const scrollPos = sessionStorage.getItem('scroll_position');
        if (scrollPos) {
            // Usamos setTimeout para asegurar que se ejecute después del renderizado inicial
            setTimeout(() => {
                window.scrollTo({ top: parseInt(scrollPos), behavior: 'instant' });
                sessionStorage.removeItem('scroll_position');
            }, 10);
        }

        // Disparar alertas de SweetAlert2 basadas en estado de sesión o validación
        @if($errors->any())
            @php
                $errorList = '<ul class="text-left list-disc list-inside space-y-1 font-mono text-xs text-red-300">';
                foreach($errors->all() as $error) {
                    $errorList .= '<li>' . e($error) . '</li>';
                }
                $errorList .= '</ul>';
            @endphp
            SwalCustom.fire({
                title: 'Faltan completar o corregir datos',
                html: '{!! $errorList !!}',
                icon: 'error',
                confirmButtonText: 'Aceptar'
            });
        @elseif(session('success_presiones') || session('success_lavados') || session('success_quimicos') || session('success_novedades') || session('success'))
            @php
                $successMsg = session('success_presiones') ?? session('success_lavados') ?? session('success_quimicos') ?? session('success_novedades') ?? session('success');
                $isDeleted = str_contains(strtolower($successMsg), 'eliminad') || str_contains(strtolower($successMsg), 'borrad');
            @endphp
            SwalCustom.fire({
                title: '{{ $isDeleted ? "¡Eliminado!" : "¡Guardado!" }}',
                text: "{{ $successMsg }}",
                icon: 'success',
                confirmButtonText: 'Aceptar'
            });
        @elseif(session('error_presiones') || session('error_lavados') || session('error_quimicos') || session('error_novedades') || session('error'))
            SwalCustom.fire({
                title: '¡Error!',
                text: "{{ session('error_presiones') ?? session('error_lavados') ?? session('error_quimicos') ?? session('error_novedades') ?? session('error') }}",
                icon: 'error',
                confirmButtonText: 'Cerrar'
            });
        @endif
    });

    // Paginación en Frontend para Tabla de Presiones
    let currentPage = 1;
    const totalItems = {{ count($ultimosRegistros) }};
    const itemsPerPage = 8;
    const totalPages = Math.ceil(totalItems / itemsPerPage);

    function changePage(direction) {
        currentPage += direction;
        
        if (currentPage < 1) currentPage = 1;
        if (currentPage > totalPages) currentPage = totalPages;

        const indicator = document.getElementById('page-indicator');
        if(indicator) indicator.innerText = `Página ${currentPage} / ${totalPages}`;

        const rows = document.querySelectorAll('.presion-row');
        rows.forEach(row => {
            const index = parseInt(row.getAttribute('data-index'));
            const start = (currentPage - 1) * itemsPerPage;
            const end = start + itemsPerPage - 1;
            
            if (index >= start && index <= end) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    }
</script>

<!-- Botón Volver Arriba -->
<button id="btn-scroll-top" title="Volver al inicio" onclick="window.scrollTo({top:0,behavior:'smooth'})">
    <i class="fa-solid fa-chevron-up"></i>
</button>

<script>
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