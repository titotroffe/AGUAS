<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'AGUAS') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
        
        <!-- Icons -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <style>
            body {
                font-family: 'Outfit', sans-serif;
                background-color: #050810;
                color: #e2e8f0;
            }
            
            /* Background animation */
            .bg-animated {
                background: radial-gradient(circle at 15% 50%, rgba(14, 38, 82, 0.4), transparent 50%),
                            radial-gradient(circle at 85% 30%, rgba(13, 73, 92, 0.4), transparent 50%);
                background-attachment: fixed;
                animation: bgMove 15s ease-in-out infinite alternate;
            }
            
            @keyframes bgMove {
                0% { background-position: 0% 0%; }
                100% { background-position: 100% 100%; }
            }
            
            /* Glassmorphism utility */
            .glass {
                background: rgba(15, 23, 42, 0.6);
                backdrop-filter: blur(16px);
                -webkit-backdrop-filter: blur(16px);
                border: 1px solid rgba(255, 255, 255, 0.05);
                box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
            }
            
            .glass-header {
                background: rgba(11, 17, 32, 0.7);
                backdrop-filter: blur(20px);
                border-bottom: 1px solid rgba(255, 255, 255, 0.05);
            }

            /* Custom scrollbar */
            ::-webkit-scrollbar {
                width: 8px;
                height: 8px;
            }
            ::-webkit-scrollbar-track {
                background: rgba(15, 23, 42, 0.8);
            }
            ::-webkit-scrollbar-thumb {
                background: rgba(59, 130, 246, 0.5);
                border-radius: 4px;
            }
            ::-webkit-scrollbar-thumb:hover {
                background: rgba(59, 130, 246, 0.8);
            }
        </style>
    </head>
    <body class="antialiased bg-animated min-h-screen flex flex-col relative selection:bg-blue-500/30 selection:text-blue-200 text-slate-300">
        <!-- Ambient Glow -->
        <div class="absolute top-0 inset-x-0 h-40 bg-gradient-to-b from-blue-900/20 to-transparent pointer-events-none -z-10"></div>
        
        @include('layouts.navigation')

        <!-- Page Heading -->
        @isset($header)
            <header class="glass-header sticky top-0 z-40 shadow-lg shadow-black/20">
                <div class="max-w-7xl mx-auto py-5 px-4 sm:px-6 lg:px-8">
                    {{ $header }}
                </div>
            </header>
        @endisset

        <!-- Page Content -->
        <main class="flex-grow flex flex-col relative z-10 w-full mb-12">
            <div class="w-full">
                {{ $slot }}
            </div>
        </main>
        
        <footer class="glass-header mt-auto py-6">
            <div class="max-w-7xl mx-auto px-4 text-center text-sm text-slate-500 font-medium tracking-wide">
                &copy; {{ date('Y') }} AGUAS <span class="text-blue-500 ml-2">•</span> Premium System
            </div>
        </footer>
    </body>
</html>
