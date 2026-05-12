@extends('layouts.app')

@section('title', 'Mis Entradas - Roig Arena')

@section('content')
<div class="space-y-8">
    <!-- Header -->
    <div class="text-center mb-12">
        <h1 class="text-4xl font-bold mb-3 bg-gradient-to-r from-red-500 to-red-600 bg-clip-text text-transparent">
            🎟️ Mis Entradas
        </h1>
        <p class="text-slate-400">Tus entradas compradas y códigos QR</p>
    </div>

    <!-- Loading State -->
    <div id="loading" class="text-center py-12">
        <div class="inline-block animate-spin">
            <div class="h-8 w-8 border-4 border-red-600 border-t-transparent rounded-full"></div>
        </div>
        <p class="text-slate-400 mt-3">Cargando entradas...</p>
    </div>

    <!-- Empty State -->
    <div id="empty-state" class="hidden text-center py-12">
        <p class="text-6xl mb-4">🎭</p>
        <p class="text-slate-400 text-lg mb-6">No tienes entradas aún</p>
        <a href="{{ route('home') }}" class="inline-block px-6 py-3 bg-red-600 hover:bg-red-700 text-white font-bold rounded-lg transition">
            Comprar Entradas
        </a>
    </div>

    <!-- Entradas Grid -->
    <div id="entradas-container" class="hidden grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <!-- Entradas will be loaded here -->
    </div>

    <!-- Error Message -->
    <div id="error-message" class="hidden p-4 rounded-lg bg-red-950/40 border-l-4 border-red-500 text-red-200">
        <p id="error-text"></p>
    </div>
</div>

<!-- Modal for QR Code -->
<div id="qr-modal" class="hidden fixed inset-0 bg-black/70 z-50 flex items-center justify-center p-4">
    <div class="bg-slate-800 rounded-lg border border-slate-700 p-8 max-w-md w-full">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-bold text-white">Código QR</h2>
            <button onclick="cerrarModal()" class="text-slate-400 hover:text-white text-2xl">✕</button>
        </div>

        <div id="modal-content" class="space-y-4">
            <!-- Content will be inserted here -->
        </div>

        <button onclick="cerrarModal()" class="w-full mt-6 py-2 bg-slate-700 hover:bg-slate-600 text-white font-semibold rounded-lg transition">
            Cerrar
        </button>
    </div>
</div>

@endsection

@section('scripts')
<script>
    async function cargarEntradas() {
        try {
            const response = await fetch('/api/entradas', {
                headers: {
                    'Authorization': `Bearer ${localStorage.getItem('token')}`
                }
            });

            if (!response.ok) {
                throw new Error('Error al cargar entradas');
            }

            const data = response.json ? response.json() : JSON.parse(await response.text());
            const entradas = await data;

            if (entradas.data && entradas.data.length === 0) {
                document.getElementById('loading').classList.add('hidden');
                document.getElementById('empty-state').classList.remove('hidden');
                return;
            }

            renderizarEntradas(entradas.data || entradas);

        } catch (error) {
            console.error('Error:', error);
            document.getElementById('loading').classList.add('hidden');
            document.getElementById('error-message').classList.remove('hidden');
            document.getElementById('error-text').textContent = error.message;
        }
    }

    function renderizarEntradas(entradas) {
        const container = document.getElementById('entradas-container');

        container.innerHTML = entradas.map(entrada => `
            <div class="bg-slate-800 rounded-lg border border-slate-700 overflow-hidden hover:border-red-500 transition">
                <div class="bg-gradient-to-r from-red-900 to-red-800 p-6 text-white">
                    <h3 class="text-lg font-bold mb-2">${entrada.evento?.nombre || 'Evento'}</h3>
                    <div class="space-y-1 text-sm opacity-90">
                        <p>📅 ${entrada.evento?.fecha ? new Date(entrada.evento.fecha).toLocaleDateString('es-ES', { year: 'numeric', month: 'long', day: 'numeric' }) : 'Fecha desconocida'}</p>
                        <p>🕐 ${entrada.evento?.hora || 'Hora desconocida'}</p>
                    </div>
                </div>

                <div class="p-6 space-y-4">
                    <div class="bg-slate-700/50 p-4 rounded-lg">
                        <p class="text-slate-400 text-sm mb-1">Asiento</p>
                        <p class="text-white font-bold">${entrada.asiento?.nombreCompleto || 'Asiento desconocido'}</p>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div class="bg-slate-700/50 p-3 rounded-lg">
                            <p class="text-slate-400 text-xs mb-1">Precio Pagado</p>
                            <p class="text-red-400 font-bold">€${(entrada.precio_pagado || 0).toFixed(2)}</p>
                        </div>
                        <div class="bg-slate-700/50 p-3 rounded-lg">
                            <p class="text-slate-400 text-xs mb-1">Código QR</p>
                            <p class="text-slate-300 font-mono text-sm">${entrada.codigo_qr}</p>
                        </div>
                    </div>

                    <button
                        onclick="mostrarQR('${entrada.codigo_qr}', '${entrada.evento?.nombre}', '${entrada.asiento?.nombreCompleto}')"
                        class="w-full py-2 bg-red-600 hover:bg-red-700 text-white font-semibold rounded-lg transition duration-200"
                    >
                        📱 Ver QR
                    </button>
                </div>
            </div>
        `).join('');

        document.getElementById('loading').classList.add('hidden');
        document.getElementById('entradas-container').classList.remove('hidden');
    }

    function mostrarQR(codigoQR, evento, asiento) {
        const modalContent = document.getElementById('modal-content');

        // Generar un "código QR visual" simple (en producción usarías una librería como qrcode.js)
        const qrSimple = codigoQR.split('').map((c, i) =>
            `<span style="background-color: ${c.charCodeAt(0) % 2 === 0 ? 'white' : 'black'}; width: 20px; height: 20px; display: inline-block;"></span>`
        ).join('');

        modalContent.innerHTML = `
            <div class="bg-white p-8 rounded-lg text-center mb-4">
                <p class="text-slate-900 font-bold mb-2">${evento}</p>
                <p class="text-slate-700 text-sm mb-4">Asiento: ${asiento}</p>
                <div class="inline-block">
                    <div style="display: grid; grid-template-columns: repeat(15, 1fr); gap: 1px; background: #000; padding: 2px;">
                        ${Array(225).fill(0).map((_, i) =>
                            `<div style="width: 12px; height: 12px; background: ${Math.random() > 0.5 ? 'black' : 'white'};"></div>`
                        ).join('')}
                    </div>
                </div>
                <p class="text-slate-900 font-mono text-sm mt-4 break-all">${codigoQR}</p>
            </div>
            <div class="bg-blue-50 border border-blue-200 rounded p-3 text-sm text-blue-900">
                <p class="font-semibold mb-1">💡 Información</p>
                <p>Muestra este código QR en la entrada para acceder al evento.</p>
            </div>
        `;

        document.getElementById('qr-modal').classList.remove('hidden');
    }

    function cerrarModal() {
        document.getElementById('qr-modal').classList.add('hidden');
    }

    // Cerrar modal al hacer click fuera
    document.getElementById('qr-modal')?.addEventListener('click', function(e) {
        if (e.target === this) {
            cerrarModal();
        }
    });

    // Cargar entradas al iniciar
    cargarEntradas();
</script>
@endsection
