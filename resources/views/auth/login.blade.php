<x-guest-layout>
    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}" class="text-white w-full pb-2">
        @csrf

        <!-- Email Address -->
        <div class="relative mb-6">
            <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username"
                   placeholder="{{ __('Email') }}"
                   class="w-full bg-white/10 border border-white/30 rounded-full px-6 py-3 text-white placeholder-white/70 focus:outline-none focus:ring-2 focus:ring-white/60 focus:border-transparent transition-all shadow-inner" />
            <div class="absolute right-5 top-1/2 transform -translate-y-1/2 text-white/80">
                <i class="fa-solid fa-user"></i>
            </div>
            <x-input-error :messages="$errors->get('email')" class="mt-2 text-red-300 font-medium" />
        </div>

        <!-- Password -->
        <div class="relative mb-6">
            <input id="password" type="password" name="password" required autocomplete="current-password"
                   placeholder="{{ __('Password') }}"
                   class="w-full bg-white/10 border border-white/30 rounded-full px-6 py-3 text-white placeholder-white/70 focus:outline-none focus:ring-2 focus:ring-white/60 focus:border-transparent transition-all shadow-inner" />
            <div class="absolute right-5 top-1/2 transform -translate-y-1/2 text-white/80 cursor-pointer hover:text-white transition" onclick="togglePassword()">
                <i class="fa-solid fa-eye-slash" id="eyeIcon"></i>
            </div>
            <x-input-error :messages="$errors->get('password')" class="mt-2 text-red-300 font-medium" />
        </div>

        <!-- Options -->
        <div class="flex items-center justify-between mb-8 px-2 text-sm">
            <label for="remember_me" class="inline-flex items-center cursor-pointer">
                <input id="remember_me" type="checkbox" class="rounded border-white/40 bg-white/20 text-indigo-500 focus:ring-indigo-500 shadow-sm" name="remember">
                <span class="ms-2 text-white/90 font-medium">{{ __('Remember me') }}</span>
            </label>

            @if (Route::has('password.request'))
                <a class="text-white/90 hover:text-white font-medium underline transition" href="{{ route('password.request') }}">
                    {{ __('Forgot your password?') }}
                </a>
            @endif
        </div>

        <!-- Submit Button -->
        <button type="submit" class="w-full bg-white text-slate-900 font-bold text-lg rounded-full py-3 hover:bg-slate-100 hover:shadow-lg transition-all mb-2 shadow-md transform hover:-translate-y-0.5">
            {{ __('Log in') }}
        </button>

        @if (Route::has('register'))
            <div class="text-center text-sm font-medium">
                <span class="text-white/80">¿No está registrado?</span>
                <a href="{{ route('register') }}" class="font-bold text-white hover:underline ms-1">
                    {{ __('Register') }}
                </a>
            </div>
        @endif
    </form>
    
    <script>
        function togglePassword() {
            const pwd = document.getElementById('password');
            const icon = document.getElementById('eyeIcon');
            if (pwd.type === 'password') {
                pwd.type = 'text';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            } else {
                pwd.type = 'password';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            }
        }
    </script>
</x-guest-layout>
