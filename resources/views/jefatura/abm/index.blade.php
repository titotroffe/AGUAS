<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ABM Base de Datos - Jefatura</title>
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>
<body class="bg-slate-800 text-slate-200 font-sans min-h-screen px-4 py-6 md:p-8">
    <div class="max-w-4xl mx-auto">
        <!-- Navegación y Título -->
        <div class="flex flex-col md:relative md:flex md:items-center md:justify-center mb-12 gap-4">
            <div class="md:absolute md:left-0 md:top-1/2 md:-translate-y-1/2 flex justify-center">
                <a href="{{ route('jefatura.index') }}" class="bg-slate-700 hover:bg-slate-600 text-white py-2 px-6 rounded border border-slate-500 transition text-sm font-semibold">
                     ← VOLVER A JEFATURA
                </a>
            </div>
            <h1 class="text-xl md:text-2xl font-bold text-white tracking-wider text-center m-0 w-full">ABM - ADMINISTRADOR DE TABLAS</h1>
        </div>

        <div class="bg-slate-900/40 border border-slate-700 rounded-xl p-4 md:p-8 shadow-2xl">
            <h2 class="text-xl font-bold mb-6 text-center text-blue-400">Seleccione la tabla a administrar</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($availableTables as $table)
                    <a href="{{ route('jefatura.abm.show', $table) }}" class="bg-slate-800 hover:bg-slate-700 border border-slate-600 rounded p-4 text-center transition flex flex-col justify-center items-center h-24 shadow group">
                        <i class="fa-solid fa-table text-slate-500 group-hover:text-blue-400 mb-2 text-2xl transition"></i>
                        <span class="font-bold tracking-wide uppercase text-xs">{{ $table }}</span>
                    </a>
                @endforeach
            </div>
        </div>
    </div>
</body>
</html>
