<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Formulario de Operadores</title>
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
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
        
        /* Pintar de blanco el icono del calendario en los campos de fecha */
        input[type="datetime-local"]::-webkit-calendar-picker-indicator {
            cursor: pointer;
        }
        /* Para navegadores que soporten color-scheme */
        input[type="datetime-local"] {
            color-scheme: dark;
        }
    </style>
</head>
<body class="bg-slate-800 text-slate-200 font-sans min-h-screen p-8">

    <div class="max-w-6xl mx-auto">
        
       <div class="relative mb-12">
    <a href="/dashboard" class="absolute left-0 top-1 bg-slate-700 hover:bg-slate-600 text-white py-2 px-6 rounded border border-slate-500 transition text-sm font-semibold">
         ← VOLVER AL MENÚ
    </a>
    <h1 class="text-2xl font-bold text-white tracking-wider text-center">FORMULARIO DE OPERADOR DE TURNO</h1>
</div>

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

        <details id="details-presiones" class="bg-slate-900/40 rounded-xl border border-slate-700 mb-12 shadow-2xl group overflow-hidden">
            <summary class="list-none cursor-pointer bg-slate-800/80 p-6 flex justify-between items-center text-xl font-bold text-white tracking-wider hover:bg-slate-700/50 transition border-b border-slate-700">
                <span class="text-blue-400">1. PRESIONES Y NIVELES DE CISTERNA</span>
                <span class="transform transition-transform group-open:rotate-180 text-slate-400">▼</span>
            </summary>
            <div class="p-8">
                <form action="/operadores/presion" method="POST" class="mb-16" onsubmit="const btns = this.querySelectorAll('button[type=submit]'); btns.forEach(b => { b.disabled = true; b.innerHTML = 'GUARDANDO...'; b.classList.add('opacity-50', 'cursor-not-allowed'); });">
            @csrf 

            <div class="grid grid-cols-4 gap-8 mb-8 text-center">
                <div class="flex flex-col items-center">
                    <label class="text-xs font-bold mb-2 tracking-wide text-slate-400">BAJADA DE TANQUE</label>
                    <input type="number" name="presion_tanque" step="0.01" value="{{ old('presion_tanque') }}" class="w-32 bg-slate-900 border border-slate-600 rounded p-2 text-center text-white focus:outline-none focus:border-blue-500 font-mono" placeholder="0.00">
                </div>
                
                <div class="flex flex-col items-center">
                    <label class="text-xs font-bold mb-2 tracking-wide text-slate-400">PLANTA</label>
                    <input type="number" name="presion_planta" step="0.01" value="{{ old('presion_planta') }}" class="w-32 bg-slate-900 border border-slate-600 rounded p-2 text-center text-white focus:outline-none focus:border-blue-500 font-mono" placeholder="0.00">
                </div>

                <div class="flex flex-col items-center">
                    <label class="text-xs font-bold mb-2 tracking-wide text-slate-400">TANQUE DE FALCON</label>
                    <input type="number" name="presion_falcon" step="0.01" value="{{ old('presion_falcon') }}" class="w-32 bg-slate-900 border border-slate-600 rounded p-2 text-center text-white focus:outline-none focus:border-blue-500 font-mono" placeholder="0.00">
                </div>

                <div class="flex flex-col items-center">
                    <label class="text-xs font-bold mb-2 tracking-wide text-slate-400">NIVEL DE CISTERNA (%)</label>
                    <input type="number" name="nivel_cisterna" step="0.01" value="{{ old('nivel_cisterna') }}" class="w-32 bg-slate-900 border border-slate-600 rounded p-2 text-center text-white focus:outline-none focus:border-blue-500 font-mono" placeholder="0.00%">
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
                <table class="w-full text-center text-sm text-slate-300 border-collapse border border-slate-700">
                    <thead class="text-xs uppercase bg-slate-800 text-slate-400 tracking-wider">
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
                            <tr class="hover:bg-slate-800/40 transition presion-row" data-index="{{ $index }}" style="{{ $index >= 8 ? 'display:none;' : '' }}">
                                <td class="py-4 px-4 font-mono text-slate-400 border border-slate-700">
                                    {{ $registro->created_at->format('d/m/Y H:i') }}
                                </td>
                                <td class="py-4 px-4 text-white font-semibold border border-slate-700">
                                    {{ $registro->user->name ?? 'N/A' }}
                                </td>
                                <td class="py-4 px-4 text-center font-mono text-blue-400 border border-slate-700">
                                    {{ $registro->presion_tanque !== null ? number_format($registro->presion_tanque, 2) . ' MCA' : '-' }}
                                </td>
                                <td class="py-4 px-4 text-center font-mono text-blue-400 border border-slate-700">
                                    {{ $registro->presion_planta !== null ? number_format($registro->presion_planta, 2) . ' MCA' : '-' }}
                                </td>
                                <td class="py-4 px-4 text-center font-mono text-blue-400 border border-slate-700">
                                    {{ $registro->presion_falcon !== null ? number_format($registro->presion_falcon, 2) . ' MCA' : '-' }}
                                </td>
                                <td class="py-4 px-4 text-center font-mono text-emerald-400 font-bold border border-slate-700">
                                    {{ $registro->nivel_cisterna !== null ? number_format($registro->nivel_cisterna, 2) . '% (' . number_format(($registro->nivel_cisterna / 100) * 7, 2) . ' m)' : '-' }}
                                </td>
                                <td class="py-4 px-4 text-center border border-slate-700">
                                    @if(auth()->id() == $registro->user_id && $registro->created_at->gt(now()->subHours(2)))
                                        <button type="button" 
                                                onclick="if(confirm('¿Seguro que deseas borrar este registro de presión?')) { 
                                                    const form = document.getElementById('delete-pressure-form'); 
                                                    form.action = '/operadores/presion/{{ $registro->id }}'; 
                                                    form.submit(); 
                                                }"
                                                class="bg-red-600/85 hover:bg-red-600 text-white py-1 px-3 rounded text-xs font-bold transition shadow-sm flex items-center gap-1 mx-auto">
                                            <i class="fa-solid fa-trash text-[10px]"></i> Borrar
                                        </button>
                                    @else
                                        <span class="text-slate-500 text-xs">-</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="py-8 text-center text-slate-500 font-semibold border border-slate-700">
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

        <details id="details-lavados" class="bg-slate-900/40 rounded-xl border border-slate-700 mb-12 shadow-2xl group overflow-hidden">
            <summary class="list-none cursor-pointer bg-slate-800/80 p-6 flex justify-between items-center text-xl font-bold text-white tracking-wider hover:bg-slate-700/50 transition border-b border-slate-700">
                <span class="text-blue-400">2. LAVADO DE FILTROS</span>
                <span class="transform transition-transform group-open:rotate-180 text-slate-400">▼</span>
            </summary>
            <div class="p-8">
            
            <form action="/operadores/filtro" method="POST" class="mb-16" onsubmit="const btns = this.querySelectorAll('button[type=submit]'); btns.forEach(b => { b.disabled = true; b.innerHTML = 'GUARDANDO...'; b.classList.add('opacity-50', 'cursor-not-allowed'); });">
                @csrf
                <div class="grid grid-cols-2 gap-8">
                
                <div class="flex flex-col items-center">
                    <h3 class="text-sm font-bold mb-6 tracking-wide text-white">LINEA NORTE</h3>
                    <div class="space-y-4 flex flex-col items-start">
                        <label class="flex items-center cursor-pointer">
                            <input type="checkbox" name="norte_1" class="w-5 h-5 rounded border-slate-600 bg-slate-900 text-blue-600 focus:ring-blue-500">
                            <span class="ml-3 text-sm font-bold text-slate-300">FILTRO 1</span>
                        </label>
                        <label class="flex items-center cursor-pointer">
                            <input type="checkbox" name="norte_2" class="w-5 h-5 rounded border-slate-600 bg-slate-900 text-blue-600 focus:ring-blue-500">
                            <span class="ml-3 text-sm font-bold text-slate-300">FILTRO 2</span>
                        </label>
                        <label class="flex items-center cursor-pointer">
                            <input type="checkbox" name="norte_3" class="w-5 h-5 rounded border-slate-600 bg-slate-900 text-blue-600 focus:ring-blue-500">
                            <span class="ml-3 text-sm font-bold text-slate-300">FILTRO 3</span>
                        </label>
                    </div>
                </div>

                <div class="flex flex-col items-center">
                    <h3 class="text-sm font-bold mb-6 tracking-wide text-white">LINEA SUR</h3>
                    <div class="space-y-4 flex flex-col items-start">
                        <label class="flex items-center cursor-pointer">
                            <input type="checkbox" name="sur_1" class="w-5 h-5 rounded border-slate-600 bg-slate-900 text-blue-600 focus:ring-blue-500">
                            <span class="ml-3 text-sm font-bold text-slate-300">FILTRO 1</span>
                        </label>
                        <label class="flex items-center cursor-pointer">
                            <input type="checkbox" name="sur_2" class="w-5 h-5 rounded border-slate-600 bg-slate-900 text-blue-600 focus:ring-blue-500">
                            <span class="ml-3 text-sm font-bold text-slate-300">FILTRO 2</span>
                        </label>
                        <label class="flex items-center cursor-pointer">
                            <input type="checkbox" name="sur_3" class="w-5 h-5 rounded border-slate-600 bg-slate-900 text-blue-600 focus:ring-blue-500">
                            <span class="ml-3 text-sm font-bold text-slate-300">FILTRO 3</span>
                        </label>
                    </div>
                </div>

                <!-- Selector de Fechas de Lavado y Botón -->
                <div class="col-span-2 flex flex-col items-center mt-8 space-y-6">
                    <div class="flex gap-8">
                        <div class="flex flex-col items-center">
                            <label class="text-xs font-bold mb-2 tracking-wide text-slate-400">INICIO DE LAVADO</label>
                            <input type="datetime-local" name="inicio_lavado" required value="{{ old('inicio_lavado') }}" class="w-56 bg-slate-900 border border-slate-600 rounded p-2 text-white focus:outline-none focus:border-blue-500 font-mono text-sm">
                        </div>
                        <div class="flex flex-col items-center">
                            <label class="text-xs font-bold mb-2 tracking-wide text-slate-400">FIN DE LAVADO</label>
                            <input type="datetime-local" name="fin_lavado" required value="{{ old('fin_lavado') }}" class="w-56 bg-slate-900 border border-slate-600 rounded p-2 text-white focus:outline-none focus:border-blue-500 font-mono text-sm">
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
                <table class="w-full text-center text-sm text-slate-300 border-collapse border border-slate-700">
                    <thead class="text-xs uppercase bg-slate-800 text-slate-400 tracking-wider">
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
                            <tr class="hover:bg-slate-800/40 transition">
                                <td class="py-4 px-4 text-white font-semibold border border-slate-700">
                                    {{ $lavado->user->name ?? 'N/A' }}
                                </td>
                                <td class="py-4 px-4 font-mono text-slate-400 border border-slate-700">
                                    {{ $lavado->inicio_lavado ? $lavado->inicio_lavado->format('d/m/Y H:i') : '-' }}
                                </td>
                                <td class="py-4 px-4 font-mono text-slate-400 border border-slate-700">
                                    {{ $lavado->fin_lavado ? $lavado->fin_lavado->format('d/m/Y H:i') : '-' }}
                                </td>
                                <td class="py-4 px-4 text-emerald-400 border border-slate-700">
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
                                </td>
                                <td class="py-4 px-4 text-center border border-slate-700">
                                    @if(auth()->id() == $lavado->user_id && $lavado->created_at->gt(now()->subHours(2)))
                                        <button type="button" 
                                                onclick="if(confirm('¿Seguro que deseas borrar este registro de lavado?')) { 
                                                    const form = document.getElementById('delete-filtro-form'); 
                                                    form.action = '/operadores/filtro/{{ $lavado->id }}'; 
                                                    form.submit(); 
                                                }"
                                                class="bg-red-600/85 hover:bg-red-600 text-white py-1 px-3 rounded text-xs font-bold transition shadow-sm flex items-center gap-1 mx-auto">
                                            <i class="fa-solid fa-trash text-[10px]"></i> Borrar
                                        </button>
                                    @else
                                        <span class="text-slate-500 text-xs">-</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="py-8 text-center text-slate-500 font-semibold border border-slate-700">
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

        <form id="delete-filtro-form" action="" method="POST" class="hidden">
            @csrf
            @method('DELETE')
        </form>

        <details id="details-quimicos" class="bg-slate-900/40 rounded-xl border border-slate-700 mb-12 shadow-2xl group overflow-hidden">
            <summary class="list-none cursor-pointer bg-slate-800/80 p-6 flex justify-between items-center text-xl font-bold text-white tracking-wider hover:bg-slate-700/50 transition border-b border-slate-700">
                <span class="text-blue-400">3. NIVELES DE TANQUES QUÍMICOS</span>
                <span class="transform transition-transform group-open:rotate-180 text-slate-400">▼</span>
            </summary>
            <div class="p-8">
                <div class="grid grid-cols-3 gap-12 text-center mb-16">
            
            <form action="/operadores/quimico" method="POST" class="flex flex-col items-center space-y-6" onsubmit="const btns = this.querySelectorAll('button[type=submit]'); btns.forEach(b => { b.disabled = true; b.innerHTML = 'GUARDANDO...'; b.classList.add('opacity-50', 'cursor-not-allowed'); });">
                @csrf
                <input type="hidden" name="quimico" value="cloro">
                <h3 class="text-lg font-bold text-blue-400 tracking-widest">CLORO</h3>
                
                <div class="w-full max-w-xs bg-slate-850 p-4 rounded border border-slate-700/50">
                    <p class="text-xs font-bold text-slate-300 mb-1">TANQUE PRINCIPAL</p>
                    <p class="text-sm font-medium text-slate-400 mb-3">Nivel actual: <span class="text-white font-mono">{{ $ultimoCloro && $ultimoCloro->tanque_principal !== null ? number_format($ultimoCloro->tanque_principal, 2) . '%' : 'N/A' }}</span></p>
                    <input type="number" name="tanque_principal" step="0.01" class="w-full bg-slate-900 border border-slate-600 rounded p-2 text-center text-white focus:outline-none focus:border-blue-500 mb-2 font-mono" placeholder="00.0%">
                </div>

                <div class="w-full max-w-xs bg-slate-850 p-4 rounded border border-slate-700/50">
                    <p class="text-xs font-bold text-slate-300 mb-1">TANQUE AUXILIAR</p>
                    <p class="text-sm font-medium text-slate-400 mb-3">Nivel actual: <span class="text-white font-mono">{{ $ultimoCloro && $ultimoCloro->tanque_auxiliar !== null ? number_format($ultimoCloro->tanque_auxiliar, 2) . '%' : 'N/A' }}</span></p>
                    <input type="number" name="tanque_auxiliar" step="0.01" class="w-full bg-slate-900 border border-slate-600 rounded p-2 text-center text-white focus:outline-none focus:border-blue-500 mb-2 font-mono" placeholder="00.0%">
                </div>
                
                <button type="submit" class="w-full max-w-xs bg-blue-600 hover:bg-blue-500 text-white font-bold py-3 rounded shadow-lg transition tracking-wide text-sm">
                    ACTUALIZAR CLORO
                </button>
            </form>

            <form action="/operadores/quimico" method="POST" class="flex flex-col items-center space-y-6" onsubmit="const btns = this.querySelectorAll('button[type=submit]'); btns.forEach(b => { b.disabled = true; b.innerHTML = 'GUARDANDO...'; b.classList.add('opacity-50', 'cursor-not-allowed'); });">
                @csrf
                <input type="hidden" name="quimico" value="poliamina">
                <h3 class="text-lg font-bold text-emerald-400 tracking-widest">POLIAMINA</h3>
                
                <div class="w-full max-w-xs bg-slate-850 p-4 rounded border border-slate-700/50">
                    <p class="text-xs font-bold text-slate-300 mb-1">TANQUE PRINCIPAL</p>
                    <p class="text-sm font-medium text-slate-400 mb-3">Nivel actual: <span class="text-white font-mono">{{ $ultimaPoliamina && $ultimaPoliamina->tanque_principal !== null ? number_format($ultimaPoliamina->tanque_principal, 2) . '%' : 'N/A' }}</span></p>
                    <input type="number" name="tanque_principal" step="0.01" class="w-full bg-slate-900 border border-slate-600 rounded p-2 text-center text-white focus:outline-none focus:border-emerald-500 mb-2 font-mono" placeholder="00.0%">
                </div>

                <div class="w-full max-w-xs bg-slate-850 p-4 rounded border border-slate-700/50">
                    <p class="text-xs font-bold text-slate-300 mb-1">TANQUE AUXILIAR</p>
                    <p class="text-sm font-medium text-slate-400 mb-3">Nivel actual: <span class="text-white font-mono">{{ $ultimaPoliamina && $ultimaPoliamina->tanque_auxiliar !== null ? number_format($ultimaPoliamina->tanque_auxiliar, 2) . '%' : 'N/A' }}</span></p>
                    <input type="number" name="tanque_auxiliar" step="0.01" class="w-full bg-slate-900 border border-slate-600 rounded p-2 text-center text-white focus:outline-none focus:border-emerald-500 mb-2 font-mono" placeholder="00.0%">
                </div>
                
                <button type="submit" class="w-full max-w-xs bg-emerald-600 hover:bg-emerald-500 text-white font-bold py-3 rounded shadow-lg transition tracking-wide text-sm">
                    ACTUALIZAR POLIAMINA
                </button>
            </form>

            <form action="/operadores/quimico" method="POST" class="flex flex-col items-center space-y-6" onsubmit="const btns = this.querySelectorAll('button[type=submit]'); btns.forEach(b => { b.disabled = true; b.innerHTML = 'GUARDANDO...'; b.classList.add('opacity-50', 'cursor-not-allowed'); });">
                @csrf
                <input type="hidden" name="quimico" value="sulfato">
                <h3 class="text-lg font-bold text-red-400 tracking-widest">SULFATO</h3>
                
                <div class="w-full max-w-xs bg-slate-850 p-4 rounded border border-slate-700/50">
                    <p class="text-xs font-bold text-slate-300 mb-1">TANQUE PRINCIPAL</p>
                    <p class="text-sm font-medium text-slate-400 mb-3">Nivel actual: <span class="text-white font-mono">{{ $ultimoSulfato && $ultimoSulfato->tanque_principal !== null ? number_format($ultimoSulfato->tanque_principal, 2) . '%' : 'N/A' }}</span></p>
                    <input type="number" name="tanque_principal" step="0.01" class="w-full bg-slate-900 border border-slate-600 rounded p-2 text-center text-white focus:outline-none focus:border-red-500 mb-2 font-mono" placeholder="00.0%">
                </div>

                <div class="w-full max-w-xs bg-slate-850 p-4 rounded border border-slate-700/50">
                    <p class="text-xs font-bold text-slate-300 mb-1">TANQUE AUXILIAR</p>
                    <p class="text-sm font-medium text-slate-400 mb-3">Nivel actual: <span class="text-white font-mono">{{ $ultimoSulfato && $ultimoSulfato->tanque_auxiliar !== null ? number_format($ultimoSulfato->tanque_auxiliar, 2) . '%' : 'N/A' }}</span></p>
                    <input type="number" name="tanque_auxiliar" step="0.01" class="w-full bg-slate-900 border border-slate-600 rounded p-2 text-center text-white focus:outline-none focus:border-red-500 mb-2 font-mono" placeholder="00.0%">
                </div>
                
                <button type="submit" class="w-full max-w-xs bg-red-600 hover:bg-red-500 text-white font-bold py-3 rounded shadow-lg transition tracking-wide text-sm">
                    ACTUALIZAR SULFATO
                </button>
            </form>

        </div>
        </div>
        </details>

        <details id="novedades-details" class="bg-slate-900/40 rounded-xl border border-slate-700 mb-12 shadow-2xl group overflow-hidden">
            <summary class="list-none cursor-pointer bg-slate-800/80 p-6 flex justify-between items-center text-xl font-bold text-white tracking-wider hover:bg-slate-700/50 transition border-b border-slate-700">
                <span class="text-blue-400">4. NOVEDADES Y COMENTARIOS DEL TURNO</span>
                <span class="transform transition-transform group-open:rotate-180 text-slate-400">▼</span>
            </summary>
            <div class="p-8">
                
                <form action="/operadores/novedad" method="POST" class="mb-12" onsubmit="const btns = this.querySelectorAll('button[type=submit]'); btns.forEach(b => { b.disabled = true; b.innerHTML = 'GUARDANDO...'; b.classList.add('opacity-50', 'cursor-not-allowed'); });">
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
                        <form action="/operadores/novedades/leidas" method="POST" onsubmit="const btn = this.querySelector('button'); btn.disabled = true; btn.innerHTML = 'MARCANDO...'; btn.classList.add('opacity-50', 'cursor-not-allowed');">
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
                                                    onclick="if(confirm('¿Seguro que deseas borrar esta novedad?')) { 
                                                        const form = document.getElementById('delete-novedad-form'); 
                                                        form.action = '/operadores/novedad/{{ $novedad->id }}'; 
                                                        form.submit(); 
                                                    }"
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
        // Restaurar estado de los bloques (abiertos o cerrados)
        document.addEventListener('DOMContentLoaded', () => {
            const detailsElements = document.querySelectorAll('details');
            detailsElements.forEach(detail => {
                if (!detail.id) return;
                // Si la alerta de novedades abrió el detalle, tiene prioridad
                if (detail.open) {
                    localStorage.setItem('block_state_' + detail.id, 'open');
                } else {
                    const state = localStorage.getItem('block_state_' + detail.id);
                    if (state === 'open') {
                        detail.open = true;
                    }
                }
                
                detail.addEventListener('toggle', () => {
                    if (detail.open) {
                        localStorage.setItem('block_state_' + detail.id, 'open');
                    } else {
                        localStorage.removeItem('block_state_' + detail.id);
                    }
                });
            });
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

            // Actualizar texto del indicador
            const indicator = document.getElementById('page-indicator');
            if(indicator) indicator.innerText = `Página ${currentPage} / ${totalPages}`;

            // Ocultar todas las filas y mostrar solo las de la página actual
            const rows = document.querySelectorAll('.presion-row');
            rows.forEach(row => {
                const index = parseInt(row.getAttribute('data-index'));
                const start = (currentPage - 1) * itemsPerPage;
                const end = start + itemsPerPage - 1;
                
                if (index >= start && index <= end) {
                    row.style.display = ''; // Mostrar
                } else {
                    row.style.display = 'none'; // Ocultar
                }
            });
        }
    </script>
</body>
</html>