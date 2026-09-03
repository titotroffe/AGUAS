<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Jefatura</title>
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <style>
        .chart-container {
            position: relative;
            height: 300px;
            width: 100%;
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
        <!-- Header -->
        <div class="flex flex-col md:flex-row md:items-center md:justify-center mb-12 relative gap-6">
            <div class="md:absolute md:left-0 md:top-1/2 md:-translate-y-1/2 flex justify-center z-10">
                <a href="/menu" class="bg-slate-700 hover:bg-slate-600 text-white py-2 px-6 rounded border border-slate-500 transition text-sm font-semibold">
                     ← VOLVER AL MENÚ
                </a>
            </div>
            <h1 class="text-xl md:text-2xl font-bold text-white tracking-wider text-center m-0 w-full uppercase">
                PANEL DE JEFATURA
            </h1>
        </div>

        @php
            $ultimaPresion = $presiones->last();
        @endphp

        <!-- Panel de Usuarios Pendientes -->
        @if(isset($usuariosPendientes) && $usuariosPendientes->count() > 0)
        <div class="bg-amber-900/40 border border-amber-600 rounded-xl p-6 shadow-2xl mb-8 relative overflow-hidden">
            <div class="absolute top-0 right-0 p-4 opacity-20">
                <i class="fa-solid fa-users-gear text-6xl text-amber-500"></i>
            </div>
            <h2 class="text-xl font-bold text-amber-400 tracking-wide mb-4 relative z-10 flex items-center gap-2">
                <i class="fa-solid fa-user-clock"></i> Usuarios Pendientes de Aprobación
            </h2>
            <div class="overflow-x-auto relative z-10">
                <table class="w-full text-center text-sm text-slate-300 border-collapse border border-amber-700/50">
                    <thead class="text-xs uppercase bg-amber-900/60 text-amber-200 tracking-wider">
                        <tr>
                            <th class="py-3 px-4 border border-amber-700/50">Nombre</th>
                            <th class="py-3 px-4 border border-amber-700/50">Email</th>
                            <th class="py-3 px-4 border border-amber-700/50">Rol Solicitado</th>
                            <th class="py-3 px-4 border border-amber-700/50">Fecha de Registro</th>
                            <th class="py-3 px-4 border border-amber-700/50">Acción</th>
                        </tr>
                    </thead>
                    <tbody class="font-medium bg-slate-900/50">
                        @foreach($usuariosPendientes as $user)
                        <tr class="hover:bg-amber-900/30 transition">
                            <td class="py-3 px-4 border border-amber-700/50 font-bold text-white">{{ $user->name }}</td>
                            <td class="py-3 px-4 border border-amber-700/50">{{ $user->email }}</td>
                            <td class="py-3 px-4 border border-amber-700/50">
                                <span class="bg-amber-600/50 text-amber-100 text-xs px-2 py-1 rounded uppercase font-bold tracking-wider">
                                    {{ $user->role }}
                                </span>
                            </td>
                            <td class="py-3 px-4 border border-amber-700/50 text-xs font-mono text-slate-400">
                                {{ $user->created_at->format('d/m/Y H:i') }}
                            </td>
                            <td class="py-3 px-4 border border-amber-700/50">
                                <div class="flex justify-center gap-2">
                                    <form method="POST" action="{{ route('jefatura.aprobarUsuario', $user->id) }}" onsubmit="return confirm('¿Aprobar acceso al sistema para este usuario?');">
                                        @csrf
                                        <button type="submit" class="bg-emerald-600 hover:bg-emerald-500 text-white font-bold py-1.5 px-4 rounded text-xs transition shadow flex items-center gap-2">
                                            <i class="fa-solid fa-check"></i> Aprobar
                                        </button>
                                    </form>
                                    <form method="POST" action="{{ route('jefatura.rechazarUsuario', $user->id) }}" onsubmit="return confirm('¿Rechazar y eliminar a este usuario?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="bg-red-600 hover:bg-red-500 text-white font-bold py-1.5 px-4 rounded text-xs transition shadow flex items-center gap-2">
                                            <i class="fa-solid fa-times"></i> Rechazar
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @endif

        <!-- Panel Gestión de Personal -->
        <details class="bg-slate-900/40 rounded-xl border border-slate-700 mb-8 shadow-2xl group overflow-hidden">
            <summary class="list-none cursor-pointer bg-slate-800/80 p-6 flex justify-between items-center text-xl font-bold text-white tracking-wider hover:bg-slate-700/50 transition border-b border-slate-700">
                <span class="text-indigo-400 uppercase">Gestión de Personal</span>
                <span class="transform transition-transform group-open:rotate-180 text-slate-400">▼</span>
            </summary>
            <div class="p-6">
                <div class="overflow-x-auto relative z-10">
                    <table class="w-full text-center text-sm text-slate-300 border-collapse border border-slate-700/50">
                        <thead class="text-xs uppercase bg-slate-800 text-slate-400 tracking-wider">
                            <tr>
                                <th class="py-3 px-4 border border-slate-700/50">Nombre</th>
                                <th class="py-3 px-4 border border-slate-700/50">Email</th>
                                <th class="py-3 px-4 border border-slate-700/50">Rol</th>
                                <th class="py-3 px-4 border border-slate-700/50">Fecha de Registro</th>
                                <th class="py-3 px-4 border border-slate-700/50">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="font-medium bg-slate-900/50">
                            @foreach($empleados as $emp)
                            <tr class="hover:bg-slate-800/40 transition">
                                <td class="py-3 px-4 border border-slate-700/50 font-bold text-white">{{ $emp->name }}</td>
                                <td class="py-3 px-4 border border-slate-700/50">{{ $emp->email }}</td>
                                <td class="py-3 px-4 border border-slate-700/50">
                                    <form method="POST" action="{{ route('jefatura.actualizarRol', $emp->id) }}" class="flex items-center justify-center gap-2">
                                        @csrf
                                        @method('PUT')
                                        <select name="role" class="bg-slate-800 border border-slate-600 rounded text-xs p-1 text-white focus:outline-none focus:border-indigo-500 font-bold uppercase tracking-wider">
                                            <option value="operador" {{ $emp->role === 'operador' ? 'selected' : '' }}>Operador</option>
                                            <option value="quimico" {{ $emp->role === 'quimico' ? 'selected' : '' }}>Químico</option>
                                            <option value="laboratorio" {{ $emp->role === 'laboratorio' ? 'selected' : '' }}>Laboratorio</option>
                                            <option value="jefatura" {{ $emp->role === 'jefatura' ? 'selected' : '' }}>Jefatura</option>
                                        </select>
                                        <button type="submit" class="bg-indigo-600 hover:bg-indigo-500 text-white p-1.5 px-3 rounded transition shadow flex items-center" title="Guardar Rol">
                                            <span class="text-xs font-bold uppercase tracking-wider">Guardar</span>
                                        </button>
                                    </form>
                                </td>
                                <td class="py-3 px-4 border border-slate-700/50 text-xs font-mono text-slate-400">
                                    {{ $emp->created_at->format('d/m/Y') }}
                                </td>
                                <td class="py-3 px-4 border border-slate-700/50">
                                    @if($emp->id !== auth()->id())
                                    <form method="POST" action="{{ route('jefatura.darDeBaja', $emp->id) }}" onsubmit="return confirm('¿Dar de baja y eliminar a este empleado?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="bg-red-600 hover:bg-red-500 text-white font-bold py-1 px-3 rounded text-xs transition shadow flex items-center gap-2 mx-auto">
                                            <i class="fa-solid fa-user-xmark"></i> Dar de Baja
                                        </button>
                                    </form>
                                    @else
                                        <span class="text-slate-500 text-xs font-bold text-emerald-500">TÚ</span>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </details>

        <!-- Mensaje de Éxito Genérico -->
        @if(session('success'))
            <div class="bg-emerald-900/50 border border-emerald-500 text-emerald-200 px-4 py-3 rounded-xl mb-8 text-center font-semibold shadow-md">
                {{ session('success') }}
            </div>
        @endif

        <!-- KPI Cards -->
        @if($ultimaPresion)
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <div class="bg-slate-900/50 border border-slate-700 rounded-xl p-5 shadow-lg border-l-4 border-l-sky-400 flex flex-col justify-center items-center text-center transform transition duration-300 hover:scale-105 hover:bg-slate-800/80 cursor-default">
                <p class="text-slate-400 text-[11px] font-black tracking-widest uppercase mb-1">Últ. Presión Tanque</p>
                <p class="text-3xl font-black text-sky-400">{{ number_format($ultimaPresion->presion_tanque, 2) }} <span class="text-sm font-medium text-slate-500">MCA</span></p>
                <p class="text-[9px] text-sky-300 font-bold tracking-widest mt-3 uppercase truncate"><i class="fa-solid fa-user mr-1"></i> {{ optional($ultimaPresion->user)->name ?? 'SISTEMA' }}</p>
                <p class="text-[9px] text-slate-500 font-mono mt-1">{{ $ultimaPresion->created_at->format('d/m H:i') }}</p>
            </div>
            <div class="bg-slate-900/50 border border-slate-700 rounded-xl p-5 shadow-lg border-l-4 border-l-indigo-400 flex flex-col justify-center items-center text-center transform transition duration-300 hover:scale-105 hover:bg-slate-800/80 cursor-default">
                <p class="text-slate-400 text-[11px] font-black tracking-widest uppercase mb-1">Últ. Presión Planta</p>
                <p class="text-3xl font-black text-indigo-400">{{ number_format($ultimaPresion->presion_planta, 2) }} <span class="text-sm font-medium text-slate-500">MCA</span></p>
                <p class="text-[9px] text-indigo-300 font-bold tracking-widest mt-3 uppercase truncate"><i class="fa-solid fa-user mr-1"></i> {{ optional($ultimaPresion->user)->name ?? 'SISTEMA' }}</p>
                <p class="text-[9px] text-slate-500 font-mono mt-1">{{ $ultimaPresion->created_at->format('d/m H:i') }}</p>
            </div>
            <div class="bg-slate-900/50 border border-slate-700 rounded-xl p-5 shadow-lg border-l-4 border-l-purple-400 flex flex-col justify-center items-center text-center transform transition duration-300 hover:scale-105 hover:bg-slate-800/80 cursor-default">
                <p class="text-slate-400 text-[11px] font-black tracking-widest uppercase mb-1">Últ. Presión Falcón</p>
                <p class="text-3xl font-black text-purple-400">{{ number_format($ultimaPresion->presion_falcon, 2) }} <span class="text-sm font-medium text-slate-500">MCA</span></p>
                <p class="text-[9px] text-purple-300 font-bold tracking-widest mt-3 uppercase truncate"><i class="fa-solid fa-user mr-1"></i> {{ optional($ultimaPresion->user)->name ?? 'SISTEMA' }}</p>
                <p class="text-[9px] text-slate-500 font-mono mt-1">{{ $ultimaPresion->created_at->format('d/m H:i') }}</p>
            </div>
            <div class="bg-slate-900/50 border border-slate-700 rounded-xl p-5 shadow-lg border-l-4 border-l-emerald-400 flex flex-col justify-center items-center text-center transform transition duration-300 hover:scale-105 hover:bg-slate-800/80 cursor-default">
                <p class="text-slate-400 text-[11px] font-black tracking-widest uppercase mb-1">Últ. Nivel Cisterna</p>
                <p class="text-3xl font-black text-emerald-400">{{ number_format($ultimaPresion->nivel_cisterna, 2) }}%</p>
                <p class="text-[9px] text-emerald-300 font-bold tracking-widest mt-3 uppercase truncate"><i class="fa-solid fa-user mr-1"></i> {{ optional($ultimaPresion->user)->name ?? 'SISTEMA' }}</p>
                <p class="text-[9px] text-slate-500 font-mono mt-1">{{ $ultimaPresion->created_at->format('d/m H:i') }}</p>
            </div>
        </div>
        @endif

        <!-- KPI Cards Ultima Calidad por Lugar -->
        <h2 class="text-xl font-bold text-white tracking-wide mb-4 drop-shadow flex items-center gap-3"><i class="fa-solid fa-map-location-dot text-emerald-400"></i> Últimos Registros por Lugar (Calidad de Agua)</h2>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-5 gap-4 mb-8">
            @foreach($ultimosPorLugar as $lugar)
            <div class="bg-slate-900/50 border border-slate-700 rounded-xl p-4 shadow-lg border-l-4 border-l-emerald-400 hover:bg-slate-800/80 transition cursor-default">
                <p class="text-slate-300 text-[11px] font-black tracking-widest uppercase mb-1 line-clamp-1" title="{{ $lugar->lugar }} {{ $lugar->filtro_numero }}">{{ $lugar->lugar }} {{ $lugar->filtro_numero }}</p>
                <p class="text-[9px] text-emerald-300 font-bold tracking-widest mb-3 uppercase truncate"><i class="fa-solid fa-user mr-1"></i> {{ optional($lugar->user)->name ?? 'SISTEMA' }}</p>
                <div class="flex justify-between items-center text-sm border-b border-slate-700/50 pb-1 mb-1">
                    <span class="text-slate-500 text-[10px] font-black uppercase">Turbiedad</span>
                    <span class="text-amber-400 font-bold">{{ $lugar->turbiedad !== null ? $lugar->turbiedad : '-' }}</span>
                </div>
                <div class="flex justify-between items-center text-sm border-b border-slate-700/50 pb-1 mb-1">
                    <span class="text-slate-500 text-[10px] font-black uppercase">pH</span>
                    <span class="text-emerald-400 font-bold">{{ $lugar->ph !== null ? $lugar->ph : '-' }}</span>
                </div>
                <div class="flex justify-between items-center text-sm mb-2">
                    <span class="text-slate-500 text-[10px] font-black uppercase">Cloro</span>
                    <span class="text-rose-400 font-bold">{{ $lugar->cloro_residual !== null ? $lugar->cloro_residual : '-' }}</span>
                </div>
                <p class="text-[9px] text-slate-500 text-right mt-1 font-mono border-t border-slate-700/50 pt-1">{{ $lugar->created_at->format('d/m H:i') }}</p>
            </div>
            @endforeach
        </div>

        <!-- Panel Presiones -->
        <details class="bg-slate-900/40 rounded-xl border border-slate-700 mb-12 shadow-2xl group overflow-hidden">
            <summary class="list-none cursor-pointer bg-slate-800/80 p-6 flex justify-between items-center text-xl font-bold text-white tracking-wider hover:bg-slate-700/50 transition border-b border-slate-700">
                <span class="text-blue-400 uppercase"><i class="fa-solid fa-gauge-high mr-2"></i> Tendencia de Presiones y Cisterna</span>
                <span class="transform transition-transform group-open:rotate-180 text-slate-400">▼</span>
            </summary>
            <div class="p-8">
                <div class="chart-container">
                    <canvas id="chartPresiones"></canvas>
                </div>
            </div>
        </details>

        <!-- Calidad Agua separada en 3 (Efecto Decantación) -->
        <details class="bg-slate-900/40 rounded-xl border border-slate-700 mb-12 shadow-2xl group overflow-hidden">
            <summary class="list-none cursor-pointer bg-slate-800/80 p-6 flex justify-between items-center text-xl font-bold text-white tracking-wider hover:bg-slate-700/50 transition border-b border-slate-700">
                <span class="text-emerald-400 uppercase"><i class="fa-solid fa-microscope mr-2"></i> Calidad de Agua (Por Sector)</span>
                <span class="transform transition-transform group-open:rotate-180 text-slate-400">▼</span>
            </summary>
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 p-8">
            <!-- Turbiedad (Efecto Decantación) -->
            <div class="bg-slate-900/50 border border-slate-700 rounded-2xl p-6 shadow-xl transition hover:shadow-amber-900/20 lg:col-span-2">
                <div class="flex items-center gap-3 mb-4 border-b border-slate-700/50 pb-3">
                    <div class="bg-amber-500/20 p-2 rounded-lg text-amber-400">
                        <i class="fa-solid fa-water text-lg"></i>
                    </div>
                    <h3 class="text-xl font-bold text-white tracking-wide">Turbiedad</h3>
                </div>
                <div class="chart-container" style="height: 350px;">
                    <canvas id="chartTurbiedad"></canvas>
                </div>
            </div>
            
            <!-- Cloro -->
            <div class="bg-slate-900/50 border border-slate-700 rounded-2xl p-6 shadow-xl transition hover:shadow-rose-900/20 lg:col-span-2">
                <div class="flex items-center gap-3 mb-4 border-b border-slate-700/50 pb-3">
                    <div class="bg-rose-500/20 p-2 rounded-lg text-rose-400">
                        <i class="fa-solid fa-flask text-lg"></i>
                    </div>
                    <h3 class="text-xl font-bold text-white tracking-wide">Cloro Residual</h3>
                </div>
                <div class="chart-container" style="height: 300px;">
                    <canvas id="chartCloro"></canvas>
                </div>
            </div>

            <!-- pH -->
            <div class="bg-slate-900/50 border border-slate-700 rounded-2xl p-6 shadow-xl transition hover:shadow-emerald-900/20 lg:col-span-2">
                <div class="flex items-center gap-3 mb-4 border-b border-slate-700/50 pb-3">
                    <div class="bg-emerald-500/20 p-2 rounded-lg text-emerald-400">
                        <i class="fa-solid fa-vial-circle-check text-lg"></i>
                    </div>
                    <h3 class="text-xl font-bold text-white tracking-wide">pH</h3>
                </div>
                <div class="chart-container" style="height: 300px;">
                    <canvas id="chartPh"></canvas>
                </div>
            </div>
            </div>
        </details>

        <details class="bg-slate-900/40 rounded-xl border border-slate-700 mb-12 shadow-2xl group overflow-hidden">
            <summary class="list-none cursor-pointer bg-slate-800/80 p-6 flex justify-between items-center text-xl font-bold text-white tracking-wider hover:bg-slate-700/50 transition border-b border-slate-700">
                <span class="text-yellow-400 uppercase"><i class="fa-solid fa-vial mr-2"></i> Niveles de Químicos</span>
                <span class="transform transition-transform group-open:rotate-180 text-slate-400">▼</span>
            </summary>
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 p-8">
            
            <!-- Historial Quimicos -->
            <div class="bg-slate-900/50 border border-slate-700 rounded-2xl p-6 shadow-2xl transition hover:shadow-yellow-900/20">
                <div class="flex items-center gap-3 mb-6 border-b border-slate-700/50 pb-4">
                    <h2 class="text-xl font-bold text-white tracking-wide">Historial de Niveles (%)</h2>
                </div>
                <div class="chart-container">
                    <canvas id="chartHistorialQuimicos"></canvas>
                </div>
            </div>

            <!-- Panel Químicos Último -->
            <div class="bg-slate-900/50 border border-slate-700 rounded-2xl p-6 shadow-2xl transition hover:shadow-yellow-900/20">
                <div class="flex items-center gap-3 mb-6 border-b border-slate-700/50 pb-4">
                    <div class="bg-yellow-500/20 p-3 rounded-lg text-yellow-400">
                        <i class="fa-solid fa-vial text-xl"></i>
                    </div>
                    <h2 class="text-xl font-bold text-white tracking-wide">Últimos Niveles (%)</h2>
                </div>
                <div class="chart-container">
                    <canvas id="chartQuimicos"></canvas>
                </div>
            </div>
            </div>
        </details>

        <!-- Panel Filtros -->
        <details class="bg-slate-900/40 rounded-xl border border-slate-700 mb-12 shadow-2xl group overflow-hidden">
            <summary class="list-none cursor-pointer bg-slate-800/80 p-6 flex justify-between items-center text-xl font-bold text-white tracking-wider hover:bg-slate-700/50 transition border-b border-slate-700">
                <span class="text-indigo-400 uppercase"><i class="fa-solid fa-filter mr-2"></i> Lavados Frecuentes de Filtros</span>
                <span class="transform transition-transform group-open:rotate-180 text-slate-400">▼</span>
            </summary>
            <div class="p-8">
                <div class="chart-container flex justify-center">
                    <div class="w-[80%] h-full">
                        <canvas id="chartFiltros"></canvas>
                    </div>
                </div>
            </div>
        </details>
        <!-- SECCIÓN HISTÓRICOS Y TABLAS -->
        <details id="historicos" class="bg-slate-900/40 rounded-xl border border-slate-700 mb-12 shadow-2xl group overflow-hidden" {{ request()->hasAny(['calidad_page', 'presiones_page', 'calidad_fecha_inicio', 'presiones_fecha_inicio']) ? 'open' : '' }}>
            <summary class="list-none cursor-pointer bg-slate-800/80 p-6 flex justify-between items-center text-xl font-bold text-white tracking-wider hover:bg-slate-700/50 transition border-b border-slate-700">
                <span class="text-sky-400 uppercase"><i class="fa-solid fa-clock-rotate-left mr-2"></i> Consultas Históricas</span>
                <span class="transform transition-transform group-open:rotate-180 text-slate-400">▼</span>
            </summary>
            <div class="p-8">
                <div class="grid grid-cols-1 gap-8 mb-8">
                    <!-- Historial Calidad de Agua -->
                    <details class="bg-slate-900/50 border border-slate-700 rounded-2xl shadow-2xl transition hover:shadow-sky-900/20 group" {{ request()->has('calidad_page') || request()->has('calidad_fecha_inicio') ? 'open' : '' }}>
                        <summary class="list-none cursor-pointer bg-slate-800/50 p-4 flex justify-between items-center text-lg font-bold text-white tracking-wider hover:bg-slate-700/50 transition rounded-2xl">
                            <span class="text-sky-300 uppercase"><i class="fa-solid fa-microscope mr-2"></i> Historial: Calidad de Agua</span>
                            <span class="transform transition-transform group-open:rotate-180 text-slate-400">▼</span>
                        </summary>
                        <div class="p-6 border-t border-slate-700">
                            <!-- Filtro Calidad -->
                            <form action="{{ route('jefatura.index') }}#historicos" method="GET" class="flex flex-col md:flex-row items-end gap-6 mb-6">
                                <div class="w-full md:w-auto flex-1">
                                    <label for="calidad_fecha_inicio" class="block text-slate-400 text-xs font-bold mb-2 tracking-wide uppercase">Fecha Inicio</label>
                                    <input type="date" id="calidad_fecha_inicio" name="calidad_fecha_inicio" value="{{ $calidadFechaInicio }}" class="w-full bg-slate-800 border border-slate-600 rounded p-2 text-white focus:outline-none focus:border-sky-500 font-mono">
                                </div>
                                <div class="w-full md:w-auto flex-1">
                                    <label for="calidad_fecha_fin" class="block text-slate-400 text-xs font-bold mb-2 tracking-wide uppercase">Fecha Fin</label>
                                    <input type="date" id="calidad_fecha_fin" name="calidad_fecha_fin" value="{{ $calidadFechaFin }}" class="w-full bg-slate-800 border border-slate-600 rounded p-2 text-white focus:outline-none focus:border-sky-500 font-mono">
                                </div>
                                <div class="w-full md:w-auto flex-1">
                                    <label for="lugar" class="block text-slate-400 text-xs font-bold mb-2 tracking-wide uppercase">Lugar</label>
                                    <select id="lugar" name="lugar" class="w-full bg-slate-800 border border-slate-600 rounded p-2 text-white focus:outline-none focus:border-sky-500 font-mono">
                                        <option value="">Todos</option>
                                        <option value="RIO" {{ request('lugar') == 'RIO' ? 'selected' : '' }}>Río</option>
                                        <option value="DECANTADOR NORTE" {{ request('lugar') == 'DECANTADOR NORTE' ? 'selected' : '' }}>Decantador Norte</option>
                                        <option value="DECANTADOR SUR" {{ request('lugar') == 'DECANTADOR SUR' ? 'selected' : '' }}>Decantador Sur</option>
                                        <option value="CISTERNA" {{ request('lugar') == 'CISTERNA' ? 'selected' : '' }}>Cisterna</option>
                                        <option value="FILTRO LINEA NORTE" {{ request('lugar') == 'FILTRO LINEA NORTE' ? 'selected' : '' }}>Filtros Norte</option>
                                        <option value="FILTRO LINEA SUR" {{ request('lugar') == 'FILTRO LINEA SUR' ? 'selected' : '' }}>Filtros Sur</option>
                                    </select>
                                </div>
                                <div class="flex gap-3 w-full md:w-auto">
                                    <button type="submit" class="bg-sky-600 hover:bg-sky-500 text-white py-2 px-6 rounded border border-sky-400 transition font-bold tracking-wide w-full md:w-auto flex-1 text-center justify-center flex items-center gap-2">
                                        <i class="fa-solid fa-filter"></i> Filtrar
                                    </button>
                                    <a href="{{ route('jefatura.index') }}#historicos" class="bg-slate-700 hover:bg-slate-600 text-white py-2 px-6 rounded border border-slate-500 transition font-bold tracking-wide text-center flex items-center gap-2">
                                        <i class="fa-solid fa-rotate-left"></i> Limpiar
                                    </a>
                                </div>
                                @if(request()->has('presiones_fecha_inicio'))
                                    <input type="hidden" name="presiones_fecha_inicio" value="{{ request('presiones_fecha_inicio') }}">
                                @endif
                                @if(request()->has('presiones_fecha_fin'))
                                    <input type="hidden" name="presiones_fecha_fin" value="{{ request('presiones_fecha_fin') }}">
                                @endif
                                @if(request()->has('presiones_page'))
                                    <input type="hidden" name="presiones_page" value="{{ request('presiones_page') }}">
                                @endif
                            </form>
                            <div class="overflow-x-auto rounded-xl border border-slate-700">
                                <table class="w-full text-left border-collapse">
                                    <thead>
                                        <tr class="bg-slate-800 text-slate-300 text-xs uppercase tracking-wider">
                                            <th class="p-3 border-b border-slate-700">Fecha/Hora</th>
                                            <th class="p-3 border-b border-slate-700">Lugar</th>
                                            <th class="p-3 border-b border-slate-700 text-center">Filtro Nº</th>
                                            <th class="p-3 border-b border-slate-700 text-center">Turbiedad</th>
                                            <th class="p-3 border-b border-slate-700 text-center">pH</th>
                                            <th class="p-3 border-b border-slate-700 text-center">Cloro</th>
                                            <th class="p-3 border-b border-slate-700 text-right">Operador</th>
                                        </tr>
                                    </thead>
                                    <tbody class="text-sm">
                                        @forelse($historialCalidad as $reg)
                                        <tr class="border-b border-slate-800 hover:bg-slate-800/50 transition">
                                            <td class="p-3 text-slate-400 font-mono">{{ $reg->created_at->format('d/m/Y H:i') }}</td>
                                            <td class="p-3 font-semibold text-slate-200">{{ $reg->lugar }}</td>
                                            <td class="p-3 text-center text-slate-400">{{ $reg->filtro_numero ?? '-' }}</td>
                                            <td class="p-3 text-center text-amber-300">{{ $reg->turbiedad ?? '-' }}</td>
                                            <td class="p-3 text-center text-emerald-300">{{ $reg->ph ?? '-' }}</td>
                                            <td class="p-3 text-center text-rose-300">{{ $reg->cloro_residual ?? '-' }}</td>
                                            <td class="p-3 text-right text-slate-500 text-xs"><i class="fa-solid fa-user mr-1"></i> {{ optional($reg->user)->name ?? 'Sistema' }}</td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="7" class="p-6 text-center text-slate-500">No hay registros para este periodo.</td>
                                        </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                            <div class="mt-4">
                                <div class="flex justify-center items-center space-x-2 text-sm font-bold text-slate-300">
                                    @if ($historialCalidad->onFirstPage())
                                        <button disabled class="bg-slate-800 px-3 py-1 rounded border border-slate-600 transition opacity-50 cursor-not-allowed">&lt;</button>
                                    @else
                                        <a href="{{ $historialCalidad->previousPageUrl() }}#historicos" class="bg-slate-800 hover:bg-slate-700 px-3 py-1 rounded border border-slate-600 transition">&lt;</a>
                                    @endif
                                    
                                    <span class="px-4 text-sky-400 font-mono">Página {{ $historialCalidad->currentPage() }} / {{ $historialCalidad->lastPage() }}</span>
                                    
                                    @if ($historialCalidad->hasMorePages())
                                        <a href="{{ $historialCalidad->nextPageUrl() }}#historicos" class="bg-slate-800 hover:bg-slate-700 px-3 py-1 rounded border border-slate-600 transition">&gt;</a>
                                    @else
                                        <button disabled class="bg-slate-800 px-3 py-1 rounded border border-slate-600 transition opacity-50 cursor-not-allowed">&gt;</button>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </details>

                    <!-- Historial Presiones -->
                    <details class="bg-slate-900/50 border border-slate-700 rounded-2xl shadow-2xl transition hover:shadow-indigo-900/20 group" {{ request()->has('presiones_page') || request()->has('presiones_fecha_inicio') ? 'open' : '' }}>
                        <summary class="list-none cursor-pointer bg-slate-800/50 p-4 flex justify-between items-center text-lg font-bold text-white tracking-wider hover:bg-slate-700/50 transition rounded-2xl">
                            <span class="text-indigo-300 uppercase"><i class="fa-solid fa-gauge mr-2"></i> Historial: Presiones y Cisterna</span>
                            <span class="transform transition-transform group-open:rotate-180 text-slate-400">▼</span>
                        </summary>
                        <div class="p-6 border-t border-slate-700">
                            <!-- Filtro Presiones -->
                            <form action="{{ route('jefatura.index') }}#historicos" method="GET" class="flex flex-col md:flex-row items-end gap-6 mb-6">
                                <div class="w-full md:w-auto flex-1">
                                    <label for="presiones_fecha_inicio" class="block text-slate-400 text-xs font-bold mb-2 tracking-wide uppercase">Fecha Inicio</label>
                                    <input type="date" id="presiones_fecha_inicio" name="presiones_fecha_inicio" value="{{ $presionesFechaInicio }}" class="w-full bg-slate-800 border border-slate-600 rounded p-2 text-white focus:outline-none focus:border-indigo-500 font-mono">
                                </div>
                                <div class="w-full md:w-auto flex-1">
                                    <label for="presiones_fecha_fin" class="block text-slate-400 text-xs font-bold mb-2 tracking-wide uppercase">Fecha Fin</label>
                                    <input type="date" id="presiones_fecha_fin" name="presiones_fecha_fin" value="{{ $presionesFechaFin }}" class="w-full bg-slate-800 border border-slate-600 rounded p-2 text-white focus:outline-none focus:border-indigo-500 font-mono">
                                </div>
                                <div class="flex gap-3 w-full md:w-auto">
                                    <button type="submit" class="bg-indigo-600 hover:bg-indigo-500 text-white py-2 px-6 rounded border border-indigo-400 transition font-bold tracking-wide w-full md:w-auto flex-1 text-center justify-center flex items-center gap-2">
                                        <i class="fa-solid fa-filter"></i> Filtrar
                                    </button>
                                    <a href="{{ route('jefatura.index') }}#historicos" class="bg-slate-700 hover:bg-slate-600 text-white py-2 px-6 rounded border border-slate-500 transition font-bold tracking-wide text-center flex items-center gap-2">
                                        <i class="fa-solid fa-rotate-left"></i> Limpiar
                                    </a>
                                </div>
                                @if(request()->has('calidad_fecha_inicio'))
                                    <input type="hidden" name="calidad_fecha_inicio" value="{{ request('calidad_fecha_inicio') }}">
                                @endif
                                @if(request()->has('calidad_fecha_fin'))
                                    <input type="hidden" name="calidad_fecha_fin" value="{{ request('calidad_fecha_fin') }}">
                                @endif
                                @if(request()->has('lugar'))
                                    <input type="hidden" name="lugar" value="{{ request('lugar') }}">
                                @endif
                                @if(request()->has('calidad_page'))
                                    <input type="hidden" name="calidad_page" value="{{ request('calidad_page') }}">
                                @endif
                            </form>
                            <div class="overflow-x-auto rounded-xl border border-slate-700">
                                <table class="w-full text-left border-collapse">
                                    <thead>
                                        <tr class="bg-slate-800 text-slate-300 text-xs uppercase tracking-wider">
                                            <th class="p-3 border-b border-slate-700">Fecha/Hora</th>
                                            <th class="p-3 border-b border-slate-700 text-center">Tanque</th>
                                            <th class="p-3 border-b border-slate-700 text-center">Planta</th>
                                            <th class="p-3 border-b border-slate-700 text-center">Falcón</th>
                                            <th class="p-3 border-b border-slate-700 text-center">Nivel Cisterna</th>
                                            <th class="p-3 border-b border-slate-700 text-right">Operador</th>
                                        </tr>
                                    </thead>
                                    <tbody class="text-sm">
                                        @forelse($historialPresiones as $reg)
                                        <tr class="border-b border-slate-800 hover:bg-slate-800/50 transition">
                                            <td class="p-3 text-slate-400 font-mono">{{ $reg->created_at->format('d/m/Y H:i') }}</td>
                                            <td class="p-3 text-center text-sky-300">{{ $reg->presion_tanque ?? '-' }} MCA</td>
                                            <td class="p-3 text-center text-indigo-300">{{ $reg->presion_planta ?? '-' }} MCA</td>
                                            <td class="p-3 text-center text-purple-300">{{ $reg->presion_falcon ?? '-' }} MCA</td>
                                            <td class="p-3 text-center text-emerald-300">{{ $reg->nivel_cisterna ?? '-' }}%</td>
                                            <td class="p-3 text-right text-slate-500 text-xs"><i class="fa-solid fa-user mr-1"></i> {{ optional($reg->user)->name ?? 'Sistema' }}</td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="6" class="p-6 text-center text-slate-500">No hay registros para este periodo.</td>
                                        </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                            <div class="mt-4">
                                <div class="flex justify-center items-center space-x-2 text-sm font-bold text-slate-300">
                                    @if ($historialPresiones->onFirstPage())
                                        <button disabled class="bg-slate-800 px-3 py-1 rounded border border-slate-600 transition opacity-50 cursor-not-allowed">&lt;</button>
                                    @else
                                        <a href="{{ $historialPresiones->previousPageUrl() }}#historicos" class="bg-slate-800 hover:bg-slate-700 px-3 py-1 rounded border border-slate-600 transition">&lt;</a>
                                    @endif
                                    
                                    <span class="px-4 text-sky-400 font-mono">Página {{ $historialPresiones->currentPage() }} / {{ $historialPresiones->lastPage() }}</span>
                                    
                                    @if ($historialPresiones->hasMorePages())
                                        <a href="{{ $historialPresiones->nextPageUrl() }}#historicos" class="bg-slate-800 hover:bg-slate-700 px-3 py-1 rounded border border-slate-600 transition">&gt;</a>
                                    @else
                                        <button disabled class="bg-slate-800 px-3 py-1 rounded border border-slate-600 transition opacity-50 cursor-not-allowed">&gt;</button>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </details>
                </div>
            </div>
        </details>
    </div>

    <!-- Data Injection for Chart.js -->
    <script>
        // Data from Controller
        const presionesData = @json($presiones);
        const calidadData = @json($calidadAgua);
        const quimicosData = @json($nivelesQuimicos);
        const historialQuimicosData = @json($historialQuimicos);
        const filtrosData = @json($conteoFiltros);

        // Chart Config Defaults (Dark mode)
        Chart.defaults.color = '#94a3b8'; // text-slate-400
        Chart.defaults.font.family = 'ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif';
        Chart.defaults.scale.grid.color = 'rgba(148, 163, 184, 0.1)';

        // 1. Chart Presiones (Line)
        const labelsPresiones = presionesData.map(p => {
            const date = new Date(p.created_at);
            return date.toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'});
        });
        const ctxPresiones = document.getElementById('chartPresiones').getContext('2d');
        new Chart(ctxPresiones, {
            type: 'line',
            data: {
                labels: labelsPresiones,
                datasets: [
                    {
                        label: 'Tanque',
                        data: presionesData.map(p => p.presion_tanque),
                        borderColor: '#38bdf8', // sky-400
                        backgroundColor: 'rgba(56, 189, 248, 0.1)',
                        tension: 0.4,
                        borderWidth: 2,
                        pointRadius: 2
                    },
                    {
                        label: 'Planta',
                        data: presionesData.map(p => p.presion_planta),
                        borderColor: '#818cf8', // indigo-400
                        backgroundColor: 'rgba(129, 140, 248, 0.1)',
                        tension: 0.4,
                        borderWidth: 2,
                        pointRadius: 2
                    },
                    {
                        label: 'Falcón',
                        data: presionesData.map(p => p.presion_falcon),
                        borderColor: '#c084fc', // purple-400
                        backgroundColor: 'rgba(192, 132, 252, 0.1)',
                        tension: 0.4,
                        borderWidth: 2,
                        pointRadius: 2,
                        yAxisID: 'y'
                    },
                    {
                        label: 'Cisterna (%)',
                        data: presionesData.map(p => p.nivel_cisterna),
                        borderColor: '#34d399', // emerald-400
                        backgroundColor: 'rgba(52, 211, 153, 0.1)',
                        tension: 0.4,
                        borderWidth: 2,
                        pointRadius: 2,
                        borderDash: [5, 5],
                        yAxisID: 'y1'
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: { mode: 'index', intersect: false },
                plugins: {
                    legend: { position: 'top', labels: { usePointStyle: true, padding: 20 } },
                    tooltip: { backgroundColor: 'rgba(15, 23, 42, 0.9)', titleColor: '#fff', padding: 12 }
                },
                scales: {
                    y: { 
                        beginAtZero: false,
                        type: 'linear',
                        display: true,
                        position: 'left',
                    },
                    y1: {
                        beginAtZero: true,
                        max: 100,
                        type: 'linear',
                        display: true,
                        position: 'right',
                        grid: {
                            drawOnChartArea: false, // only want the grid lines for one axis to show up
                        },
                    }
                }
            }
        });

        // Calidad Agua Charts (Por Sector)
        let labelsCalidad = calidadData.map(c => new Date(c.created_at).toLocaleTimeString([], {day:'2-digit', month:'2-digit', hour: '2-digit', minute:'2-digit'}));
        labelsCalidad = [...new Set(labelsCalidad)]; // Unique labels

        const lugares = [...new Set(calidadData.filter(c => !c.lugar.includes('FILTRO')).map(c => c.lugar + (c.filtro_numero ? ' ' + c.filtro_numero : '')))];
        const colorsCalidad = ['#fcd34d', '#38bdf8', '#818cf8', '#10b981', '#f472b6', '#a78bfa', '#fb923c', '#4ade80', '#c084fc', '#f87171'];

        function buildDatasets(field) {
            return lugares.map((lugar, index) => {
                const color = colorsCalidad[index % colorsCalidad.length];
                const dataPoints = calidadData
                    .filter(c => (c.lugar + (c.filtro_numero ? ' ' + c.filtro_numero : '')) === lugar && c[field] !== null)
                    .map(c => ({
                        x: new Date(c.created_at).toLocaleTimeString([], {day:'2-digit', month:'2-digit', hour: '2-digit', minute:'2-digit'}),
                        y: c[field]
                    }));
                
                return {
                    label: lugar,
                    data: dataPoints,
                    borderColor: color,
                    backgroundColor: color,
                    borderWidth: 2,
                    tension: 0.3,
                    spanGaps: true,
                    pointRadius: 3
                };
            });
        }

        // Turbiedad Chart
        const ctxTurbiedad = document.getElementById('chartTurbiedad').getContext('2d');
        new Chart(ctxTurbiedad, {
            type: 'line',
            data: { labels: labelsCalidad, datasets: buildDatasets('turbiedad') },
            options: {
                responsive: true, maintainAspectRatio: false,
                plugins: { legend: { position: 'top', labels: { usePointStyle: true, boxWidth: 10, padding: 15, font: {size: 11} } } },
                scales: { y: { beginAtZero: true } }
            }
        });

        // pH Chart
        const ctxPh = document.getElementById('chartPh').getContext('2d');
        new Chart(ctxPh, {
            type: 'line',
            data: { labels: labelsCalidad, datasets: buildDatasets('ph') },
            options: {
                responsive: true, maintainAspectRatio: false,
                plugins: { legend: { position: 'top', labels: { usePointStyle: true, boxWidth: 10, padding: 15, font: {size: 11} } } },
                scales: { y: { suggestedMin: 6, suggestedMax: 8 } }
            }
        });

        // Cloro Chart
        const ctxCloro = document.getElementById('chartCloro').getContext('2d');
        const cloroDatasets = buildDatasets('cloro_residual').filter(ds => ds.label === 'CISTERNA');
        new Chart(ctxCloro, {
            type: 'line',
            data: { labels: labelsCalidad, datasets: cloroDatasets },
            options: {
                responsive: true, maintainAspectRatio: false,
                plugins: { legend: { position: 'top', labels: { usePointStyle: true, boxWidth: 10, padding: 15, font: {size: 11} } } },
                scales: { y: { beginAtZero: true } }
            }
        });

        // Historial Químicos Chart
        // Agrupamos Cloro, Poliamina, Sulfato (solo tanques principales para limpieza)
        const histCloro = historialQuimicosData.filter(q => q.quimico === 'cloro' && q.tipo_tanque === 'principal').map(q => ({x: new Date(q.created_at).toLocaleTimeString([], {day:'2-digit', month:'2-digit', hour: '2-digit', minute:'2-digit'}), y: q.nivel}));
        const histPoli = historialQuimicosData.filter(q => q.quimico === 'poliamina' && q.tipo_tanque === 'principal').map(q => ({x: new Date(q.created_at).toLocaleTimeString([], {day:'2-digit', month:'2-digit', hour: '2-digit', minute:'2-digit'}), y: q.nivel}));
        const histSulfato = historialQuimicosData.filter(q => q.quimico === 'sulfato' && q.tipo_tanque === 'principal').map(q => ({x: new Date(q.created_at).toLocaleTimeString([], {day:'2-digit', month:'2-digit', hour: '2-digit', minute:'2-digit'}), y: q.nivel}));
        
        let histLabels = historialQuimicosData.map(q => new Date(q.created_at).toLocaleTimeString([], {day:'2-digit', month:'2-digit', hour: '2-digit', minute:'2-digit'}));
        histLabels = [...new Set(histLabels)];
        
        const ctxHistQuimicos = document.getElementById('chartHistorialQuimicos').getContext('2d');
        new Chart(ctxHistQuimicos, {
            type: 'line',
            data: {
                labels: histLabels,
                datasets: [
                    { label: 'Cloro (Principal)', data: histCloro, borderColor: '#eab308', backgroundColor: 'rgba(234, 179, 8, 0.2)', borderWidth: 2, tension: 0.3 },
                    { label: 'Poliamina (Principal)', data: histPoli, borderColor: '#10b981', backgroundColor: 'rgba(16, 185, 129, 0.2)', borderWidth: 2, tension: 0.3 },
                    { label: 'Sulfato (Principal)', data: histSulfato, borderColor: '#ef4444', backgroundColor: 'rgba(239, 68, 68, 0.2)', borderWidth: 2, tension: 0.3 }
                ]
            },
            options: {
                responsive: true, maintainAspectRatio: false,
                plugins: { legend: { position: 'top', labels: { usePointStyle: true } } },
                scales: { y: { beginAtZero: true, max: 100 } }
            }
        });

        // 3. Chart Quimicos (Bar)
        // Group by chemical type to show main and aux tank if we had that structure
        // Since NivelQuimico currently just has 'nivel' in some models or 'tanque_principal'/'tanque_auxiliar' in others?
        // Let's adapt based on the data. The JefaturaController gets unique 'quimico' row.
        // Wait, looking at the Operador controller earlier, they save 'tanque_principal' and 'tanque_auxiliar' but NivelQuimico model didn't show it?
        // Ah, if they saved it as JSON or separate rows. Let's just plot what's available.
        // I will assume $nivelesQuimicos has 'quimico', 'tanque_principal' and 'tanque_auxiliar'.
        const labelsQuimicos = quimicosData.map(q => q.quimico.toUpperCase());
        const dataPrincipal = quimicosData.map(q => q.tanque_principal || q.nivel || 0); // fallback to 'nivel' if 'tanque_principal' isn't there
        const dataAuxiliar = quimicosData.map(q => q.tanque_auxiliar || 0);

        const ctxQuimicos = document.getElementById('chartQuimicos').getContext('2d');
        new Chart(ctxQuimicos, {
            type: 'bar',
            data: {
                labels: labelsQuimicos,
                datasets: [
                    {
                        label: 'Tanque Principal (%)',
                        data: dataPrincipal,
                        backgroundColor: 'rgba(251, 191, 36, 0.8)', // amber-400
                        borderRadius: 6
                    },
                    {
                        label: 'Tanque Auxiliar (%)',
                        data: dataAuxiliar,
                        backgroundColor: 'rgba(14, 165, 233, 0.8)', // sky-500
                        borderRadius: 6
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'top', labels: { usePointStyle: true } }
                },
                scales: {
                    y: { beginAtZero: true, max: 100 }
                }
            }
        });

        // 4. Chart Filtros (Doughnut)
        const labelsFiltros = Object.keys(filtrosData);
        const dataFiltros = Object.values(filtrosData);
        
        const ctxFiltros = document.getElementById('chartFiltros').getContext('2d');
        new Chart(ctxFiltros, {
            type: 'doughnut',
            data: {
                labels: labelsFiltros,
                datasets: [{
                    data: dataFiltros,
                    backgroundColor: [
                        '#3b82f6', // blue-500
                        '#8b5cf6', // violet-500
                        '#ec4899', // pink-500
                        '#10b981', // emerald-500
                        '#f59e0b', // amber-500
                        '#ef4444'  // red-500
                    ],
                    borderWidth: 0,
                    hoverOffset: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '70%',
                plugins: {
                    legend: { position: 'right', labels: { usePointStyle: true, padding: 15 } }
                }
            }
        });
        // Fix Chart.js size when opening details
        document.querySelectorAll('details').forEach(details => {
            details.addEventListener('toggle', function() {
                if (this.open) {
                    // Small timeout to allow DOM to update before chart resizes
                    setTimeout(() => window.dispatchEvent(new Event('resize')), 10);
                }
            });
        });
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
