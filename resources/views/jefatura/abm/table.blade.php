<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ABM: {{ strtoupper($table) }}</title>
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>
<body class="bg-slate-800 text-slate-200 font-sans min-h-screen p-8">
    <div class="max-w-7xl mx-auto">
        <!-- Navegación y Título -->
        <div class="relative flex items-center justify-center mb-8">
            <a href="{{ route('jefatura.abm.index') }}" class="absolute left-0 top-1/2 -translate-y-1/2 bg-slate-700 hover:bg-slate-600 text-white py-2 px-6 rounded border border-slate-500 transition text-sm font-semibold">
                 ← VOLVER AL LISTADO
            </a>
            <h1 class="text-2xl font-bold text-white tracking-wider text-center m-0">GESTIÓN DE: <span class="text-blue-400">{{ strtoupper($table) }}</span></h1>
            
            <div class="absolute right-0 top-1/2 -translate-y-1/2 flex gap-3">
                <button onclick="openColumnModal()" class="bg-indigo-600 hover:bg-indigo-500 text-white py-2 px-4 rounded border border-indigo-500 transition text-sm font-bold shadow-lg">
                    <i class="fa-solid fa-plus-minus mr-1"></i> COLUMNA
                </button>
                
                <button onclick="openModal('create')" class="bg-emerald-600 hover:bg-emerald-500 text-white py-2 px-6 rounded border border-emerald-500 transition text-sm font-bold shadow-lg">
                    + NUEVO REGISTRO
                </button>
            </div>
        </div>

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

        <!-- Tabla -->
        <div class="bg-slate-900/40 border border-slate-700 rounded-xl shadow-2xl overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-center text-sm text-slate-300 border-collapse">
                    <thead class="text-xs uppercase bg-slate-800 text-slate-400 tracking-wider">
                        <tr>
                            @foreach($columns as $col)
                                <th scope="col" class="py-3 px-4 border-b border-slate-700 whitespace-nowrap">{{ $col }}</th>
                            @endforeach
                            <th scope="col" class="py-3 px-4 border-b border-slate-700 whitespace-nowrap">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="font-medium">
                        @forelse($records as $row)
                            <tr class="hover:bg-slate-800/40 transition border-b border-slate-700/50">
                                @foreach($columns as $col)
                                    @php
                                        $displayValue = $row->$col;
                                        if (isset($foreignData[$col])) {
                                            $fkInfo = $foreignData[$col];
                                            $match = collect($fkInfo['options'])->firstWhere($fkInfo['key'], $row->$col);
                                            if ($match) {
                                                $displayValue = $match->{$fkInfo['display']};
                                                if ($fkInfo['key'] !== $fkInfo['display']) {
                                                    $displayValue = $row->$col . ' - ' . $displayValue;
                                                }
                                            }
                                        }
                                    @endphp
                                    <td class="py-3 px-4 max-w-xs truncate" title="{{ $displayValue }}">{{ $displayValue }}</td>
                                @endforeach
                                <td class="py-3 px-4 whitespace-nowrap">
                                    <button onclick="openModal('edit', {{ json_encode($row) }})" class="bg-blue-600/85 hover:bg-blue-600 text-white py-1 px-3 rounded text-xs font-bold transition shadow-sm mx-1">Editar</button>
                                    <form action="{{ route('jefatura.abm.destroy', ['table' => $table, 'id' => $row->id]) }}" method="POST" class="inline" onsubmit="return confirm('¿Eliminar este registro permanentemente?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="bg-red-600/85 hover:bg-red-600 text-white py-1 px-3 rounded text-xs font-bold transition shadow-sm mx-1">Borrar</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="{{ count($columns) + 1 }}" class="py-8 text-slate-500">No hay registros en esta tabla.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($records->hasPages())
                <div class="p-4 border-t border-slate-700 bg-slate-800">
                    {{ $records->links() }}
                </div>
            @endif
        </div>
    </div>

    <!-- Modal ABM -->
    <div id="abm-modal" class="fixed inset-0 z-50 hidden bg-black/60 backdrop-blur-sm flex items-center justify-center p-4">
        <div class="bg-slate-800 border border-slate-600 rounded-xl w-full max-w-2xl shadow-2xl flex flex-col max-h-[90vh]">
            <div class="p-4 border-b border-slate-700 flex justify-between items-center bg-slate-900/50 rounded-t-xl">
                <h3 class="text-xl font-bold text-white tracking-wide" id="modal-title">NUEVO REGISTRO</h3>
                <button onclick="closeModal()" class="text-slate-400 hover:text-white text-2xl transition">&times;</button>
            </div>
            
            <div class="p-6 overflow-y-auto flex-1">
                <form id="abm-form" method="POST" action="">
                    @csrf
                    <input type="hidden" name="_method" id="form-method" value="POST">
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        @foreach($columns as $col)
                            @if(in_array($col, ['id', 'created_at', 'updated_at', 'deleted_at']))
                                @continue
                            @endif
                            
                            @php
                                $type = strtolower($columnTypes[$col] ?? 'varchar');
                                $inputType = 'text';
                                $step = '';
                                if (str_contains($type, 'int') || str_contains($type, 'decimal') || str_contains($type, 'float') || str_contains($type, 'double')) {
                                    $inputType = 'number';
                                    if (!str_contains($type, 'int')) { $step = 'step="0.0001"'; }
                                } elseif (str_contains($type, 'date') || str_contains($type, 'timestamp')) {
                                    $inputType = 'date'; // Podría ser datetime-local, pero date es mas comun
                                } elseif (str_contains($type, 'tinyint(1)') || str_contains($type, 'bool')) {
                                    $inputType = 'checkbox';
                                }
                            @endphp
                            
                            <div class="flex flex-col">
                                <label class="text-xs font-bold mb-2 tracking-wide text-slate-400 uppercase">{{ $col }}</label>
                                
                                @if(isset($foreignData[$col]))
                                    @php $fkInfo = $foreignData[$col]; @endphp
                                    <select name="{{ $col }}" id="input-{{ $col }}" class="w-full bg-slate-900 border border-slate-600 rounded p-2 text-white focus:outline-none focus:border-blue-500">
                                        <option value="">(Nulo / Sin asignar)</option>
                                        @foreach($fkInfo['options'] as $opt)
                                            <option value="{{ $opt->{$fkInfo['key']} }}">
                                                {{ $opt->{$fkInfo['key']} }} - {{ $opt->{$fkInfo['display']} }}
                                            </option>
                                        @endforeach
                                    </select>
                                @elseif($inputType === 'checkbox')
                                    <div class="relative inline-block w-10 align-middle select-none transition duration-200 ease-in mt-2">
                                        <input type="checkbox" name="{{ $col }}" id="input-{{ $col }}" value="1" class="toggle-checkbox absolute block w-6 h-6 rounded-full bg-white border-4 appearance-none cursor-pointer border-slate-600"/>
                                        <label for="input-{{ $col }}" class="toggle-label block overflow-hidden h-6 rounded-full bg-slate-600 cursor-pointer"></label>
                                    </div>
                                @elseif(str_contains($type, 'text'))
                                    <textarea name="{{ $col }}" id="input-{{ $col }}" rows="3" class="w-full bg-slate-900 border border-slate-600 rounded p-2 text-white focus:outline-none focus:border-blue-500"></textarea>
                                @else
                                    <input type="{{ $inputType }}" name="{{ $col }}" id="input-{{ $col }}" {!! $step !!} class="w-full bg-slate-900 border border-slate-600 rounded p-2 text-white focus:outline-none focus:border-blue-500">
                                @endif
                            </div>
                        @endforeach
                    </div>
                </form>
            </div>
            
            <div class="p-4 border-t border-slate-700 bg-slate-900/50 rounded-b-xl flex justify-end gap-4">
                <button type="button" onclick="closeModal()" class="bg-slate-600 hover:bg-slate-500 text-white font-bold py-2 px-6 rounded transition">
                    CANCELAR
                </button>
                <button type="button" onclick="document.getElementById('abm-form').submit()" class="bg-blue-600 hover:bg-blue-500 text-white font-bold py-2 px-6 rounded shadow-lg transition tracking-wide">
                    GUARDAR
                </button>
            </div>
        </div>
    </div>

    <!-- Modal Add Column -->
    <div id="add-column-modal" class="fixed inset-0 z-50 hidden bg-black/60 backdrop-blur-sm flex items-center justify-center p-4">
        <div class="bg-slate-800 border border-slate-600 rounded-xl w-full max-w-md shadow-2xl flex flex-col">
            <div class="p-4 border-b border-slate-700 flex justify-between items-center bg-slate-900/50 rounded-t-xl">
                <h3 class="text-xl font-bold text-white tracking-wide">AÑADIR COLUMNA A: {{ strtoupper($table) }}</h3>
                <button onclick="closeColumnModal()" class="text-slate-400 hover:text-white text-2xl transition">&times;</button>
            </div>
            
            <div class="p-6">
                <form id="add-column-form" method="POST" action="{{ route('jefatura.abm.addColumn', $table) }}">
                    @csrf
                    <div class="flex flex-col mb-4">
                        <label class="text-xs font-bold mb-2 tracking-wide text-slate-400 uppercase">Nombre de la Columna</label>
                        <input type="text" name="column_name" required pattern="[a-zA-Z0-9_]+" title="Solo letras, números y guiones bajos" placeholder="ej: observaciones_extra" class="w-full bg-slate-900 border border-slate-600 rounded p-2 text-white focus:outline-none focus:border-indigo-500">
                        <span class="text-xs text-slate-500 mt-1">Sin espacios ni caracteres especiales.</span>
                    </div>

                    <div class="flex flex-col mb-4">
                        <label class="text-xs font-bold mb-2 tracking-wide text-slate-400 uppercase">Tipo de Dato</label>
                        <select name="column_type" required class="w-full bg-slate-900 border border-slate-600 rounded p-2 text-white focus:outline-none focus:border-indigo-500">
                            <option value="string">Texto Corto (Varchar)</option>
                            <option value="integer">Número Entero (Integer)</option>
                            <option value="float">Número Decimal (Float)</option>
                            <option value="date">Fecha (Date)</option>
                            <option value="boolean">Casilla Si/No (Boolean)</option>
                        </select>
                    </div>
                </form>
            </div>
            
            <div class="p-4 border-t border-slate-700 bg-slate-900/50 rounded-b-xl flex justify-end gap-4">
                <button type="button" onclick="closeColumnModal()" class="bg-slate-600 hover:bg-slate-500 text-white font-bold py-2 px-6 rounded transition">
                    CANCELAR
                </button>
                <button type="button" onclick="document.getElementById('add-column-form').submit()" class="bg-indigo-600 hover:bg-indigo-500 text-white font-bold py-2 px-6 rounded shadow-lg transition tracking-wide">
                    AÑADIR COLUMNA
                </button>
            </div>
        </div>
    </div>

    <style>
        .toggle-checkbox:checked { right: 0; border-color: #06b6d4; }
        .toggle-checkbox:checked + .toggle-label { background-color: #06b6d4; }
    </style>

    <script>
        const storeUrl = "{{ route('jefatura.abm.store', $table) }}";
        const updateUrlBase = "{{ route('jefatura.abm.update', ['table' => $table, 'id' => 'ID_PLACEHOLDER']) }}";
        
        function openModal(mode, data = null) {
            const form = document.getElementById('abm-form');
            const title = document.getElementById('modal-title');
            const methodInput = document.getElementById('form-method');
            
            form.reset();
            
            // Clear checkboxes manually (form.reset() works, but just in case)
            document.querySelectorAll('#abm-form input[type="checkbox"]').forEach(cb => cb.checked = false);

            if (mode === 'create') {
                title.innerText = 'NUEVO REGISTRO';
                form.action = storeUrl;
                methodInput.value = 'POST';
            } else {
                title.innerText = 'EDITAR REGISTRO #' + data.id;
                form.action = updateUrlBase.replace('ID_PLACEHOLDER', data.id);
                methodInput.value = 'PUT';
                
                // Populate data
                for (const key in data) {
                    const input = document.getElementById('input-' + key);
                    if (input) {
                        if (input.type === 'checkbox') {
                            input.checked = (data[key] == 1 || data[key] === true);
                        } else {
                            input.value = data[key] !== null ? data[key] : '';
                        }
                    }
                }
            }
            
            document.getElementById('abm-modal').classList.remove('hidden');
        }

        function closeModal() {
            document.getElementById('abm-modal').classList.add('hidden');
        }

        function openColumnModal() {
            document.getElementById('add-column-form').reset();
            document.getElementById('add-column-modal').classList.remove('hidden');
        }

        function closeColumnModal() {
            document.getElementById('add-column-modal').classList.add('hidden');
        }
    </script>
</body>
</html>
