<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Formulario de Operadores</title>
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
</head>
<body class="bg-slate-800 text-slate-200 font-sans min-h-screen p-8">

    <div class="max-w-6xl mx-auto">
        
        <div class="text-center mb-12">
            <h1 class="text-2xl font-bold text-white tracking-wider mb-6">FORMULARIO DE OPERADOR DE TURNO</h1>
            <a href="/dashboard" class="bg-slate-700 hover:bg-slate-600 text-white py-2 px-6 rounded border border-slate-500 transition text-sm font-semibold">
                 ← VOLVER AL MENÚ
            </a> 
        </div>

        <form action="/operadores/guardar" method="POST" class="mb-16">
            @csrf 

            <div class="grid grid-cols-4 gap-8 mb-8 text-center">
                <div class="flex flex-col items-center">
                    <label class="text-xs font-bold mb-2 tracking-wide text-slate-400">PRESION EN TANQUE</label>
                    <input type="number" name="presion_tanque" step="0.01" class="w-32 bg-slate-900 border border-slate-600 rounded p-2 text-center text-white focus:outline-none focus:border-blue-500 font-mono" placeholder="0.00">
                </div>
                
                <div class="flex flex-col items-center">
                    <label class="text-xs font-bold mb-2 tracking-wide text-slate-400">PRESION EN PLANTA</label>
                    <input type="number" name="presion_planta" step="0.01" class="w-32 bg-slate-900 border border-slate-600 rounded p-2 text-center text-white focus:outline-none focus:border-blue-500 font-mono" placeholder="0.00">
                </div>

                <div class="flex flex-col items-center">
                    <label class="text-xs font-bold mb-2 tracking-wide text-slate-400">PRESION EN FALCON</label>
                    <input type="number" name="presion_falcon" step="0.01" class="w-32 bg-slate-900 border border-slate-600 rounded p-2 text-center text-white focus:outline-none focus:border-blue-500 font-mono" placeholder="0.00">
                </div>

                <div class="flex flex-col items-center">
                    <label class="text-xs font-bold mb-2 tracking-wide text-slate-400">NIVEL DE CISTERNA</label>
                    <input type="number" name="presion_cisterna" step="0.01" class="w-32 bg-slate-900 border border-slate-600 rounded p-2 text-center text-white focus:outline-none focus:border-blue-500 font-mono" placeholder="0.00">
                </div>
            </div>

            <div class="flex justify-center mb-12">
                <button type="submit" class="bg-blue-600 hover:bg-blue-500 text-white font-bold py-2 px-12 rounded shadow-lg transition tracking-wide text-sm">
                    CONFIRMAR PRESIONES
                </button>
            </div>

            <div class="grid grid-cols-3 gap-8 border-b border-slate-700 pb-12">
                
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
                    <div class="space-y-4 flex flex-col items-start mb-6">
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
                    <button type="button" class="bg-blue-600 hover:bg-blue-500 text-white font-bold py-2 px-12 rounded shadow-lg transition tracking-wide text-sm">
                       CONFIRMAR LAVADO
                    </button>
                </div>

                <div class="flex flex-col items-center justify-center">
                    <div class="mb-6 w-full flex flex-col items-center">
                        <label class="text-xs font-bold mb-2 tracking-wide text-slate-400">HORA INICIO</label>
                        <input type="time" name="hora_inicio" class="w-40 bg-slate-900 border border-slate-600 rounded p-2 text-center text-white focus:outline-none focus:border-blue-500 font-mono">
                    </div>
                    <div class="w-full flex flex-col items-center">
                        <label class="text-xs font-bold mb-2 tracking-wide text-slate-400">HORA FINAL</label>
                        <input type="time" name="hora_final" class="w-40 bg-slate-900 border border-slate-600 rounded p-2 text-center text-white focus:outline-none focus:border-blue-500 font-mono">
                    </div>
                </div>

            </div>
        </form>


        <div class="text-center mb-12">
            <h2 class="text-xl font-bold text-white tracking-wider uppercase">NIVELES DE TANQUES QUÍMICOS</h2>
        </div>

        <div class="grid grid-cols-3 gap-12 text-center mb-16">
            
            <div class="flex flex-col items-center space-y-6">
                <h3 class="text-lg font-bold text-blue-400 tracking-widest">CLORO</h3>
                
                <div class="w-full max-w-xs bg-slate-850 p-4 rounded border border-slate-700/50">
                    <p class="text-xs font-bold text-slate-300 mb-1">TANQUE PRINCIPAL</p>
                    <p class="text-sm font-medium text-slate-400 mb-3">Nivel actual: <span class="text-white font-mono">4.00%</span></p>
                    <input type="number" step="0.1" class="w-full bg-slate-900 border border-slate-600 rounded p-2 text-center text-white focus:outline-none focus:border-blue-500 mb-2 font-mono" placeholder="Ej: 85.5">
                    <button type="button" class="w-full bg-blue-600 hover:bg-blue-500 text-white text-xs font-bold py-2 rounded transition tracking-wide">
                        ACTUALIZAR NIVEL
                    </button>
                </div>

                <div class="w-full max-w-xs bg-slate-850 p-4 rounded border border-slate-700/50">
                    <p class="text-xs font-bold text-slate-300 mb-1">TANQUE AUXILIAR</p>
                    <p class="text-sm font-medium text-slate-400 mb-3">Nivel actual: <span class="text-white font-mono">56.00%</span></p>
                    <input type="number" step="0.1" class="w-full bg-slate-900 border border-slate-600 rounded p-2 text-center text-white focus:outline-none focus:border-blue-500 mb-2 font-mono" placeholder="Ej: 72.3">
                    <button type="button" class="w-full bg-blue-600 hover:bg-blue-500 text-white text-xs font-bold py-2 rounded transition tracking-wide">
                        ACTUALIZAR NIVEL
                    </button>
                </div>
            </div>

            <div class="flex flex-col items-center space-y-6">
                <h3 class="text-lg font-bold text-emerald-400 tracking-widest">POLIAMINA</h3>
                
                <div class="w-full max-w-xs bg-slate-850 p-4 rounded border border-slate-700/50">
                    <p class="text-xs font-bold text-slate-300 mb-1">TANQUE PRINCIPAL</p>
                    <p class="text-sm font-medium text-slate-400 mb-3">Nivel actual: <span class="text-white font-mono">78.40%</span></p>
                    <input type="number" step="0.1" class="w-full bg-slate-900 border border-slate-600 rounded p-2 text-center text-white focus:outline-none focus:border-emerald-500 mb-2 font-mono" placeholder="Ej: 68.7">
                    <button type="button" class="w-full bg-blue-600 hover:bg-blue-500 text-white text-xs font-bold py-2 rounded transition tracking-wide">
                        ACTUALIZAR NIVEL
                    </button>
                </div>

                <div class="w-full max-w-xs bg-slate-850 p-4 rounded border border-slate-700/50">
                    <p class="text-xs font-bold text-slate-300 mb-1">TANQUE AUXILIAR</p>
                    <p class="text-sm font-medium text-slate-400 mb-3">Nivel actual: <span class="text-white font-mono">65.20%</span></p>
                    <input type="number" step="0.1" class="w-full bg-slate-900 border border-slate-600 rounded p-2 text-center text-white focus:outline-none focus:border-emerald-500 mb-2 font-mono" placeholder="Ej: 45.2">
                    <button type="button" class="w-full bg-blue-600 hover:bg-blue-500 text-white text-xs font-bold py-2 rounded transition tracking-wide">
                        ACTUALIZAR NIVEL
                    </button>
                </div>
            </div>

            <div class="flex flex-col items-center space-y-6">
                <h3 class="text-lg font-bold text-red-400 tracking-widest">SULFATO</h3>
                
                <div class="w-full max-w-xs bg-slate-850 p-4 rounded border border-slate-700/50">
                    <p class="text-xs font-bold text-slate-300 mb-1">TANQUE PRINCIPAL</p>
                    <p class="text-sm font-medium text-slate-400 mb-3">Nivel actual: <span class="text-white font-mono">91.20%</span></p>
                    <input type="number" step="0.1" class="w-full bg-slate-900 border border-slate-600 rounded p-2 text-center text-white focus:outline-none focus:border-red-500 mb-2 font-mono" placeholder="Ej: 92.1">
                    <button type="button" class="w-full bg-blue-600 hover:bg-blue-500 text-white text-xs font-bold py-2 rounded transition tracking-wide">
                        ACTUALIZAR NIVEL
                    </button>
                </div>

                <div class="w-full max-w-xs bg-slate-850 p-4 rounded border border-slate-700/50">
                    <p class="text-xs font-bold text-slate-300 mb-1">TANQUE AUXILIAR</p>
                    <p class="text-sm font-medium text-slate-400 mb-3">Nivel actual: <span class="text-white font-mono">68.70%</span></p>
                    <input type="number" step="0.1" class="w-full bg-slate-900 border border-slate-600 rounded p-2 text-center text-white focus:outline-none focus:border-red-500 mb-2 font-mono" placeholder="Ej: 78.9">
                    <button type="button" class="w-full bg-blue-600 hover:bg-blue-500 text-white text-xs font-bold py-2 rounded transition tracking-wide">
                        ACTUALIZAR NIVEL
                    </button>
                </div>
            </div>

        </div>

       

    </div>

</body>
</html>