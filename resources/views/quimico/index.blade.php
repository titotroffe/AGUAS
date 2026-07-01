<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Monitoreo de Calidad de Agua</title>
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    <style>
        input[type=number]::-webkit-inner-spin-button, 
        input[type=number]::-webkit-outer-spin-button { 
            -webkit-appearance: none; 
            margin: 0; 
        }
        input[type=number] {
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
    </style>
</head>
<body class="bg-slate-800 text-slate-200 font-sans min-h-screen p-8">

    <div class="max-w-6xl mx-auto">
        
        <!-- Navegación y Título -->
        <div class="relative flex items-center justify-center mb-12">
            <a href="/menu" class="absolute left-0 top-1/2 -translate-y-1/2 bg-slate-700 hover:bg-slate-600 text-white py-2 px-6 rounded border border-slate-500 transition text-sm font-semibold">
                 ← VOLVER AL MENÚ
            </a>
            <h1 class="text-2xl font-bold text-white tracking-wider text-center m-0">MONITOREO DE CALIDAD DE AGUA</h1>
        </div>

        <!-- Alertas -->
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

        <details id="details-calidad" class="bg-slate-900/40 rounded-xl border border-slate-700 mb-12 shadow-2xl group overflow-hidden">
            <summary class="list-none cursor-pointer bg-slate-800/80 p-6 flex justify-between items-center text-xl font-bold text-white tracking-wider hover:bg-slate-700/50 transition border-b border-slate-700">
                <span class="text-blue-400">1. MONITOREO DE CALIDAD</span>
                <span class="transform transition-transform group-open:rotate-180 text-slate-400">▼</span>
            </summary>
            <div class="p-8">
                <form action="{{ route('quimico.storeCalidad') }}" method="POST" onsubmit="const btns = this.querySelectorAll('button[type=submit]'); btns.forEach(b => { b.disabled = true; b.innerHTML = 'GUARDANDO...'; b.classList.add('opacity-50', 'cursor-not-allowed'); });">
                                    <!-- Grid de Mediciones por Lugar -->
                    <div class="grid grid-cols-6 gap-8 mb-8 text-center items-start">
                    
                        <!-- Decantador Norte -->
                        <div class="flex flex-col items-center">
                            <label class="text-xs font-bold mb-4 tracking-wide text-slate-400 uppercase text-center w-full">DECANTADOR NORTE</label>
                            
                            <label class="text-[10px] font-bold mb-2 tracking-wide text-slate-400">TURBIEDAD (NTU)</label>
                            <input type="number" name="decantador_norte_turbiedad" step="0.01" min="0" value="{{ old('decantador_norte_turbiedad') }}" class="w-24 bg-slate-900 border border-slate-600 rounded p-2 text-center text-white focus:outline-none focus:border-blue-500 font-mono mb-4" placeholder="0.00">
                            
                            <label class="text-[10px] font-bold mb-2 tracking-wide text-slate-400">pH</label>
                            <input type="number" name="decantador_norte_ph" step="0.01" min="0" max="14" value="{{ old('decantador_norte_ph') }}" class="w-24 bg-slate-900 border border-slate-600 rounded p-2 text-center text-white focus:outline-none focus:border-blue-500 font-mono mb-4" placeholder="0.00">
                        </div>

                        <!-- Decantador Sur -->
                        <div class="flex flex-col items-center">
                            <label class="text-xs font-bold mb-4 tracking-wide text-slate-400 uppercase text-center w-full">DECANTADOR SUR</label>
                            
                            <label class="text-[10px] font-bold mb-2 tracking-wide text-slate-400">TURBIEDAD (NTU)</label>
                            <input type="number" name="decantador_sur_turbiedad" step="0.01" min="0" value="{{ old('decantador_sur_turbiedad') }}" class="w-24 bg-slate-900 border border-slate-600 rounded p-2 text-center text-white focus:outline-none focus:border-blue-500 font-mono mb-4" placeholder="0.00">
                            
                            <label class="text-[10px] font-bold mb-2 tracking-wide text-slate-400">pH</label>
                            <input type="number" name="decantador_sur_ph" step="0.01" min="0" max="14" value="{{ old('decantador_sur_ph') }}" class="w-24 bg-slate-900 border border-slate-600 rounded p-2 text-center text-white focus:outline-none focus:border-blue-500 font-mono mb-4" placeholder="0.00">
                        </div>

                        <!-- Cisterna -->
                        <div class="flex flex-col items-center">
                            <label class="text-xs font-bold mb-4 tracking-wide text-slate-400 uppercase text-center w-full">CISTERNA</label>
                            
                            <label class="text-[10px] font-bold mb-2 tracking-wide text-slate-400">TURBIEDAD (NTU)</label>
                            <input type="number" name="cisterna_turbiedad" step="0.01" min="0" value="{{ old('cisterna_turbiedad') }}" class="w-24 bg-slate-900 border border-slate-600 rounded p-2 text-center text-white focus:outline-none focus:border-blue-500 font-mono mb-4" placeholder="0.00">
                            
                            <label class="text-[10px] font-bold mb-2 tracking-wide text-slate-400">pH</label>
                            <input type="number" name="cisterna_ph" step="0.01" min="0" max="14" value="{{ old('cisterna_ph') }}" class="w-24 bg-slate-900 border border-slate-600 rounded p-2 text-center text-white focus:outline-none focus:border-blue-500 font-mono mb-4" placeholder="0.00">
                            
                            <label class="text-[10px] font-bold mb-2 tracking-wide text-slate-400">CLORO RESIDUAL</label>
                            <input type="number" name="cisterna_cloro" step="0.01" min="0" value="{{ old('cisterna_cloro') }}" class="w-24 bg-slate-900 border border-slate-600 rounded p-2 text-center text-white focus:outline-none focus:border-blue-500 font-mono mb-4" placeholder="0.00">
                        </div>

                        <!-- Río -->
                        <div class="flex flex-col items-center">
                            <label class="text-xs font-bold mb-4 tracking-wide text-slate-400 uppercase text-center w-full">RÍO</label>
                            
                            <label class="text-[10px] font-bold mb-2 tracking-wide text-slate-400">TURBIEDAD (NTU)</label>
                            <input type="number" name="rio_turbiedad" step="0.01" min="0" value="{{ old('rio_turbiedad') }}" class="w-24 bg-slate-900 border border-slate-600 rounded p-2 text-center text-white focus:outline-none focus:border-blue-500 font-mono mb-4" placeholder="0.00">
                            
                            <label class="text-[10px] font-bold mb-2 tracking-wide text-slate-400">pH</label>
                            <input type="number" name="rio_ph" step="0.01" min="0" max="14" value="{{ old('rio_ph') }}" class="w-24 bg-slate-900 border border-slate-600 rounded p-2 text-center text-white focus:outline-none focus:border-blue-500 font-mono mb-4" placeholder="0.00">
                        </div>

                        <!-- Filtro Línea Norte -->
                        <div class="flex flex-col items-center">
                            <label class="text-xs font-bold mb-4 tracking-wide text-slate-400 uppercase text-center w-full">FILTRO LÍNEA NORTE</label>
                            
                            <label class="text-[10px] font-bold mb-2 tracking-wide text-slate-400">SELECCIÓN</label>
                            <select name="filtro_norte_select" class="w-24 bg-slate-900 border border-slate-600 rounded p-2 text-center focus:outline-none focus:border-blue-500 text-[10px] font-bold tracking-wide text-slate-400 uppercase mb-4">
                                <option value="">Elegir</option>
                                <option value="Filtro 1" {{ old('filtro_norte_select') == 'Filtro 1' ? 'selected' : '' }}>Filtro 1</option>
                                <option value="Filtro 2" {{ old('filtro_norte_select') == 'Filtro 2' ? 'selected' : '' }}>Filtro 2</option>
                                <option value="Filtro 3" {{ old('filtro_norte_select') == 'Filtro 3' ? 'selected' : '' }}>Filtro 3</option>
                            </select>

                            <label class="text-[10px] font-bold mb-2 tracking-wide text-slate-400">TURBIEDAD (NTU)</label>
                            <input type="number" name="filtro_norte_turbiedad" step="0.01" min="0" value="{{ old('filtro_norte_turbiedad') }}" class="w-24 bg-slate-900 border border-slate-600 rounded p-2 text-center text-white focus:outline-none focus:border-blue-500 font-mono mb-4" placeholder="0.00">
                            
                            <label class="text-[10px] font-bold mb-2 tracking-wide text-slate-400">pH</label>
                            <input type="number" name="filtro_norte_ph" step="0.01" min="0" max="14" value="{{ old('filtro_norte_ph') }}" class="w-24 bg-slate-900 border border-slate-600 rounded p-2 text-center text-white focus:outline-none focus:border-blue-500 font-mono mb-4" placeholder="0.00">
                        </div>

                        <!-- Filtro Línea Sur -->
                        <div class="flex flex-col items-center">
                            <label class="text-xs font-bold mb-4 tracking-wide text-slate-400 uppercase text-center w-full">FILTRO LÍNEA SUR</label>
                            
                            <label class="text-[10px] font-bold mb-2 tracking-wide text-slate-400">SELECCIÓN</label>
                            <select name="filtro_sur_select" class="w-24 bg-slate-900 border border-slate-600 rounded p-2 text-center focus:outline-none focus:border-blue-500 text-[10px] font-bold tracking-wide text-slate-400 uppercase mb-4">
                                <option value="">Elegir</option>
                                <option value="Filtro 1" {{ old('filtro_sur_select') == 'Filtro 1' ? 'selected' : '' }}>Filtro 1</option>
                                <option value="Filtro 2" {{ old('filtro_sur_select') == 'Filtro 2' ? 'selected' : '' }}>Filtro 2</option>
                                <option value="Filtro 3" {{ old('filtro_sur_select') == 'Filtro 3' ? 'selected' : '' }}>Filtro 3</option>
                            </select>

                            <label class="text-[10px] font-bold mb-2 tracking-wide text-slate-400">TURBIEDAD (NTU)</label>
                            <input type="number" name="filtro_sur_turbiedad" step="0.01" min="0" value="{{ old('filtro_sur_turbiedad') }}" class="w-24 bg-slate-900 border border-slate-600 rounded p-2 text-center text-white focus:outline-none focus:border-blue-500 font-mono mb-4" placeholder="0.00">
                            
                            <label class="text-[10px] font-bold mb-2 tracking-wide text-slate-400">pH</label>
                            <input type="number" name="filtro_sur_ph" step="0.01" min="0" max="14" value="{{ old('filtro_sur_ph') }}" class="w-24 bg-slate-900 border border-slate-600 rounded p-2 text-center text-white focus:outline-none focus:border-blue-500 font-mono mb-4" placeholder="0.00">
                        </div>
                    </div>

                <!-- Botón Confirmar -->
                <div class="flex justify-center mb-4">
                    <button type="submit" class="bg-blue-600 hover:bg-blue-500 text-white font-bold py-2 px-12 rounded shadow-lg transition tracking-wide text-sm">
                        CONFIRMAR CALIDAD
                    </button>
                </div>
            </form>

        <!-- Tabla: Registros de Calidad -->
        <div class="mt-8 mb-16 bg-slate-900/50 rounded-xl border border-slate-700 p-6 shadow-2xl">
            <h2 class="text-xl font-bold text-white text-center mb-6 tracking-wider uppercase">
                REGISTROS DE CALIDAD
            </h2>
            <div class="overflow-x-auto">
                <table class="w-full text-center text-sm text-slate-300 border-collapse border border-slate-700">
                    <thead class="text-xs uppercase bg-slate-800 text-slate-400 tracking-wider">
                        <tr>
                            <th scope="col" class="py-3 px-4 border border-slate-700">Fecha y Hora</th>
                            <th scope="col" class="py-3 px-4 border border-slate-700">Químico</th>
                            <th scope="col" class="py-3 px-4 border border-slate-700">Lugar</th>
                            <th scope="col" class="py-3 px-4 border border-slate-700">Turbiedad N.T.U.</th>
                            <th scope="col" class="py-3 px-4 border border-slate-700">pH</th>
                            <th scope="col" class="py-3 px-4 border border-slate-700">Cloro Residual</th>
                            <th scope="col" class="py-3 px-4 border border-slate-700">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="font-medium">
                        @forelse($ultimosRegistros as $index => $registro)
                            <tr class="hover:bg-slate-800/40 transition calidad-row" data-index="{{ $index }}" style="{{ $index >= 8 ? 'display:none;' : '' }}">
                                <!-- Fecha y Hora -->
                                <td class="py-4 px-4 font-mono text-slate-400 border border-slate-700">
                                    {{ $registro->created_at->format('d/m/Y H:i') }}
                                </td>
                                <!-- Químico -->
                                <td class="py-4 px-4 text-white font-semibold border border-slate-700">
                                    {{ $registro->user->name ?? 'N/A' }}
                                </td>
                                <!-- Lugar -->
                                <td class="py-4 px-4 text-white font-semibold border border-slate-700 text-center">
                                    {{ $registro->lugar }}
                                    @if($registro->filtro_numero)
                                        <span class="text-xs text-blue-400 font-normal">({{ $registro->filtro_numero }})</span>
                                    @endif
                                </td>
                                <!-- Turbiedad -->
                                <td class="py-4 px-4 text-center font-mono text-blue-400 border border-slate-700">
                                    {{ $registro->turbiedad !== null ? number_format($registro->turbiedad, 2) : '-' }}
                                </td>
                                <!-- pH -->
                                <td class="py-4 px-4 text-center font-mono text-blue-400 border border-slate-700">
                                    {{ $registro->ph !== null ? number_format($registro->ph, 2) : '-' }}
                                </td>
                                <!-- Cloro Residual -->
                                <td class="py-4 px-4 text-center font-mono text-emerald-400 border border-slate-700 font-bold">
                                    {{ $registro->cloro_residual !== null ? number_format($registro->cloro_residual, 2) : '-' }}
                                </td>
                                <!-- Acciones -->
                                <td class="py-4 px-4 text-center border border-slate-700">
                                    @if(auth()->id() == $registro->user_id && $registro->created_at->gt(now()->subHours(2)))
                                        <button type="button" 
                                                onclick="if(confirm('¿Seguro que deseas borrar este registro de calidad?')) { 
                                                    const form = document.getElementById('delete-calidad-form'); 
                                                    form.action = '{{ route('quimico.destroyCalidad', $registro->id) }}'; 
                                                    form.submit(); 
                                                }"
                                                class="bg-red-600/85 hover:bg-red-600 text-white py-1 px-3 rounded text-xs font-bold transition shadow-sm mx-auto">
                                            Borrar
                                        </button>
                                    @else
                                        <span class="text-slate-500 text-xs">-</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="py-8 text-center text-slate-500 font-semibold border border-slate-700">
                                    No hay registros de calidad de agua cargados todavía.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            @if(count($ultimosRegistros) > 8)
            <div class="flex justify-center items-center mt-6 space-x-2 text-sm font-bold text-slate-300" id="calidad-pagination">
                <button type="button" onclick="changePage(-1)" class="bg-slate-800 hover:bg-slate-700 px-3 py-1 rounded border border-slate-600 transition">&lt;</button>
                <span id="page-indicator" class="px-4 text-blue-400 font-mono">Página 1 / {{ ceil(count($ultimosRegistros) / 8) }}</span>
                <button type="button" onclick="changePage(1)" class="bg-slate-800 hover:bg-slate-700 px-3 py-1 rounded border border-slate-600 transition">&gt;</button>
            </div>
            @endif
        </div>
        </div>
        </details>

        <!-- Sección: Novedades del Turno -->
        <details id="novedades-details" class="bg-slate-900/40 rounded-xl border border-slate-700 mb-12 shadow-2xl group overflow-hidden">
            <summary class="list-none cursor-pointer bg-slate-800/80 p-6 flex justify-between items-center text-xl font-bold text-white tracking-wider hover:bg-slate-700/50 transition border-b border-slate-700">
                <span class="text-blue-400">2. NOVEDADES Y COMENTARIOS DEL TURNO</span>
                <span class="transform transition-transform group-open:rotate-180 text-slate-400">▼</span>
            </summary>
            <div class="p-8">
                <form action="{{ route('quimico.storeNovedad') }}" method="POST" class="mb-12" onsubmit="const btns = this.querySelectorAll('button[type=submit]'); btns.forEach(b => { b.disabled = true; b.innerHTML = 'GUARDANDO...'; b.classList.add('opacity-50', 'cursor-not-allowed'); });">
                    @csrf
                    <label class="block text-sm font-bold text-slate-300 mb-3 tracking-wide">REGISTRAR NUEVA NOVEDAD (Máx. 1000 caracteres)</label>
                    <textarea name="mensaje" rows="3" class="w-full bg-slate-900 border border-slate-600 rounded p-4 text-white focus:outline-none focus:border-blue-500 mb-4 resize-none" placeholder="Escribe aquí cualquier comentario importante o novedad del turno para que el próximo químico o personal esté enterado..." required></textarea>
                    
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
                    <form action="{{ route('quimico.marcarLeidas') }}" method="POST" onsubmit="const btn = this.querySelector('button'); btn.disabled = true; btn.innerHTML = 'MARCANDO...'; btn.classList.add('opacity-50', 'cursor-not-allowed');">
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
                                                    form.action = '{{ route('quimico.destroyNovedad', $novedad->id) }}'; 
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
    <form id="delete-calidad-form" method="POST" style="display: none;">
        @csrf
        @method('DELETE')
    </form>
    <form id="delete-novedad-form" method="POST" style="display: none;">
        @csrf
        @method('DELETE')
    </form>

    <script>
        // Restaurar estado de los bloques (abiertos o cerrados) usando sessionStorage
        document.addEventListener('DOMContentLoaded', () => {
            const detailsElements = document.querySelectorAll('details');
            detailsElements.forEach(detail => {
                if (!detail.id) return;
                // Si la alerta de novedades abrió el detalle, tiene prioridad
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
        });

        // Paginación en Frontend para Tabla de Calidad
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
            const rows = document.querySelectorAll('.calidad-row');
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
