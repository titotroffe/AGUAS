<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Jefatura</title>
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <style>
        .glass-panel {
            background: rgba(30, 41, 59, 0.7);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }
        
        .chart-container {
            position: relative;
            height: 300px;
            width: 100%;
        }
    </style>
</head>
<body class="bg-slate-900 text-slate-200 font-sans min-h-screen p-4 md:p-8" style="background-image: radial-gradient(circle at top right, #1e293b, #0f172a);">

    <div class="max-w-7xl mx-auto">
        <!-- Header -->
        <div class="flex flex-col md:flex-row md:items-center md:justify-center mb-12 relative gap-6">
            <div class="md:absolute md:left-0 md:top-1/2 md:-translate-y-1/2 flex justify-center">
                <a href="/menu" class="bg-slate-800/80 hover:bg-slate-700 text-white py-2 px-6 rounded-full border border-slate-600 transition shadow-lg text-sm font-bold flex items-center gap-2 group">
                    <i class="fa-solid fa-arrow-left group-hover:-translate-x-1 transition-transform"></i> VOLVER AL MENÚ
                </a>
            </div>
            <h1 class="text-3xl md:text-4xl font-black text-transparent bg-clip-text bg-gradient-to-r from-cyan-400 to-blue-500 tracking-wider text-center m-0 w-full drop-shadow-sm uppercase">
                Panel de Jefatura
            </h1>
        </div>

        @php
            $ultimaPresion = $presiones->last();
        @endphp

        <!-- KPI Cards -->
        @if($ultimaPresion)
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <div class="glass-panel rounded-xl p-5 shadow-lg border-l-4 border-l-sky-400 flex flex-col justify-center items-center text-center transform transition duration-300 hover:scale-105 hover:bg-slate-800/80 cursor-default">
                <p class="text-slate-400 text-[11px] font-black tracking-widest uppercase mb-1">Últ. Presión Tanque</p>
                <p class="text-3xl font-black text-sky-400">{{ number_format($ultimaPresion->presion_tanque, 2) }} <span class="text-sm font-medium text-slate-500">MCA</span></p>
            </div>
            <div class="glass-panel rounded-xl p-5 shadow-lg border-l-4 border-l-indigo-400 flex flex-col justify-center items-center text-center transform transition duration-300 hover:scale-105 hover:bg-slate-800/80 cursor-default">
                <p class="text-slate-400 text-[11px] font-black tracking-widest uppercase mb-1">Últ. Presión Planta</p>
                <p class="text-3xl font-black text-indigo-400">{{ number_format($ultimaPresion->presion_planta, 2) }} <span class="text-sm font-medium text-slate-500">MCA</span></p>
            </div>
            <div class="glass-panel rounded-xl p-5 shadow-lg border-l-4 border-l-purple-400 flex flex-col justify-center items-center text-center transform transition duration-300 hover:scale-105 hover:bg-slate-800/80 cursor-default">
                <p class="text-slate-400 text-[11px] font-black tracking-widest uppercase mb-1">Últ. Presión Falcón</p>
                <p class="text-3xl font-black text-purple-400">{{ number_format($ultimaPresion->presion_falcon, 2) }} <span class="text-sm font-medium text-slate-500">MCA</span></p>
            </div>
            <div class="glass-panel rounded-xl p-5 shadow-lg border-l-4 border-l-emerald-400 flex flex-col justify-center items-center text-center transform transition duration-300 hover:scale-105 hover:bg-slate-800/80 cursor-default">
                <p class="text-slate-400 text-[11px] font-black tracking-widest uppercase mb-1">Últ. Nivel Cisterna</p>
                <p class="text-3xl font-black text-emerald-400">{{ number_format($ultimaPresion->nivel_cisterna, 2) }}%</p>
            </div>
        </div>
        @endif

        <!-- KPI Cards Ultima Calidad por Lugar -->
        <h2 class="text-xl font-bold text-white tracking-wide mb-4 drop-shadow flex items-center gap-3"><i class="fa-solid fa-map-location-dot text-emerald-400"></i> Últimos Registros por Lugar (Calidad de Agua)</h2>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 xl:grid-cols-6 gap-4 mb-8">
            @foreach($ultimosPorLugar as $lugar)
            <div class="glass-panel rounded-xl p-4 shadow-lg border-l-4 border-l-emerald-400 hover:bg-slate-800/80 transition cursor-default">
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

        <div class="grid grid-cols-1 lg:grid-cols-1 gap-8 mb-8">
            <!-- Panel Presiones -->
            <div class="glass-panel rounded-2xl p-6 shadow-2xl transition hover:shadow-cyan-900/20">
                <div class="flex items-center gap-3 mb-6 border-b border-slate-700/50 pb-4">
                    <div class="bg-blue-500/20 p-3 rounded-lg text-blue-400">
                        <i class="fa-solid fa-gauge-high text-xl"></i>
                    </div>
                    <h2 class="text-xl font-bold text-white tracking-wide">Tendencia de Presiones y Cisterna</h2>
                </div>
                <div class="chart-container">
                    <canvas id="chartPresiones"></canvas>
                </div>
            </div>
        </div>

        <!-- Calidad Agua separada en 3 (Efecto Decantación) -->
        <h2 class="text-2xl font-bold text-white tracking-wide mb-6 mt-8 drop-shadow flex items-center gap-3"><i class="fa-solid fa-microscope text-emerald-400"></i> Calidad de Agua (Por Sector)</h2>
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
            <!-- Turbiedad (Efecto Decantación) -->
            <div class="glass-panel rounded-2xl p-6 shadow-xl transition hover:shadow-amber-900/20 lg:col-span-2">
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
            
            <!-- pH -->
            <div class="glass-panel rounded-2xl p-6 shadow-xl transition hover:shadow-emerald-900/20">
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

            <!-- Cloro -->
            <div class="glass-panel rounded-2xl p-6 shadow-xl transition hover:shadow-rose-900/20">
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
        </div>

        <h2 class="text-2xl font-bold text-white tracking-wide mb-6 mt-8 drop-shadow flex items-center gap-3"><i class="fa-solid fa-vial text-yellow-400"></i> Niveles de Químicos</h2>
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
            
            <!-- Historial Quimicos -->
            <div class="glass-panel rounded-2xl p-6 shadow-2xl transition hover:shadow-yellow-900/20">
                <div class="flex items-center gap-3 mb-6 border-b border-slate-700/50 pb-4">
                    <h2 class="text-xl font-bold text-white tracking-wide">Historial de Niveles (%)</h2>
                </div>
                <div class="chart-container">
                    <canvas id="chartHistorialQuimicos"></canvas>
                </div>
            </div>

            <!-- Panel Químicos Último -->
            <div class="glass-panel rounded-2xl p-6 shadow-2xl transition hover:shadow-yellow-900/20">
                <div class="flex items-center gap-3 mb-6 border-b border-slate-700/50 pb-4">
                    <div class="bg-yellow-500/20 p-3 rounded-lg text-yellow-400">
                        <i class="fa-solid fa-vial text-xl"></i>
                    </div>
                    <h2 class="text-xl font-bold text-white tracking-wide">Últimos Niveles Químicos (%)</h2>
                </div>
                <div class="chart-container">
                    <canvas id="chartQuimicos"></canvas>
                </div>
            </div>

            <!-- Panel Filtros -->
            <div class="glass-panel rounded-2xl p-6 shadow-2xl transition hover:shadow-indigo-900/20">
                <div class="flex items-center gap-3 mb-6 border-b border-slate-700/50 pb-4">
                    <div class="bg-indigo-500/20 p-3 rounded-lg text-indigo-400">
                        <i class="fa-solid fa-filter text-xl"></i>
                    </div>
                    <h2 class="text-xl font-bold text-white tracking-wide">Lavados Frecuentes de Filtros</h2>
                </div>
                <div class="chart-container flex justify-center">
                    <div class="w-[80%] h-full">
                        <canvas id="chartFiltros"></canvas>
                    </div>
                </div>
            </div>

        </div>
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

        const lugares = [...new Set(calidadData.map(c => c.lugar + (c.filtro_numero ? ' ' + c.filtro_numero : '')))];
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
                plugins: { legend: { position: 'right', labels: { usePointStyle: true, boxWidth: 10, padding: 15, font: {size: 11} } } },
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
                plugins: { legend: { position: 'right', labels: { usePointStyle: true, boxWidth: 10, padding: 15, font: {size: 11} } } },
                scales: { y: { suggestedMin: 6, suggestedMax: 8 } }
            }
        });

        // Cloro Chart
        const ctxCloro = document.getElementById('chartCloro').getContext('2d');
        new Chart(ctxCloro, {
            type: 'line',
            data: { labels: labelsCalidad, datasets: buildDatasets('cloro_residual') },
            options: {
                responsive: true, maintainAspectRatio: false,
                plugins: { legend: { position: 'right', labels: { usePointStyle: true, boxWidth: 10, padding: 15, font: {size: 11} } } },
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

    </script>
</body>
</html>
