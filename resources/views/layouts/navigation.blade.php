<nav x-data="{ open: false }" class="glass-header sticky top-0 z-50 transition-all duration-300">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-20">
            <div class="flex items-center">
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('menu') }}" class="group flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-blue-500 to-cyan-400 flex items-center justify-center shadow-lg shadow-blue-500/30 group-hover:shadow-blue-400/50 transition-all duration-300">
                            <i class="fa-solid fa-droplet text-white text-xl"></i>
                        </div>
                        <span class="font-extrabold text-2xl tracking-tight bg-clip-text text-transparent bg-gradient-to-r from-white to-slate-400 group-hover:to-white transition-all duration-300">
                            AGUAS
                        </span>
                    </a>
                </div>

                <!-- Navigation Links -->
                <div class="hidden space-x-2 sm:-my-px sm:ms-10 sm:flex items-center">
                    <a href="{{ route('menu') }}" class="px-5 py-2.5 rounded-lg text-sm font-bold tracking-wide transition-all duration-300 flex items-center gap-2 {{ request()->routeIs('menu') ? 'bg-blue-500/10 text-blue-400 border border-blue-500/20 shadow-[0_0_15px_rgba(59,130,246,0.15)]' : 'text-slate-400 hover:text-white hover:bg-white/5' }}">
                        <i class="fa-solid fa-house"></i> Menú Principal
                    </a>
                </div>
            </div>

            <!-- Settings Dropdown -->
            <div class="hidden sm:flex sm:items-center sm:ms-6">
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="inline-flex items-center gap-3 px-4 py-2 border border-slate-700/50 rounded-xl text-sm leading-4 font-medium text-slate-300 bg-slate-800/50 hover:bg-slate-700/50 hover:text-white hover:border-slate-600 focus:outline-none transition-all duration-300 shadow-lg backdrop-blur-md">
                            <div class="w-7 h-7 rounded-full bg-gradient-to-tr from-indigo-500 to-purple-500 flex items-center justify-center text-white font-bold text-xs shadow-md">
                                {{ substr(Auth::user()->name, 0, 1) }}
                            </div>
                            <span class="font-semibold">{{ Auth::user()->name }}</span>
                            <i class="fa-solid fa-chevron-down text-[10px] opacity-70"></i>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <div class="bg-slate-800/90 border border-slate-700/50 rounded-xl overflow-hidden shadow-2xl backdrop-blur-xl py-1">
                            <x-dropdown-link :href="route('profile.edit')" class="hover:bg-slate-700 text-slate-300 hover:text-white font-medium transition-colors flex items-center gap-3 px-4 py-3">
                                <i class="fa-solid fa-user-pen text-blue-400"></i> Editar Perfil
                            </x-dropdown-link>

                            <!-- Authentication -->
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <x-dropdown-link :href="route('logout')"
                                        onclick="event.preventDefault(); this.closest('form').submit();" class="hover:bg-red-500/10 text-red-400 hover:text-red-300 font-medium transition-colors border-t border-slate-700/50 flex items-center gap-3 px-4 py-3">
                                    <i class="fa-solid fa-right-from-bracket"></i> Cerrar Sesión
                                </x-dropdown-link>
                            </form>
                        </div>
                    </x-slot>
                </x-dropdown>
            </div>

            <!-- Hamburger -->
            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-lg text-slate-400 hover:text-white hover:bg-white/10 focus:outline-none transition duration-150 ease-in-out border border-transparent hover:border-white/10">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden bg-slate-900/95 backdrop-blur-3xl border-b border-slate-800 absolute w-full shadow-2xl z-50">
        <div class="pt-4 pb-3 space-y-1 px-4">
            <a href="{{ route('menu') }}" class="block px-4 py-3 rounded-xl text-base font-bold tracking-wide {{ request()->routeIs('menu') ? 'bg-blue-500/20 text-blue-400 border border-blue-500/30 shadow-inner' : 'text-slate-300 hover:bg-white/5 hover:text-white' }} transition-all flex items-center gap-3">
                <i class="fa-solid fa-house"></i> Menú Principal
            </a>
        </div>

        <!-- Responsive Settings Options -->
        <div class="pt-4 pb-4 border-t border-slate-700/50 bg-slate-800/30">
            <div class="px-6 flex items-center gap-4 mb-4">
                <div class="w-12 h-12 rounded-full bg-gradient-to-tr from-indigo-500 to-purple-500 flex items-center justify-center text-white font-bold text-xl shadow-lg ring-2 ring-white/10">
                    {{ substr(Auth::user()->name, 0, 1) }}
                </div>
                <div>
                    <div class="font-bold text-lg text-white">{{ Auth::user()->name }}</div>
                    <div class="font-medium text-sm text-slate-400">{{ Auth::user()->email }}</div>
                </div>
            </div>

            <div class="space-y-2 px-4 mt-4">
                <a href="{{ route('profile.edit') }}" class="block px-4 py-3 rounded-xl text-base font-medium text-slate-300 hover:bg-white/5 hover:text-white transition-all flex items-center gap-3">
                    <i class="fa-solid fa-user-pen text-blue-400"></i> Editar Perfil
                </a>

                <!-- Authentication -->
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <a href="{{ route('logout') }}"
                            onclick="event.preventDefault(); this.closest('form').submit();" class="block px-4 py-3 rounded-xl text-base font-bold text-red-400 hover:bg-red-500/10 hover:text-red-300 border border-transparent hover:border-red-500/20 transition-all flex items-center gap-3">
                        <i class="fa-solid fa-right-from-bracket"></i> Cerrar Sesión
                    </a>
                </form>
            </div>
        </div>
    </div>
</nav>
