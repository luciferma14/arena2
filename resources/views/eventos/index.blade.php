@extends('layouts.app')

@section('title', 'Eventos - Roig Arena')

@section('content')
<div class="space-y-8">
    <!-- Header -->
    <div class="text-center mb-12">
        <h1 class="text-4xl font-bold mb-3 bg-gradient-to-r from-red-500 to-red-600 bg-clip-text text-transparent">
            🎭 Próximos Eventos
        </h1>
        <p class="text-slate-400">Descubre y compra entradas para los mejores eventos</p>
    </div>

    <!-- Loading State -->
    <div id="loading" class="text-center py-12">
        <div class="inline-block animate-spin">
            <div class="h-8 w-8 border-4 border-red-600 border-t-transparent rounded-full"></div>
        </div>
        <p class="text-slate-400 mt-3">Cargando eventos...</p>
    </div>

    <!-- Events Grid -->
    <div id="eventos-container" class="hidden grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <!-- Los eventos se cargarán aquí con JavaScript -->
    </div>

    <!-- No Events Message -->
    <div id="no-eventos" class="hidden text-center py-12">
        <p class="text-slate-400 text-lg">No hay eventos disponibles en este momento.</p>
    </div>

    <!-- Error Message -->
    <div id="error-message" class="hidden p-4 rounded-lg bg-red-950/40 border-l-4 border-red-500 text-red-200">
        <p id="error-text"></p>
    </div>
</div>

@endsection

@section('scripts')
<script>
    // Cargar eventos desde la API
    async function cargarEventos() {
        try {
            const response = await fetch('/api/eventos');
            const data = await response.json();

            if (!response.ok) {
                throw new Error(data.message || 'Error al cargar eventos');
            }

            const eventos = data.data;
            const container = document.getElementById('eventos-container');
            const loading = document.getElementById('loading');
            const noEventos = document.getElementById('no-eventos');

            if (eventos.length === 0) {
                loading.classList.add('hidden');
                noEventos.classList.remove('hidden');
                return;
            }

            container.innerHTML = eventos.map(evento => `
                <div class="bg-slate-800 rounded-lg border border-slate-700 overflow-hidden hover:border-red-500 transition group cursor-pointer" onclick="irAEvento(${evento.id})">
                    <div class="relative overflow-hidden h-48 bg-slate-700">
                        ${evento.poster_url ? `
                            <img src="${evento.poster_url}" alt="${evento.nombre}" class="w-full h-full object-cover group-hover:scale-105 transition duration-300">
                        ` : `
                            <div class="w-full h-full flex items-center justify-center text-4xl bg-gradient-to-br from-red-900 to-red-800">
                                🎭
                            </div>
                        `}
                    </div>
                    <div class="p-4">
                        <h3 class="text-lg font-bold text-white mb-2 group-hover:text-red-400 transition">
                            ${evento.nombre}
                        </h3>
                        <p class="text-slate-400 text-sm mb-3 line-clamp-2">
                            ${evento.descripcion_corta}
                        </p>
                        <div class="space-y-2 text-sm text-slate-400 mb-4">
                            <p>📅 ${new Date(evento.fecha).toLocaleDateString('es-ES', { year: 'numeric', month: 'long', day: 'numeric' })}</p>
                            <p>🕐 ${evento.hora}</p>
                        </div>
                        <button class="w-full py-2 bg-red-600 hover:bg-red-700 text-white font-semibold rounded-lg transition duration-200">
                            Ver Detalles
                        </button>
                    </div>
                </div>
            `).join('');

            loading.classList.add('hidden');
            container.classList.remove('hidden');

        } catch (error) {
            console.error('Error:', error);
            document.getElementById('loading').classList.add('hidden');
            document.getElementById('error-message').classList.remove('hidden');
            document.getElementById('error-text').textContent = error.message;
        }
    }

    function irAEvento(id) {
        window.location.href = `/eventos/${id}`;
    }

    // Cargar eventos al iniciar
    cargarEventos();
</script>
@endsection
