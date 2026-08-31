@props([
    'readonly' => false,
    'estados'  => [],
])

@php
    $csrfToken = csrf_token();

    // Helper: obtener estado de un dispositivo
    $est = function(string $key) use ($estados): bool {
        return isset($estados[$key]) ? (bool) $estados[$key]['estado'] : false;
    };
    $op = function(string $key) use ($estados): ?string {
        return isset($estados[$key]) ? ($estados[$key]['operador'] ?? null) : null;
    };

    $labels = [
        'bomba_1'    => 'Bomba 1',
        'bomba_2'    => 'Bomba 2',
        'bomba_3'    => 'Bomba 3',
        'pozo_norte' => 'Pozo Norte',
        'pozo_sur'   => 'Pozo Sur',
    ];
@endphp

{{-- ────────────────────────────────────────────────────────────── --}}
{{-- ESTILOS INTERNOS DEL COMPONENTE                               --}}
{{-- ────────────────────────────────────────────────────────────── --}}
<style>
    /* Toggle Switch */
    .bomba-toggle-wrap {
        position: relative;
        display: inline-block;
        width: 64px;
        height: 32px;
        flex-shrink: 0;
    }
    .bomba-toggle-wrap input {
        opacity: 0;
        width: 0;
        height: 0;
        position: absolute;
    }
    .bomba-slider {
        position: absolute;
        cursor: pointer;
        inset: 0;
        background: #334155;
        border: 1.5px solid #475569;
        border-radius: 999px;
        transition: background 0.3s, border-color 0.3s;
    }
    .bomba-slider:before {
        content: '';
        position: absolute;
        height: 22px;
        width: 22px;
        left: 4px;
        top: 50%;
        transform: translateY(-50%);
        background: #94a3b8;
        border-radius: 50%;
        transition: transform 0.3s, background 0.3s;
        box-shadow: 0 2px 6px rgba(0,0,0,0.4);
    }
    .bomba-toggle-wrap input:checked + .bomba-slider {
        background: #16a34a;
        border-color: #22c55e;
    }
    .bomba-toggle-wrap input:checked + .bomba-slider:before {
        transform: translateX(32px) translateY(-50%);
        background: #ffffff;
    }
    .bomba-toggle-wrap input:disabled + .bomba-slider {
        cursor: not-allowed;
        opacity: 0.55;
    }

    /* Pulse indicator */
    .pulse-on {
        animation: pulseGreen 1.8s infinite;
    }
    @keyframes pulseGreen {
        0%, 100% { box-shadow: 0 0 0 0 rgba(34,197,94,0.6); }
        50%       { box-shadow: 0 0 0 7px rgba(34,197,94,0); }
    }

    /* Card hover */
    .bomba-card {
        transition: transform 0.2s, box-shadow 0.2s, border-color 0.2s;
    }
    .bomba-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 32px rgba(0,0,0,0.35);
    }
    .bomba-card.on {
        border-color: #22c55e !important;
        background: linear-gradient(135deg, rgba(22,163,74,0.12) 0%, rgba(15,23,42,0.0) 100%) !important;
    }
    .bomba-card.on .bomba-title {
        color: #4ade80;
    }

    /* Spinner */
    .bomba-spinner {
        display: none;
        width: 18px; height: 18px;
        border: 2px solid #64748b;
        border-top-color: #22c55e;
        border-radius: 50%;
        animation: spin 0.6s linear infinite;
    }
    @keyframes spin { to { transform: rotate(360deg); } }

    /* Toast */
    #bombas-toast {
        position: fixed;
        bottom: 5rem; right: 1.5rem;
        z-index: 99999;
        min-width: 240px;
        max-width: 340px;
        padding: 0.9rem 1.4rem;
        border-radius: 0.75rem;
        font-size: 0.875rem;
        font-weight: 600;
        pointer-events: none;
        opacity: 0;
        transform: translateY(12px);
        transition: opacity 0.3s, transform 0.3s;
        box-shadow: 0 8px 32px rgba(0,0,0,0.5);
    }
    #bombas-toast.show {
        opacity: 1;
        transform: translateY(0);
    }
    #bombas-toast.success { background: #14532d; border: 1px solid #22c55e; color: #bbf7d0; }
    #bombas-toast.error   { background: #7f1d1d; border: 1px solid #ef4444; color: #fecaca; }
</style>

{{-- ────────────────────────────────────────────────────────────── --}}
{{-- PANEL PRINCIPAL                                                --}}
{{-- ────────────────────────────────────────────────────────────── --}}
<details id="details-bombas" class="bg-slate-900/40 rounded-xl border border-slate-700 mb-8 shadow-2xl group overflow-hidden">
    <summary class="list-none cursor-pointer bg-slate-800/80 p-5 flex justify-between items-center text-lg font-bold text-white tracking-wider hover:bg-slate-700/50 transition border-b border-slate-700">
        <div class="flex items-center gap-3">
            <span class="text-blue-400">ESTADO DE BOMBAS Y POZOS</span>
            @if($readonly)
                <span class="text-xs font-normal bg-slate-700 text-slate-400 px-3 py-1 rounded-full border border-slate-600">SOLO LECTURA</span>
            @endif
        </div>
        <span class="transform transition-transform group-open:rotate-180 text-slate-400">▼</span>
    </summary>

    <div class="p-6 md:p-8">

        @if($readonly)
        <div class="flex items-center gap-2 bg-amber-900/30 border border-amber-700/50 text-amber-300 text-sm px-4 py-3 rounded-xl mb-6">
            <i class="fa-solid fa-eye text-amber-400"></i>
            <span>Modo visualización. Solo el personal operador puede cambiar el estado de las bombas y pozos.</span>
        </div>
        @endif

        {{-- ── BOMBAS DE RÍO ──────────────────────────────────────── --}}
        <div class="mb-8">
            <div class="flex items-center gap-2 mb-4">
                <h3 class="text-sm font-bold tracking-widest text-slate-400 uppercase">Bombas de Río</h3>
                <span class="text-xs text-slate-500">(máx. 2 encendidas)</span>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                @foreach(['bomba_1','bomba_2','bomba_3'] as $dispositivo)
                @php $encendido = $est($dispositivo); $operador = $op($dispositivo); @endphp
                <div id="card-{{ $dispositivo }}"
                     class="bomba-card {{ $encendido ? 'on' : '' }} bg-slate-800/70 border border-slate-700 rounded-lg p-3 flex flex-col items-center gap-2">

                    {{-- Indicador LED --}}
                    <div id="led-{{ $dispositivo }}"
                         class="{{ $encendido ? 'bg-green-500 pulse-on' : 'bg-slate-600' }} w-3 h-3 rounded-full self-end transition-colors duration-300"></div>

                    {{-- Icono --}}
                    <i id="icon-{{ $dispositivo }}"
                       class="fa-solid fa-fan text-2xl {{ $encendido ? 'text-green-400' : 'text-slate-500' }} transition-colors duration-300"></i>

                    {{-- Nombre --}}
                    <span id="title-{{ $dispositivo }}"
                          class="bomba-title text-sm font-bold tracking-wide {{ $encendido ? 'text-green-400' : 'text-slate-300' }}">
                        {{ $labels[$dispositivo] }}
                    </span>

                    {{-- Estado texto --}}
                    <span id="status-{{ $dispositivo }}"
                          class="text-xs font-semibold px-3 py-1 rounded-full {{ $encendido ? 'bg-green-900/50 text-green-300 border border-green-700' : 'bg-slate-700 text-slate-400 border border-slate-600' }}">
                        {{ $encendido ? 'ENCENDIDA' : 'APAGADA' }}
                    </span>

                    {{-- Toggle --}}
                    <div class="flex items-center gap-3">
                        <span class="text-xs text-slate-500">OFF</span>
                        <label class="bomba-toggle-wrap" title="{{ $readonly ? 'Sin permisos para modificar' : '' }}">
                            <input type="checkbox"
                                   id="toggle-{{ $dispositivo }}"
                                   data-dispositivo="{{ $dispositivo }}"
                                   class="bomba-switch"
                                   {{ $encendido ? 'checked' : '' }}
                                   {{ $readonly ? 'disabled' : '' }}>
                            <span class="bomba-slider"></span>
                        </label>
                        <span class="text-xs text-slate-500">ON</span>
                        <div class="bomba-spinner" id="spinner-{{ $dispositivo }}"></div>
                    </div>

                    {{-- Operador --}}
                    <div id="operador-{{ $dispositivo }}" class="text-xs text-slate-500">
                        @if($operador)
                            <i class="fa-solid fa-user text-slate-600 mr-1"></i>{{ $operador }}
                        @endif
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        {{-- Separador --}}
        <div class="border-t border-slate-700/60 my-6"></div>

        {{-- ── POZOS ──────────────────────────────────────────────── --}}
        <div>
            <div class="flex items-center gap-2 mb-4">
                <h3 class="text-sm font-bold tracking-widest text-slate-400 uppercase">Pozos</h3>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                @foreach(['pozo_norte','pozo_sur'] as $dispositivo)
                @php $encendido = $est($dispositivo); $operador = $op($dispositivo); @endphp
                <div id="card-{{ $dispositivo }}"
                     class="bomba-card {{ $encendido ? 'on' : '' }} bg-slate-800/70 border border-slate-700 rounded-lg p-3 flex flex-col items-center gap-2">

                    <div id="led-{{ $dispositivo }}"
                         class="{{ $encendido ? 'bg-green-500 pulse-on' : 'bg-slate-600' }} w-3 h-3 rounded-full self-end transition-colors duration-300"></div>

                    <i class="fa-solid fa-droplet text-2xl {{ $encendido ? 'text-green-400' : 'text-slate-500' }}" id="icon-{{ $dispositivo }}"></i>

                    <span id="title-{{ $dispositivo }}"
                          class="bomba-title text-sm font-bold tracking-wide {{ $encendido ? 'text-green-400' : 'text-slate-300' }}">
                        {{ $labels[$dispositivo] }}
                    </span>

                    <span id="status-{{ $dispositivo }}"
                          class="text-xs font-semibold px-3 py-1 rounded-full {{ $encendido ? 'bg-green-900/50 text-green-300 border border-green-700' : 'bg-slate-700 text-slate-400 border border-slate-600' }}">
                        {{ $encendido ? 'ENCENDIDO' : 'APAGADO' }}
                    </span>

                    <div class="flex items-center gap-3">
                        <span class="text-xs text-slate-500">OFF</span>
                        <label class="bomba-toggle-wrap" title="{{ $readonly ? 'Sin permisos para modificar' : '' }}">
                            <input type="checkbox"
                                   id="toggle-{{ $dispositivo }}"
                                   data-dispositivo="{{ $dispositivo }}"
                                   class="bomba-switch"
                                   {{ $encendido ? 'checked' : '' }}
                                   {{ $readonly ? 'disabled' : '' }}>
                            <span class="bomba-slider"></span>
                        </label>
                        <span class="text-xs text-slate-500">ON</span>
                        <div class="bomba-spinner" id="spinner-{{ $dispositivo }}"></div>
                    </div>

                    <div id="operador-{{ $dispositivo }}" class="text-xs text-slate-500">
                        @if($operador)
                            <i class="fa-solid fa-user text-slate-600 mr-1"></i>{{ $operador }}
                        @endif
                    </div>
                </div>
                @endforeach
            </div>
        </div>

    </div>{{-- /p-6 --}}
</details>

{{-- Toast de notificación --}}
<div id="bombas-toast"></div>

{{-- ────────────────────────────────────────────────────────────── --}}
{{-- JAVASCRIPT                                                     --}}
{{-- ────────────────────────────────────────────────────────────── --}}
@if(!$readonly)
<script>
(function () {
    const CSRF = '{{ $csrfToken }}';
    const TOGGLE_URL = '{{ route("bombas.toggle") }}';
    const ESTADO_URL = '{{ route("bombas.estado") }}';
    let lastToggleTime = 0; // debounce

    // ── Helpers visuales ────────────────────────────────────────────
    function showToast(msg, type = 'success') {
        const t = document.getElementById('bombas-toast');
        t.textContent = msg;
        t.className = 'show ' + type;
        clearTimeout(t._timer);
        t._timer = setTimeout(() => { t.className = ''; }, 3500);
    }

    function setCardState(dispositivo, encendido, operador) {
        const esBomba = ['bomba_1','bomba_2','bomba_3'].includes(dispositivo);
        const card    = document.getElementById('card-' + dispositivo);
        const led     = document.getElementById('led-' + dispositivo);
        const icon    = document.getElementById('icon-' + dispositivo);
        const title   = document.getElementById('title-' + dispositivo);
        const status  = document.getElementById('status-' + dispositivo);
        const toggle  = document.getElementById('toggle-' + dispositivo);
        const opEl    = document.getElementById('operador-' + dispositivo);

        if (!card) return;

        if (encendido) {
            card.classList.add('on');
            led.className = 'bg-green-500 pulse-on w-3 h-3 rounded-full self-end transition-colors duration-300';
            icon.className = icon.className.replace('text-slate-500', '').replace('text-green-400', '') + ' text-green-400';
            title.className = title.className.replace('text-slate-300', '').replace('text-green-400', '') + ' text-green-400';
            status.className = 'text-xs font-semibold px-3 py-1 rounded-full bg-green-900/50 text-green-300 border border-green-700';
            status.textContent = esBomba ? 'ENCENDIDA' : 'ENCENDIDO';
        } else {
            card.classList.remove('on');
            led.className = 'bg-slate-600 w-3 h-3 rounded-full self-end transition-colors duration-300';
            icon.className = icon.className.replace('text-green-400', 'text-slate-500');
            title.className = title.className.replace('text-green-400', 'text-slate-300');
            status.className = 'text-xs font-semibold px-3 py-1 rounded-full bg-slate-700 text-slate-400 border border-slate-600';
            status.textContent = esBomba ? 'APAGADA' : 'APAGADO';
        }

        if (toggle) toggle.checked = encendido;
        if (opEl) {
            opEl.innerHTML = operador
                ? '<i class="fa-solid fa-user text-slate-600 mr-1"></i>' + operador
                : '';
        }
    }

    function setSpinner(dispositivo, visible) {
        const sp = document.getElementById('spinner-' + dispositivo);
        if (sp) sp.style.display = visible ? 'block' : 'none';
    }

    // ── Toggle handler ───────────────────────────────────────────────
    document.querySelectorAll('.bomba-switch').forEach(function (chk) {
        chk.addEventListener('change', async function () {
            const now = Date.now();
            if (now - lastToggleTime < 500) {
                // Revertir UI si debounced
                this.checked = !this.checked;
                return;
            }
            lastToggleTime = now;

            const dispositivo = this.dataset.dispositivo;
            const nuevoEstado = this.checked;

            // Deshabilitar temporalmente para evitar doble click
            this.disabled = true;
            setSpinner(dispositivo, true);

            try {
                const res  = await fetch(TOGGLE_URL, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': CSRF,
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({ dispositivo, estado: nuevoEstado }),
                });

                const data = await res.json();

                if (!res.ok || !data.ok) {
                    // Revertir
                    this.checked = !nuevoEstado;
                    showToast(data.mensaje || 'Error al cambiar estado.', 'error');
                } else {
                    setCardState(dispositivo, nuevoEstado, null);
                    showToast(data.mensaje, 'success');
                }
            } catch (err) {
                this.checked = !nuevoEstado;
                showToast('Error de conexión. Inténtalo de nuevo.', 'error');
            } finally {
                this.disabled = false;
                setSpinner(dispositivo, false);
            }
        });
    });

    // ── Polling cada 15s (sincronizar con otros operadores) ──────────
    async function pollEstado() {
        try {
            const res  = await fetch(ESTADO_URL, {
                headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF }
            });
            if (!res.ok) return;
            const data = await res.json();

            Object.entries(data).forEach(([dispositivo, info]) => {
                const toggle = document.getElementById('toggle-' + dispositivo);
                // Solo actualizar si no está activo el foco del usuario en este toggle
                if (toggle && !toggle.disabled && document.activeElement !== toggle) {
                    const estadoActual = toggle.checked;
                    if (estadoActual !== info.estado) {
                        setCardState(dispositivo, info.estado, info.operador);
                    }
                }
            });
        } catch (_) { /* ignorar errores silenciosos */ }
    }

    setInterval(pollEstado, 15000);
})();
</script>
@else
{{-- Modo readonly: polling solo visual, sin controles --}}
<script>
(function () {
    const ESTADO_URL = '{{ route("bombas.estado") }}';
    const CSRF = '{{ $csrfToken }}';

    function setCardStateReadonly(dispositivo, encendido, operador) {
        const esBomba = ['bomba_1','bomba_2','bomba_3'].includes(dispositivo);
        const card  = document.getElementById('card-' + dispositivo);
        const led   = document.getElementById('led-' + dispositivo);
        const icon  = document.getElementById('icon-' + dispositivo);
        const title = document.getElementById('title-' + dispositivo);
        const status= document.getElementById('status-' + dispositivo);
        const toggle= document.getElementById('toggle-' + dispositivo);
        const opEl  = document.getElementById('operador-' + dispositivo);

        if (!card) return;

        if (encendido) {
            card.classList.add('on');
            led.className = 'bg-green-500 pulse-on w-3 h-3 rounded-full self-end transition-colors duration-300';
            icon.className = icon.className.replace('text-slate-500', 'text-green-400');
            if (title) { title.className = title.className.replace('text-slate-300', 'text-green-400'); }
            if (status) { status.className = 'text-xs font-semibold px-3 py-1 rounded-full bg-green-900/50 text-green-300 border border-green-700'; status.textContent = esBomba ? 'ENCENDIDA' : 'ENCENDIDO'; }
        } else {
            card.classList.remove('on');
            led.className = 'bg-slate-600 w-3 h-3 rounded-full self-end transition-colors duration-300';
            icon.className = icon.className.replace('text-green-400', 'text-slate-500');
            if (title) { title.className = title.className.replace('text-green-400', 'text-slate-300'); }
            if (status) { status.className = 'text-xs font-semibold px-3 py-1 rounded-full bg-slate-700 text-slate-400 border border-slate-600'; status.textContent = esBomba ? 'APAGADA' : 'APAGADO'; }
        }

        if (toggle) toggle.checked = encendido;
        if (opEl) {
            opEl.innerHTML = operador
                ? '<i class="fa-solid fa-user text-slate-600 mr-1"></i>' + operador
                : '';
        }
    }

    async function pollEstado() {
        try {
            const res = await fetch(ESTADO_URL, {
                headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF }
            });
            if (!res.ok) return;
            const data = await res.json();
            Object.entries(data).forEach(([d, info]) => setCardStateReadonly(d, info.estado, info.operador));
        } catch (_) {}
    }

    setInterval(pollEstado, 15000);
})();
</script>
@endif
