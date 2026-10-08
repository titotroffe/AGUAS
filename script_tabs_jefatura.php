<?php
$f = 'c:\laragon\www\AGUAS\resources\views\jefatura\index.blade.php';
$c = file_get_contents($f);

// Determine default tab based on request parameters
$tabsHtml = <<<'EOD'
        @php
            $defaultTab = 'personal';
            if (request()->hasAny(['calidad_page', 'presiones_page', 'calidad_fecha_inicio', 'presiones_fecha_inicio', 'tab'])) {
                $defaultTab = 'historicos';
            }
        @endphp

        <div x-data="{ activeTab: '{{ $defaultTab }}' }">
            <!-- Menú Superior de Tabs -->
            <div class="flex flex-wrap gap-2 border-b border-slate-700/50 mb-8 overflow-x-auto scrollbar-hide p-2">
                <button @click="activeTab = 'personal'" :class="activeTab === 'personal' ? 'bg-indigo-600/20 text-indigo-400 border-b-2 border-indigo-500 shadow-[0_-10px_20px_-10px_rgba(99,102,241,0.3)]' : 'text-slate-400 hover:bg-slate-800 hover:text-slate-200 border-b-2 border-transparent'" class="px-4 py-3 font-bold text-xs tracking-wider whitespace-nowrap transition-all duration-300 rounded-t-xl flex items-center gap-2">
                    <i class="fa-solid fa-users"></i> PERSONAL
                </button>
                <button @click="activeTab = 'tendencias'" :class="activeTab === 'tendencias' ? 'bg-blue-600/20 text-blue-400 border-b-2 border-blue-500 shadow-[0_-10px_20px_-10px_rgba(59,130,246,0.3)]' : 'text-slate-400 hover:bg-slate-800 hover:text-slate-200 border-b-2 border-transparent'" class="px-4 py-3 font-bold text-xs tracking-wider whitespace-nowrap transition-all duration-300 rounded-t-xl flex items-center gap-2">
                    <i class="fa-solid fa-chart-line"></i> TENDENCIAS
                </button>
                <button @click="activeTab = 'calidad'" :class="activeTab === 'calidad' ? 'bg-emerald-600/20 text-emerald-400 border-b-2 border-emerald-500 shadow-[0_-10px_20px_-10px_rgba(16,185,129,0.3)]' : 'text-slate-400 hover:bg-slate-800 hover:text-slate-200 border-b-2 border-transparent'" class="px-4 py-3 font-bold text-xs tracking-wider whitespace-nowrap transition-all duration-300 rounded-t-xl flex items-center gap-2">
                    <i class="fa-solid fa-microscope"></i> CALIDAD
                </button>
                <button @click="activeTab = 'quimicos'" :class="activeTab === 'quimicos' ? 'bg-yellow-600/20 text-yellow-400 border-b-2 border-yellow-500 shadow-[0_-10px_20px_-10px_rgba(234,179,8,0.3)]' : 'text-slate-400 hover:bg-slate-800 hover:text-slate-200 border-b-2 border-transparent'" class="px-4 py-3 font-bold text-xs tracking-wider whitespace-nowrap transition-all duration-300 rounded-t-xl flex items-center gap-2">
                    <i class="fa-solid fa-vial"></i> QUÍMICOS
                </button>
                <button @click="activeTab = 'filtros'" :class="activeTab === 'filtros' ? 'bg-indigo-600/20 text-indigo-400 border-b-2 border-indigo-500 shadow-[0_-10px_20px_-10px_rgba(99,102,241,0.3)]' : 'text-slate-400 hover:bg-slate-800 hover:text-slate-200 border-b-2 border-transparent'" class="px-4 py-3 font-bold text-xs tracking-wider whitespace-nowrap transition-all duration-300 rounded-t-xl flex items-center gap-2">
                    <i class="fa-solid fa-filter"></i> FILTROS
                </button>
                <button @click="activeTab = 'historicos'" :class="activeTab === 'historicos' ? 'bg-sky-600/20 text-sky-400 border-b-2 border-sky-500 shadow-[0_-10px_20px_-10px_rgba(14,165,233,0.3)]' : 'text-slate-400 hover:bg-slate-800 hover:text-slate-200 border-b-2 border-transparent'" class="px-4 py-3 font-bold text-xs tracking-wider whitespace-nowrap transition-all duration-300 rounded-t-xl flex items-center gap-2">
                    <i class="fa-solid fa-clock-rotate-left"></i> HISTÓRICOS
                </button>
            </div>

EOD;

$c = preg_replace('/(<!-- Panel Gestión de Personal -->)/', $tabsHtml . "\n        $1", $c);
$c = preg_replace('/(<\/div>\s*<\/details>\s*</div>\s*</div>\s*<\/div>\s*<\/details>)/', "$1\n        </div> <!-- End of x-data tabs -->", $c);

// Arrays of patterns and replacements
$mappings = [
    [
        'tab' => 'personal',
        'pattern' => '/<details[^>]*>\s*<summary[^>]*>.*?<span class="text-indigo-400 uppercase">Gestión de Personal<\/span>.*?<\/summary>/s',
        'replacement' => '
            <div x-show="activeTab === \'personal\'" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0" style="display: none;">
                <div class="bg-slate-900/40 rounded-xl border border-slate-700 mb-12 shadow-2xl overflow-hidden glass">
                    <div class="bg-slate-800/80 p-6 flex justify-between items-center text-xl font-bold text-white tracking-wider border-b border-slate-700">
                        <span class="text-indigo-400 uppercase">Gestión de Personal</span>
                    </div>',
    ],
    [
        'tab' => 'tendencias',
        'pattern' => '/<details[^>]*>\s*<summary[^>]*>.*?<span class="text-blue-400 uppercase"><i class="fa-solid fa-gauge-high mr-2"><\/i> Tendencia de Presiones y Cisterna<\/span>.*?<\/summary>/s',
        'replacement' => '
            <div x-show="activeTab === \'tendencias\'" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0" style="display: none;">
                <div class="bg-slate-900/40 rounded-xl border border-slate-700 mb-12 shadow-2xl overflow-hidden glass">
                    <div class="bg-slate-800/80 p-6 flex justify-between items-center text-xl font-bold text-white tracking-wider border-b border-slate-700">
                        <span class="text-blue-400 uppercase"><i class="fa-solid fa-gauge-high mr-2"></i> Tendencia de Presiones y Cisterna</span>
                    </div>',
    ],
    [
        'tab' => 'calidad',
        'pattern' => '/<details[^>]*>\s*<summary[^>]*>.*?<span class="text-emerald-400 uppercase"><i class="fa-solid fa-microscope mr-2"><\/i> Calidad de Agua \(Por Sector\)<\/span>.*?<\/summary>/s',
        'replacement' => '
            <div x-show="activeTab === \'calidad\'" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0" style="display: none;">
                <div class="bg-slate-900/40 rounded-xl border border-slate-700 mb-12 shadow-2xl overflow-hidden glass">
                    <div class="bg-slate-800/80 p-6 flex justify-between items-center text-xl font-bold text-white tracking-wider border-b border-slate-700">
                        <span class="text-emerald-400 uppercase"><i class="fa-solid fa-microscope mr-2"></i> Calidad de Agua (Por Sector)</span>
                    </div>',
    ],
    [
        'tab' => 'quimicos',
        'pattern' => '/<details[^>]*>\s*<summary[^>]*>.*?<span class="text-yellow-400 uppercase"><i class="fa-solid fa-vial mr-2"><\/i> Niveles de Químicos<\/span>.*?<\/summary>/s',
        'replacement' => '
            <div x-show="activeTab === \'quimicos\'" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0" style="display: none;">
                <div class="bg-slate-900/40 rounded-xl border border-slate-700 mb-12 shadow-2xl overflow-hidden glass">
                    <div class="bg-slate-800/80 p-6 flex justify-between items-center text-xl font-bold text-white tracking-wider border-b border-slate-700">
                        <span class="text-yellow-400 uppercase"><i class="fa-solid fa-vial mr-2"></i> Niveles de Químicos</span>
                    </div>',
    ],
    [
        'tab' => 'filtros',
        'pattern' => '/<details[^>]*>\s*<summary[^>]*>.*?<span class="text-indigo-400 uppercase"><i class="fa-solid fa-filter mr-2"><\/i> Lavados Frecuentes de Filtros<\/span>.*?<\/summary>/s',
        'replacement' => '
            <div x-show="activeTab === \'filtros\'" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0" style="display: none;">
                <div class="bg-slate-900/40 rounded-xl border border-slate-700 mb-12 shadow-2xl overflow-hidden glass">
                    <div class="bg-slate-800/80 p-6 flex justify-between items-center text-xl font-bold text-white tracking-wider border-b border-slate-700">
                        <span class="text-indigo-400 uppercase"><i class="fa-solid fa-filter mr-2"></i> Lavados Frecuentes de Filtros</span>
                    </div>',
    ],
    [
        'tab' => 'historicos',
        'pattern' => '/<details[^>]*id="historicos"[^>]*>\s*<summary[^>]*>.*?<span class="text-sky-400 uppercase"><i class="fa-solid fa-clock-rotate-left mr-2"><\/i> Consultas Históricas<\/span>.*?<\/summary>/s',
        'replacement' => '
            <div x-show="activeTab === \'historicos\'" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0" style="display: none;">
                <div class="bg-slate-900/40 rounded-xl border border-slate-700 mb-12 shadow-2xl overflow-hidden glass">
                    <div class="bg-slate-800/80 p-6 flex justify-between items-center text-xl font-bold text-white tracking-wider border-b border-slate-700">
                        <span class="text-sky-400 uppercase"><i class="fa-solid fa-clock-rotate-left mr-2"></i> Consultas Históricas</span>
                    </div>',
    ],
];

foreach ($mappings as $mapping) {
    $c = preg_replace($mapping['pattern'], $mapping['replacement'], $c);
}

// Replace the closing </details> for each section
$c = preg_replace('/<\/details>\s*<!-- Panel Presiones -->/', "    </div>\n            </div>\n\n        <!-- Panel Presiones -->", $c);
$c = preg_replace('/<\/details>\s*<!-- Calidad Agua separada/', "    </div>\n            </div>\n\n        <!-- Calidad Agua separada", $c);
$c = preg_replace('/<\/details>\s*<details class="bg-slate-900\/40/', "    </div>\n            </div>\n\n        <details class=\"bg-slate-900/40", $c);
$c = preg_replace('/<\/details>\s*<!-- Panel Filtros -->/', "    </div>\n            </div>\n\n        <!-- Panel Filtros -->", $c);
$c = preg_replace('/<\/details>\s*<!-- SECCIÓN HISTÓRICOS Y TABLAS -->/', "    </div>\n            </div>\n\n        <!-- SECCIÓN HISTÓRICOS Y TABLAS -->", $c);
// The last one is at the very end
$c = preg_replace('/<\/div>\s*<\/details>\s*<\/div>\s*<!-- End of x-data tabs -->/', "</div>\n    </div>\n            </div>\n\n        </div> <!-- End of x-data tabs -->", $c);

file_put_contents($f, $c);
echo "Done";
