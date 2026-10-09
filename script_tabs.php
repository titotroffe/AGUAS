<?php
$f = 'c:\laragon\www\AGUAS\resources\views\laboratorio\index.blade.php';
$c = file_get_contents($f);

// 1. Insert x-data and tabs after the errors div
$tabsHtml = <<<'EOD'
        @php
            $defaultTab = 'insumos';
            if (session('success_tratamiento') || session('error_tratamiento')) $defaultTab = 'tratamiento';
            elseif (session('success_producto') || session('error_producto')) $defaultTab = 'producto';
            elseif (session('success_red') || session('error_red')) $defaultTab = 'red';
            elseif (session('success_pozos') || session('error_pozos')) $defaultTab = 'pozos';
            elseif (session('success_escriturado') || session('error_escriturado')) $defaultTab = 'escriturado';
            elseif (session('success_escuelas') || session('error_escuelas')) $defaultTab = 'escuelas';
            elseif (session('success_novedades') || session('error_novedades')) $defaultTab = 'novedades';
        @endphp

        <div x-data="{ activeTab: '{{ $defaultTab }}' }">
            <!-- Menú Superior de Tabs -->
            <div class="flex flex-wrap md:flex-nowrap gap-2 border-b border-slate-700/50 mb-8 overflow-x-auto scrollbar-hide">
                <button @click="activeTab = 'insumos'" :class="activeTab === 'insumos' ? 'bg-blue-600/20 text-blue-400 border-b-2 border-blue-500 shadow-[0_-10px_20px_-10px_rgba(59,130,246,0.3)]' : 'text-slate-400 hover:bg-slate-800 hover:text-slate-200 border-b-2 border-transparent'" class="px-5 py-4 font-bold text-xs tracking-wider whitespace-nowrap transition-all duration-300 rounded-t-xl flex-grow md:flex-grow-0 flex items-center justify-center gap-2">1. INSUMOS</button>
                <button @click="activeTab = 'tratamiento'" :class="activeTab === 'tratamiento' ? 'bg-blue-600/20 text-blue-400 border-b-2 border-blue-500 shadow-[0_-10px_20px_-10px_rgba(59,130,246,0.3)]' : 'text-slate-400 hover:bg-slate-800 hover:text-slate-200 border-b-2 border-transparent'" class="px-5 py-4 font-bold text-xs tracking-wider whitespace-nowrap transition-all duration-300 rounded-t-xl flex-grow md:flex-grow-0 flex items-center justify-center gap-2">2. TRATAMIENTO</button>
                <button @click="activeTab = 'producto'" :class="activeTab === 'producto' ? 'bg-blue-600/20 text-blue-400 border-b-2 border-blue-500 shadow-[0_-10px_20px_-10px_rgba(59,130,246,0.3)]' : 'text-slate-400 hover:bg-slate-800 hover:text-slate-200 border-b-2 border-transparent'" class="px-5 py-4 font-bold text-xs tracking-wider whitespace-nowrap transition-all duration-300 rounded-t-xl flex-grow md:flex-grow-0 flex items-center justify-center gap-2">3. PRODUCTO</button>
                <button @click="activeTab = 'red'" :class="activeTab === 'red' ? 'bg-blue-600/20 text-blue-400 border-b-2 border-blue-500 shadow-[0_-10px_20px_-10px_rgba(59,130,246,0.3)]' : 'text-slate-400 hover:bg-slate-800 hover:text-slate-200 border-b-2 border-transparent'" class="px-5 py-4 font-bold text-xs tracking-wider whitespace-nowrap transition-all duration-300 rounded-t-xl flex-grow md:flex-grow-0 flex items-center justify-center gap-2">4. RED</button>
                <button @click="activeTab = 'pozos'" :class="activeTab === 'pozos' ? 'bg-blue-600/20 text-blue-400 border-b-2 border-blue-500 shadow-[0_-10px_20px_-10px_rgba(59,130,246,0.3)]' : 'text-slate-400 hover:bg-slate-800 hover:text-slate-200 border-b-2 border-transparent'" class="px-5 py-4 font-bold text-xs tracking-wider whitespace-nowrap transition-all duration-300 rounded-t-xl flex-grow md:flex-grow-0 flex items-center justify-center gap-2">5. POZOS</button>
                <button @click="activeTab = 'escriturado'" :class="activeTab === 'escriturado' ? 'bg-blue-600/20 text-blue-400 border-b-2 border-blue-500 shadow-[0_-10px_20px_-10px_rgba(59,130,246,0.3)]' : 'text-slate-400 hover:bg-slate-800 hover:text-slate-200 border-b-2 border-transparent'" class="px-5 py-4 font-bold text-xs tracking-wider whitespace-nowrap transition-all duration-300 rounded-t-xl flex-grow md:flex-grow-0 flex items-center justify-center gap-2">6. ESCRITURADO</button>
                <button @click="activeTab = 'escuelas'" :class="activeTab === 'escuelas' ? 'bg-blue-600/20 text-blue-400 border-b-2 border-blue-500 shadow-[0_-10px_20px_-10px_rgba(59,130,246,0.3)]' : 'text-slate-400 hover:bg-slate-800 hover:text-slate-200 border-b-2 border-transparent'" class="px-5 py-4 font-bold text-xs tracking-wider whitespace-nowrap transition-all duration-300 rounded-t-xl flex-grow md:flex-grow-0 flex items-center justify-center gap-2">7. ESCUELAS</button>
                <button @click="activeTab = 'novedades'" :class="activeTab === 'novedades' ? 'bg-blue-600/20 text-blue-400 border-b-2 border-blue-500 shadow-[0_-10px_20px_-10px_rgba(59,130,246,0.3)]' : 'text-slate-400 hover:bg-slate-800 hover:text-slate-200 border-b-2 border-transparent'" class="px-5 py-4 font-bold text-xs tracking-wider whitespace-nowrap transition-all duration-300 rounded-t-xl flex-grow md:flex-grow-0 flex items-center justify-center gap-2">8. NOVEDADES</button>
            </div>

EOD;
$c = preg_replace('/(<\/ul>\s*<\/div>\s*@endif\s*)\s*<!-- 1\. ANÁLISIS DE INSUMOS -->/', '$1' . "\n" . $tabsHtml . "\n        <!-- 1. ANÁLISIS DE INSUMOS -->", $c);

// Replace closing divs
$c = preg_replace('/<\/details>\s*<!-- Formularios ocultos/', "    </div>\n        </div> <!-- End of x-data tabs -->\n\n    <!-- Formularios ocultos", $c);
$c = str_replace("</details>", "    </div>", $c);

$mapping = [
    'details-insumos' => 'insumos',
    'details-tratamiento' => 'tratamiento',
    'details-producto' => 'producto',
    'details-red' => 'red',
    'details-pozos' => 'pozos',
    'details-escriturado' => 'escriturado',
    'details-escuelas' => 'escuelas',
    'novedades-details' => 'novedades',
];

foreach ($mapping as $id => $tabName) {
    // Matches the details opening and summary, up to the <div class="p-4 md:p-8">
    $pattern = '/<details\s+id="' . preg_quote($id) . '"[^>]*>.*?<summary[^>]*>.*?<span[^>]*>(.*?)<\/span>.*?<\/summary>/s';
    
    $replacement = '
            <div x-show="activeTab === \'' . $tabName . '\'" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0" style="display: none;">
                <div class="bg-slate-900/40 rounded-xl border border-slate-700 mb-12 shadow-2xl overflow-hidden glass">
                    <div class="bg-slate-800/80 p-6 flex justify-between items-center text-xl font-bold text-white tracking-wider border-b border-slate-700">
                        <span class="text-blue-400">$1</span>
                    </div>';
    
    $c = preg_replace($pattern, $replacement, $c);
}

file_put_contents($f, $c);
echo "Done";
