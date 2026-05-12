@extends('layouts.app')

@section('title', 'Evento - Roig Arena')

@section('content')
<div class="space-y-8">
    <!-- Loading State -->
    <div id="loading" class="text-center py-12">
        <div class="inline-block animate-spin">
            <div class="h-8 w-8 border-4 border-red-600 border-t-transparent rounded-full"></div>
        </div>
        <p class="text-slate-400 mt-3">Cargando evento...</p>
    </div>

    <!-- Event Details -->
    <div id="evento-container" class="hidden space-y-8">
        <!-- Back Button -->
        <a href="{{ route('home') }}" class="inline-flex items-center text-red-400 hover:text-red-300 transition">
            ← Volver a eventos
        </a>

        <!-- Event Header -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <div class="md:col-span-2">
                <div id="evento-poster" class="w-full h-80 bg-slate-700 rounded-lg mb-6 overflow-hidden">
                    <!-- Poster image will be inserted here -->
                </div>
                <div id="evento-info" class="space-y-4">
                    <!-- Event details will be inserted here -->
                </div>
            </div>

            <!-- Sidebar with Stats -->
            <div class="bg-slate-800 rounded-lg border border-slate-700 p-6 h-fit sticky top-24">
                <div id="evento-stats" class="space-y-4">
                    <!-- Stats will be inserted here -->
                </div>
            </div>
        </div>

        <!-- Sectors and Seats -->
        <div class="mt-12">
            <h2 class="text-2xl font-bold text-white mb-6">Selecciona tu Asiento</h2>

            <!-- Sector Tabs -->
            <div id="sectores-tabs" class="flex gap-2 mb-6 overflow-x-auto pb-2">
                <!-- Tabs will be inserted here -->
            </div>

            <!-- Seats Grid -->
            <div id="asientos-container" class="bg-slate-800 rounded-lg border border-slate-700 p-8">
                <!-- Seats will be inserted here -->
            </div>

            <!-- Legend -->
            <div class="mt-8 flex gap-6 flex-wrap text-sm">
                <div class="flex items-center gap-2">
                    <div class="w-6 h-6 bg-slate-600 rounded border border-slate-500"></div>
                    <span class="text-slate-300">Disponible</span>
                </div>
                <div class="flex items-center gap-2">
                    <div class="w-6 h-6 bg-yellow-600 rounded border border-yellow-500"></div>
                    <span class="text-slate-300">Reservado</span>
                </div>
                <div class="flex items-center gap-2">
                    <div class="w-6 h-6 bg-red-600 rounded border border-red-500"></div>
                    <span class="text-slate-300">Vendido</span>
                </div>
                <div class="flex items-center gap-2">
                    <div class="w-6 h-6 bg-green-600 rounded border border-green-500"></div>
                    <span class="text-slate-300">Seleccionado</span>
                </div>
            </div>
        </div>

        <!-- Selected Seats Summary -->
        @auth
        <div class="fixed bottom-0 left-0 right-0 bg-slate-900 border-t border-slate-700 p-6">
            <div class="max-w-7xl mx-auto flex justify-between items-center">
                <div>
                    <p class="text-slate-400 text-sm">Asientos seleccionados: <span id="selected-count" class="text-red-400 font-bold">0</span></p>
                    <p class="text-white text-lg font-bold">Total: <span id="total-price" class="text-red-400">€0.00</span></p>
                </div>
                <button onclick="agregarAlCarrito()" class="px-8 py-3 bg-red-600 hover:bg-red-700 text-white font-bold rounded-lg transition duration-200">
                    🛒 Agregar al Carrito
                </button>
            </div>
        </div>
        <div class="pb-24"></div>
        @else
        <div class="text-center py-8 bg-red-900/20 border border-red-700 rounded-lg">
            <p class="text-slate-300 mb-4">Debes estar registrado para comprar entradas</p>
            <a href="{{ route('login') }}" class="inline-block px-6 py-2 bg-red-600 hover:bg-red-700 text-white font-semibold rounded-lg transition">
                Ingresar
            </a>
        </div>
        @endauth
    </div>

    <!-- Error Message -->
    <div id="error-message" class="hidden p-4 rounded-lg bg-red-950/40 border-l-4 border-red-500 text-red-200">
        <p id="error-text"></p>
    </div>
</div>

@endsection

@section('scripts')
<script>
    const eventoId = {{ isset($id) ? $id : 'null' }};
    let eventoData = null;
    let sectorActual = null;
    let asientosSeleccionados = {};

    async function cargarEvento() {
        try {
            const response = await fetch(`/api/eventos/${eventoId}`);
            const data = await response.json();

            if (!response.ok) {
                throw new Error(data.message || 'Error al cargar evento');
            }

            eventoData = data.data;
            renderizarEvento();
            cargarAsientos();

        } catch (error) {
            console.error('Error:', error);
            document.getElementById('loading').classList.add('hidden');
            document.getElementById('error-message').classList.remove('hidden');
            document.getElementById('error-text').textContent = error.message;
        }
    }

    function renderizarEvento() {
        const evento = eventoData.evento;

        // Poster
        const posterDiv = document.getElementById('evento-poster');
        if (evento.poster_url) {
            posterDiv.innerHTML = `<img src="${evento.poster_url}" alt="${evento.nombre}" class="w-full h-full object-cover">`;
        } else {
            posterDiv.innerHTML = '<div class="w-full h-full flex items-center justify-center text-6xl bg-gradient-to-br from-red-900 to-red-800">🎭</div>';
        }

        // Información del evento
        const infoDiv = document.getElementById('evento-info');
        infoDiv.innerHTML = `
            <h1 class="text-4xl font-bold text-white">${evento.nombre}</h1>
            <p class="text-slate-400 text-lg">${evento.descripcion_corta}</p>
            <div class="mt-6 space-y-3 text-slate-300">
                <p class="text-base"><span class="font-semibold">📅 Fecha:</span> ${new Date(evento.fecha).toLocaleDateString('es-ES', { year: 'numeric', month: 'long', day: 'numeric' })}</p>
                <p class="text-base"><span class="font-semibold">🕐 Hora:</span> ${evento.hora}</p>
            </div>
            <div class="mt-6 p-4 bg-slate-700/50 rounded-lg">
                <p class="text-slate-300">${evento.descripcion_larga}</p>
            </div>
        `;

        // Stats
        const statsDiv = document.getElementById('evento-stats');
        statsDiv.innerHTML = `
            <div class="text-center pb-4 border-b border-slate-600">
                <p class="text-slate-400 text-sm mb-1">Asientos Disponibles</p>
                <p class="text-3xl font-bold text-green-400">${eventoData.asientos_disponibles}</p>
            </div>
            <div class="text-center pb-4 border-b border-slate-600">
                <p class="text-slate-400 text-sm mb-1">Entradas Vendidas</p>
                <p class="text-3xl font-bold text-red-400">${eventoData.entradas_vendidas}</p>
            </div>
            <div class="text-center">
                <p class="text-slate-400 text-sm mb-1">Sectores Disponibles</p>
                <p class="text-3xl font-bold text-blue-400">${eventoData.sectores_disponibles.length}</p>
            </div>
        `;

        // Sectores tabs
        const tabsDiv = document.getElementById('sectores-tabs');
        tabsDiv.innerHTML = eventoData.sectores_disponibles.map((sector, index) => `
            <button
                onclick="cambiarSector(${sector.id}, '${sector.nombre}')"
                class="sector-tab px-4 py-2 rounded-lg font-semibold transition duration-200 whitespace-nowrap ${index === 0 ? 'bg-red-600 text-white' : 'bg-slate-700 text-slate-300 hover:bg-slate-600'}"
                data-sector-id="${sector.id}"
            >
                ${sector.nombre}
            </button>
        `).join('');

        // Cargar primer sector
        if (eventoData.sectores_disponibles.length > 0) {
            sectorActual = eventoData.sectores_disponibles[0].id;
            cargarAsientos();
        }

        document.getElementById('loading').classList.add('hidden');
        document.getElementById('evento-container').classList.remove('hidden');
    }

    function cambiarSector(sectorId, sectorNombre) {
        sectorActual = sectorId;

        // Actualizar tabs
        document.querySelectorAll('.sector-tab').forEach(tab => {
            if (parseInt(tab.dataset.sectorId) === sectorId) {
                tab.classList.remove('bg-slate-700', 'text-slate-300');
                tab.classList.add('bg-red-600', 'text-white');
            } else {
                tab.classList.remove('bg-red-600', 'text-white');
                tab.classList.add('bg-slate-700', 'text-slate-300');
            }
        });

        cargarAsientos();
    }

    async function cargarAsientos() {
        try {
            const response = await fetch(`/api/eventos/${eventoId}/sectores/${sectorActual}/asientos`);
            const data = await response.json();

            if (!response.ok) {
                throw new Error(data.message || 'Error al cargar asientos');
            }

            renderizarAsientos(data.data);

        } catch (error) {
            console.error('Error:', error);
        }
    }

    function renderizarAsientos(asientos) {
        const container = document.getElementById('asientos-container');

        // Agrupar por fila
        const filasMap = {};
        asientos.forEach(asiento => {
            if (!filasMap[asiento.fila]) {
                filasMap[asiento.fila] = [];
            }
            filasMap[asiento.fila].push(asiento);
        });

        const filas = Object.keys(filasMap).sort();

        let html = '<div class="space-y-4">';

        filas.forEach(fila => {
            html += `<div class="flex gap-2 items-center">
                <span class="w-8 text-center font-semibold text-slate-400 text-sm">Fila ${fila}</span>
                <div class="flex gap-2 flex-wrap">`;

            filasMap[fila].sort((a, b) => a.numero - b.numero).forEach(asiento => {
                const estado = asiento.estado; // 'disponible', 'bloqueado', 'vendido'
                const key = `${asiento.id}-${eventoId}`;
                const isSelected = asientosSeleccionados[key];

                let colorClass = 'bg-slate-600 border-slate-500 hover:bg-slate-500 cursor-pointer';
                if (estado === 'vendido') {
                    colorClass = 'bg-red-600 border-red-500 cursor-not-allowed opacity-50';
                } else if (estado === 'bloqueado') {
                    colorClass = 'bg-yellow-600 border-yellow-500 cursor-not-allowed';
                } else if (isSelected) {
                    colorClass = 'bg-green-600 border-green-500 cursor-pointer';
                }

                html += `
                    <button
                        class="asiento w-6 h-6 rounded border transition duration-200 ${colorClass} text-xs flex items-center justify-center font-bold"
                        data-asiento-id="${asiento.id}"
                        data-sector-id="${sectorActual}"
                        data-fila="${fila}"
                        data-numero="${asiento.numero}"
                        data-precio="${asiento.precio}"
                        onclick="toggleAsiento(event)"
                        ${estado === 'vendido' || estado === 'bloqueado' ? 'disabled' : ''}
                        title="${asiento.precio}€"
                    >
                        ${asiento.numero}
                    </button>
                `;
            });

            html += '</div></div>';
        });

        html += '</div>';
        container.innerHTML = html;
    }

    function toggleAsiento(event) {
        event.preventDefault();
        const button = event.target.closest('button.asiento');
        if (!button || button.disabled) return;

        const asientoId = button.dataset.asientoId;
        const key = `${asientoId}-${eventoId}`;

        if (asientosSeleccionados[key]) {
            delete asientosSeleccionados[key];
            button.classList.remove('bg-green-600', 'border-green-500');
            button.classList.add('bg-slate-600', 'border-slate-500');
        } else {
            asientosSeleccionados[key] = {
                asiento_id: asientoId,
                evento_id: eventoId,
                precio: parseFloat(button.dataset.precio),
                descripcion: `Fila ${button.dataset.fila} - Asiento ${button.dataset.numero}`
            };
            button.classList.remove('bg-slate-600', 'border-slate-500');
            button.classList.add('bg-green-600', 'border-green-500');
        }

        actualizarResumen();
    }

    function actualizarResumen() {
        const count = Object.keys(asientosSeleccionados).length;
        const total = Object.values(asientosSeleccionados).reduce((sum, item) => sum + item.precio, 0);

        document.getElementById('selected-count').textContent = count;
        document.getElementById('total-price').textContent = `€${total.toFixed(2)}`;
    }

    async function agregarAlCarrito() {
        if (Object.keys(asientosSeleccionados).length === 0) {
            alert('Por favor selecciona al menos un asiento');
            return;
        }

        try {
            const reservas = [];

            for (const [key, asiento] of Object.entries(asientosSeleccionados)) {
                const response = await fetch('/api/reservas', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Authorization': `Bearer ${localStorage.getItem('token')}`
                    },
                    body: JSON.stringify({
                        evento_id: asiento.evento_id,
                        asiento_id: asiento.asiento_id
                    })
                });

                if (!response.ok) {
                    const error = await response.json();
                    throw new Error(error.error || 'Error al reservar asiento');
                }

                const data = await response.json();
                reservas.push(data.data);
            }

            // Guardar en localStorage y redirigir
            let carrito = JSON.parse(localStorage.getItem('reservas') || '[]');
            carrito = carrito.concat(reservas.map(r => ({
                id: r.id,
                evento_id: r.evento_id,
                asiento_id: r.asiento_id,
                precio: Object.values(asientosSeleccionados).find(a => a.asiento_id === r.asiento_id)?.precio || 0
            })));
            localStorage.setItem('reservas', JSON.stringify(carrito));

            window.dispatchEvent(new Event('storage'));

            alert('✅ Asientos añadidos al carrito');
            asientosSeleccionados = {};
            cargarAsientos();
            actualizarResumen();

        } catch (error) {
            console.error('Error:', error);
            alert('❌ Error: ' + error.message);
        }
    }

    // Cargar evento al iniciar
    if (eventoId) {
        cargarEvento();
    } else {
        document.getElementById('loading').classList.add('hidden');
        document.getElementById('error-message').classList.remove('hidden');
        document.getElementById('error-text').textContent = 'ID de evento no válido';
    }
</script>
@endsection
