{{-- Componente Global de Alerta Popup Centrada y Minimalista de Dosificadora (ESP32) --}}
<div id="global-alerta-dosificadora" style="display: none;" class="fixed inset-0 z-[9999] bg-black/55 backdrop-blur-[2px] flex items-center justify-center p-4">
    <div class="bg-[#132235] border border-red-500/40 rounded-xl max-w-sm w-full p-5 shadow-2xl text-center relative">
        <div class="mx-auto mb-3 w-11 h-11 rounded-full bg-red-500/15 border border-red-500/30 flex items-center justify-center">
            <i class="fa-solid fa-triangle-exclamation text-red-400 text-lg"></i>
        </div>

        <h3 class="text-base font-bold text-white tracking-wide">
            Alerta en Dosificadora
        </h3>

        <p id="alerta-dosificadora-motivo" class="text-sm text-gray-300 mt-1.5 mb-5">
            La corriente bajó a 0 A.
        </p>

        <div class="flex items-center justify-center gap-2.5">
            <button type="button" onclick="cerrarPopupDosificadora()" class="flex-1 bg-white/10 hover:bg-white/15 text-gray-200 text-xs font-semibold py-2.5 px-4 rounded-lg border border-white/10 transition cursor-pointer">
                Cerrar
            </button>
            <button type="button" onclick="irADosificadora()" class="flex-1 bg-red-600 hover:bg-red-500 text-white text-xs font-bold py-2.5 px-4 rounded-lg transition cursor-pointer">
                Ver Dosificadora
            </button>
        </div>
    </div>
</div>

<script>
    window.__alertaDosificadoraSilenciada = false;

    function cerrarPopupDosificadora() {
        window.__alertaDosificadoraSilenciada = true;
        const popup = document.getElementById('global-alerta-dosificadora');
        if (popup) popup.style.display = 'none';
    }

    function irADosificadora() {
        window.__alertaDosificadoraSilenciada = true;
        const popup = document.getElementById('global-alerta-dosificadora');
        if (popup) popup.style.display = 'none';

        sessionStorage.setItem('activeTab_operadores', 'dosificadora');
        if (window.location.pathname.includes('/operadores')) {
            window.dispatchEvent(new CustomEvent('cambiar-tab', { detail: 'dosificadora' }));
        } else {
            window.location.href = '/operadores';
        }
    }

    (function () {
        if (window.__dosificadoraMonitorIniciado) return;
        window.__dosificadoraMonitorIniciado = true;

        // Limpiar cualquier dato viejo guardado en el navegador
        try {
            sessionStorage.removeItem('historial_dosificadora');
        } catch (_) {}

        const API_URL = '{{ route("api.dosificadoras.actual") }}';

        function actualizarUI(data) {
            if (!data) return;
            window.dispatchEvent(new CustomEvent('telemetria-dosificadora', { detail: data }));

            const popup    = document.getElementById('global-alerta-dosificadora');
            const elMotivo = document.getElementById('alerta-dosificadora-motivo');

            if (!popup) return;

            // Solo disparar alerta si realmente hay conexión por POST activa desde el ESP32
            if (!data.conectado || data.voltaje === null || typeof data.voltaje === 'undefined') {
                popup.style.display = 'none';
                return;
            }

            const v = Number(data.voltaje);
            const c = Number(data.corriente);
            const falloVoltaje = v <= 1;
            const falloCorriente = c <= 0.02;

            if (falloVoltaje || falloCorriente) {
                if (elMotivo) {
                    if (falloVoltaje && falloCorriente) {
                        elMotivo.textContent = 'El voltaje y la corriente bajaron a 0.';
                    } else if (falloVoltaje) {
                        elMotivo.textContent = 'El voltaje bajó a 0 V.';
                    } else {
                        elMotivo.textContent = 'La corriente bajó a 0 A.';
                    }
                }

                if (!window.__alertaDosificadoraSilenciada) {
                    popup.style.display = 'flex';
                }
            } else {
                window.__alertaDosificadoraSilenciada = false;
                popup.style.display = 'none';
            }
        }

        // Únicamente lee lo que recibió el servidor por POST en /api/dosificadoras (sin GET al ESP32 ni datos en caché del navegador)
        async function consultarTelemetria() {
            try {
                const res = await fetch(API_URL, {
                    headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                    cache: 'no-store'
                });
                if (res.ok) {
                    const data = await res.json();
                    actualizarUI(data);
                }
            } catch (_) {}

            setTimeout(consultarTelemetria, 2000);
        }

        document.addEventListener('DOMContentLoaded', () => {
            consultarTelemetria();
        });
    })();
</script>
