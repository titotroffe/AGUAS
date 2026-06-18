<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Menú Principal</title>
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    <style>
        .texto-borde {
            text-shadow: 2px 2px 4px rgba(0,0,0,0.9), -1px -1px 0 #000, 1px -1px 0 #000, -1px 1px 0 #000, 1px 1px 0 #000;
        }
    </style>
</head>
<body class="m-0 p-0 overflow-hidden font-sans h-screen flex flex-col relative" style="background-image: url('data:image/jpeg;base64,{{ base64_encode(file_get_contents(public_path("img/fondo-ciudad.jpg"))) }}'); background-size: cover; background-position: center; background-repeat: no-repeat; background-color: #0f172a;">

    <div class="relative z-10 bg-[#333333] w-full flex justify-between items-center px-6 py-2 shadow-md">
        <div class="text-white font-bold text-[15px] tracking-wide">
            Bienvenido, {{ Auth::user()->name }}
        </div>
        
        <div class="flex gap-4">
            <a href="#" class="bg-blue-600 hover:bg-blue-500 text-white py-1.5 px-6 rounded text-sm font-semibold transition shadow">
                Editar Perfil
            </a>
            
            <form method="POST" action="/logout" class="m-0">
                @csrf
                <button type="submit" class="bg-blue-600 hover:bg-blue-500 text-white py-1.5 px-6 rounded text-sm font-semibold transition shadow">
                    Cerrar Sesión
                </button>
            </form>
        </div>
    </div>

    <div class="relative z-10 flex-1 flex items-center justify-center gap-16 px-4">

        <a href="/jefatura" class="flex flex-col items-center group transform transition duration-300 hover:scale-105">
            <h2 class="text-white text-[28px] font-bold mb-4 texto-borde tracking-wide">Jefatura</h2>
            <div class="w-[140px] h-[140px] rounded-full bg-[#1c2bb5] flex items-center justify-center border-4 border-transparent group-hover:border-white transition shadow-2xl">
                <i class="fa-solid fa-sitemap text-6xl text-white"></i>
            </div>
        </a>

        <a href="/operadores" class="flex flex-col items-center group transform transition duration-300 hover:scale-105">
            <h2 class="text-white text-[28px] font-bold mb-4 texto-borde tracking-wide">Encargado de Turno</h2>
            <div class="w-[140px] h-[140px] rounded-full bg-[#1c2bb5] flex items-center justify-center border-4 border-transparent group-hover:border-white transition shadow-2xl">
                <i class="fa-solid fa-droplet text-6xl text-white relative">
    
                </i>
            </div>
        </a>

        <a href="/quimico" class="flex flex-col items-center group transform transition duration-300 hover:scale-105">
            <h2 class="text-white text-[28px] font-bold mb-4 texto-borde tracking-wide">Químico</h2>
            <div class="w-[140px] h-[140px] rounded-full bg-[#1c2bb5] flex items-center justify-center border-4 border-transparent group-hover:border-white transition shadow-2xl">
                <i class="fa-solid fa-atom text-7xl text-white"></i>
            </div>
        </a>

        <a href="/mantenimiento" class="flex flex-col items-center group transform transition duration-300 hover:scale-105">
            <h2 class="text-white text-[28px] font-bold mb-4 texto-borde tracking-wide">Mantenimiento</h2>
            <div class="w-[140px] h-[140px] rounded-full bg-[#1c2bb5] flex items-center justify-center border-4 border-transparent group-hover:border-white transition shadow-2xl">
                <i class="fa-solid fa-user-gear text-6xl text-white"></i>
            </div>
        </a>

    </div>

</body>
</html>