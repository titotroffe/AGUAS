<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
        
        <!-- FontAwesome -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-gray-900 antialiased relative">
        <!-- Fondo de pantalla con efecto oscuro opcional -->
        <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-cover bg-center bg-no-repeat relative" style="background-image: url('data:image/jpeg;base64,{{ base64_encode(file_get_contents(public_path("img/fondo-ciudad.jpg"))) }}'); background-color: #0f172a;">
            
            <!-- Overlay sutil oscuro para mejorar la lectura -->
            <div class="absolute inset-0 bg-slate-900/40 backdrop-blur-[2px] z-0"></div>

            <!-- Logo -->
            <div class="z-10 mb-8 mt-4 relative">
                <!-- Soft spotlight glow behind the logo -->
                <div class="absolute inset-0 bg-white/40 blur-xl scale-125 rounded-full z-0"></div>
                
                <a href="/" class="relative z-10 block">
                    <img src="data:image/svg+xml;base64,{{ base64_encode(file_get_contents(public_path('img/logo.svg'))) }}" alt="AGUAS Logo" style="width: 220px; height: auto;" />
                </a>
            </div>

            <!-- Contenedor Glassmorphism -->
            <div class="z-10 w-full sm:max-w-md px-8 py-8 bg-slate-800/40 backdrop-blur-md border border-white/20 shadow-[0_8px_32px_0_rgba(0,0,0,0.3)] sm:rounded-2xl">
                {{ $slot }}
            </div>
        </div>
    </body>
</html>
