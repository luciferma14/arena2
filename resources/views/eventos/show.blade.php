@extends('layouts.app')

@section('title', 'Evento - Roig Arena')

@section('content')
<div>
    {{-- Loading --}}
    <div id="loading" class="flex flex-col items-center justify-center py-24">
        <div class="w-10 h-10 border-4 border-red-600 border-t-transparent rounded-full animate-spin mb-4"></div>
        <p class="text-slate-400">Cargando evento...</p>
    </div>

    {{-- Contenido principal --}}
    <div id="evento-container" class="hidden">

        {{-- Header del evento --}}
        <div class="mb-8">
            <a href="{{ route('home') }}" class="inline-flex items-center gap-2 text-slate-400 hover:text-red-400 transition text-sm mb-6">
                ← Volver a eventos
            </a>
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                {{-- Poster + Info --}}
                <div class="lg:col-span-2 space-y-6">
                    <div id="evento-poster" class="w-full h-72 bg-slate-800 rounded-xl overflow-hidden border border-slate-700"></div>
                    <div id="evento-info"></div>
                </div>
                {{-- Stats --}}
                <div class="bg-slate-800 rounded-xl border border-slate-700 p-6 h-fit sticky top-24 space-y-5">
                    <div id="evento-stats"></div>
                </div>
            </div>
        </div>

        {{-- Selector de asientos --}}
        <div class="mt-10">
            <h2 class="text-2xl font-bold text-white mb-6">🪑 Selecciona tu Asiento</h2>

            <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">

                {{-- Columna izquierda: sectores --}}
                <div class="lg:col-span-1">
                    <p class="text-xs font-semibold uppercase tracking-widest text-slate-500 mb-3">Sectores</p>
                    <div id="sectores-lista" class="space-y-2"></div>

                    {{-- Info del sector seleccionado --}}
                    <div id="sector-info" class="hidden mt-6 p-4 bg-slate-800 border border-slate-700 rounded-xl text-sm space-y-2">
                        <p class="text-slate-400">Precio por asiento</p>
                        <p id="sector-precio" class="text-2xl font-bold text-red-400"></p>
                    </div>
                </div>

                {{-- Columna derecha: asientos --}}
                <div class="lg:col-span-3">
                    {{-- Escenario --}}
                    <div class="w-full bg-gradient-to-b from-slate-600 to-slate-700 rounded-lg py-2 text-center text-xs font-bold tracking-widest uppercase text-slate-300 mb-6 border border-slate-500">
                        🎤 Escenario / Cancha
                    </div>

                    {{-- Grid de asientos --}}
                    <div id="asientos-container" class="bg-slate-800 rounded-xl border border-slate-700 p-6 min-h-48">
                        <p class="text-slate-500 text-center py-12">Selecciona un sector para ver los asientos</p>
                    </div>

                    {{-- Leyenda --}}
                    <div class="mt-4 flex flex-wrap gap-5 text-sm">
                        <div class="flex items-center gap-2">
                            <div class="w-5 h-5 rounded bg-slate-600 border border-slate-500"></div>
                            <span class="text-slate-400">Disponible</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <div class="w-5 h-5 rounded bg-yellow-600 border border-yellow-500"></div>
                            <span class="text-slate-400">Reservado</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <div class="w-5 h-5 rounded bg-red-700 border border-red-600 opacity-60"></div>
                            <span class="text-slate-400">Vendido</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <div class="w-5 h-5 rounded bg-green-500 border border-green-400"></div>
                            <span class="text-slate-400">Seleccionado</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Barra de compra fija abajo --}}
        @auth('sanctum')
        <div class="fixed bottom-0 left-0 right-0 bg-slate-900/95 backdrop-blur border-t border-slate-700 z-50 py-4 px-6">
            <div class="max-w-7xl mx-auto flex justify-between items-center gap-4">
                <div>
                    <p class="text-slate-400 text-sm">Asientos seleccionados: <span id="selected-count" class="text-white font-bold">0</span></p>
                    <p class="text-xl font-bold text-white">Total: <span id="total-price" class="text-red-400">€0.00</span></p>
                </div>
                <button onclick="agregarAlCarrito()" class="px-8 py-3 bg-red-600 hover:bg-red-700 active:scale-95 text-white font-bold rounded-lg transition-all duration-150">
                    🛒 Agregar al Carrito
                </button>
            </div>
        </div>
        <div class="pb-24"></div>
        @else
        <div class="mt-10 text-center py-8 bg-red-900/20 border border-red-700/50 rounded-xl">
            <p class="text-slate-300 mb-4">Debes iniciar sesión para comprar entradas</p>
            <a href="{{ route('login') }}" class="inline-block px-6 py-2 bg-red-600 hover:bg-red-700 text-white font-semibold rounded-lg transition">
                Iniciar Sesión
            </a>
        </div>
        @endauth
    </div>

    {{-- Error --}}
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
    let precioSectorActual = 0;
    let asientosSeleccionados = {}; // { asientoId: { ...datos } }

    // ─── Cargar evento ───────────────────────────────────────────
    async function cargarEvento() {
        try {
            const res = await fetch(`/api/eventos/${eventoId}`);
            const data = await res.json();
            if (!res.ok) throw new Error(data.message || 'Error al cargar evento');
            eventoData = data.data;
            renderizarEvento();
        } catch (err) {
            document.getElementById('loading').classList.add('hidden');
            mostrarError(err.message);
        }
    }

    function renderizarEvento() {
        const evento = eventoData.evento;

        // Poster
        const posterDiv = document.getElementById('evento-poster');
        posterDiv.innerHTML = evento.poster_url
            ? `<img src="${evento.poster_url}" alt="${evento.nombre}" class="w-full h-full object-cover">`
            : `<div class="w-full h-full flex items-center justify-center text-7xl bg-gradient-to-br from-red-900 to-slate-800">🎭</div>`;

        // Info
        document.getElementById('evento-info').innerHTML = `
            <h1 class="text-4xl font-bold text-white">${evento.nombre}</h1>
            <p class="text-slate-400 text-lg mt-2">${evento.descripcion_corta}</p>
            <div class="mt-4 flex flex-wrap gap-6 text-slate-300 text-sm">
                <span>📅 ${new Date(evento.fecha).toLocaleDateString('es-ES', { year:'numeric', month:'long', day:'numeric' })}</span>
                <span>🕐 ${evento.hora}</span>
            </div>
            <div class="mt-4 p-4 bg-slate-800 rounded-xl border border-slate-700">
                <p class="text-slate-300 text-sm leading-relaxed">${evento.descripcion_larga}</p>
            </div>
        `;

        // Stats
        document.getElementById('evento-stats').innerHTML = `
            <div class="text-center pb-4 border-b border-slate-700">
                <p class="text-slate-400 text-xs uppercase tracking-wider mb-1">Asientos Disponibles</p>
                <p class="text-3xl font-bold text-green-400">${eventoData.asientos_disponibles}</p>
            </div>
            <div class="text-center pb-4 border-b border-slate-700">
                <p class="text-slate-400 text-xs uppercase tracking-wider mb-1">Entradas Vendidas</p>
                <p class="text-3xl font-bold text-red-400">${eventoData.entradas_vendidas}</p>
            </div>
            <div class="text-center">
                <p class="text-slate-400 text-xs uppercase tracking-wider mb-1">Sectores Disponibles</p>
                <p class="text-3xl font-bold text-blue-400">${eventoData.sectores_disponibles.length}</p>
            </div>
        `;

        // Lista de sectores (columna lateral)
        const lista = document.getElementById('sectores-lista');
        lista.innerHTML = eventoData.sectores_disponibles.map((sector, i) => {
            // Buscar precio del sector desde evento.precios si existe
            const precioObj = evento.precios ? evento.precios.find(p => p.sector && p.sector.id === sector.id) : null;
            const precio = precioObj ? `€${parseFloat(precioObj.precio).toFixed(2)}` : '';
            return `
                <button
                    onclick="cambiarSector(${sector.id})"
                    class="sector-btn w-full text-left px-4 py-3 rounded-lg border transition-all duration-150 ${i === 0 ? 'bg-red-600 border-red-500 text-white' : 'bg-slate-800 border-slate-700 text-slate-300 hover:border-red-600 hover:text-white'}"
                    data-sector-id="${sector.id}"
                >
                    <span class="font-semibold block">${sector.nombre}</span>
                    ${precio ? `<span class="text-xs opacity-75">${precio} / asiento</span>` : ''}
                </button>
            `;
        }).join('');

        document.getElementById('loading').classList.add('hidden');
        document.getElementById('evento-container').classList.remove('hidden');

        // Cargar primer sector automáticamente
        if (eventoData.sectores_disponibles.length > 0) {
            cambiarSector(eventoData.sectores_disponibles[0].id);
        }
    }

    function cambiarSector(sectorId) {
        sectorActual = sectorId;

        // Resaltar botón activo
        document.querySelectorAll('.sector-btn').forEach(btn => {
            const activo = parseInt(btn.dataset.sectorId) === sectorId;
            btn.className = btn.className
                .replace(/bg-red-600|border-red-500|text-white|bg-slate-800|border-slate-700|text-slate-300/g, '').trim();
            if (activo) {
                btn.classList.add('bg-red-600', 'border-red-500', 'text-white');
            } else {
                btn.classList.add('bg-slate-800', 'border-slate-700', 'text-slate-300');
            }
        });

        cargarAsientos();
    }

    async function cargarAsientos() {
        if (!sectorActual) return;

        document.getElementById('asientos-container').innerHTML = `
            <div class="flex items-center justify-center py-12 gap-3 text-slate-400">
                <div class="w-5 h-5 border-2 border-red-500 border-t-transparent rounded-full animate-spin"></div>
                Cargando asientos...
            </div>
        `;

        try {
            const res = await fetch(`/api/eventos/${eventoId}/sectores/${sectorActual}/asientos`);
            const data = await res.json();
            if (!res.ok) throw new Error(data.error || 'Error al cargar asientos');

            // La API devuelve data.data = { sector, precio, asientos: [{id, fila, numero, disponible}] }
            const { asientos, precio } = data.data;
            precioSectorActual = parseFloat(precio) || 0;

            // Mostrar precio del sector
            const infoDiv = document.getElementById('sector-info');
            const precioEl = document.getElementById('sector-precio');
            if (precio) {
                precioEl.textContent = `€${precioSectorActual.toFixed(2)}`;
                infoDiv.classList.remove('hidden');
            } else {
                infoDiv.classList.add('hidden');
            }

            renderizarAsientos(asientos);
        } catch (err) {
            document.getElementById('asientos-container').innerHTML = `
                <p class="text-red-400 text-center py-8">❌ ${err.message}</p>
            `;
        }
    }

    function renderizarAsientos(asientos) {
        const container = document.getElementById('asientos-container');

        if (!asientos || asientos.length === 0) {
            container.innerHTML = `<p class="text-slate-500 text-center py-12">No hay asientos en este sector</p>`;
            return;
        }

        // Agrupar por fila
        const filasMap = {};
        asientos.forEach(a => {
            if (!filasMap[a.fila]) filasMap[a.fila] = [];
            filasMap[a.fila].push(a);
        });

        const filas = Object.keys(filasMap).sort((a, b) => a - b);

        let html = '<div class="space-y-3 overflow-x-auto">';

        filas.forEach(fila => {
            const asientosFila = filasMap[fila].sort((a, b) => a.numero - b.numero);

            html += `
                <div class="flex items-center gap-2 min-w-max">
                    <span class="w-14 shrink-0 text-right text-xs font-mono text-slate-500">F${fila}</span>
                    <div class="flex gap-1">
            `;

            asientosFila.forEach(asiento => {
                // La API devuelve disponible: true/false (no un campo 'estado')
                const seleccionado = !!asientosSeleccionados[asiento.id];
                let cls, disabled, title;

                if (seleccionado) {
                    cls = 'bg-green-500 border-green-400 text-white cursor-pointer hover:bg-green-400';
                    disabled = '';
                    title = `Fila ${fila} · Asiento ${asiento.numero} · SELECCIONADO`;
                } else if (asiento.disponible === true) {
                    cls = 'bg-slate-600 border-slate-500 text-slate-300 cursor-pointer hover:bg-slate-500 hover:border-red-400';
                    disabled = '';
                    title = `Fila ${fila} · Asiento ${asiento.numero} · €${precioSectorActual.toFixed(2)}`;
                } else if (asiento.disponible === false) {
                    // Puede ser bloqueado o vendido — la API no distingue en este endpoint
                    cls = 'bg-red-800 border-red-700 text-red-500 cursor-not-allowed opacity-50';
                    disabled = 'disabled';
                    title = `Fila ${fila} · Asiento ${asiento.numero} · No disponible`;
                } else {
                    cls = 'bg-yellow-700 border-yellow-600 text-yellow-300 cursor-not-allowed';
                    disabled = 'disabled';
                    title = `Fila ${fila} · Asiento ${asiento.numero} · Reservado`;
                }

                html += `
                    <button
                        class="asiento w-7 h-7 rounded border text-xs font-bold transition-all duration-100 ${cls}"
                        data-id="${asiento.id}"
                        data-fila="${fila}"
                        data-numero="${asiento.numero}"
                        onclick="toggleAsiento(this)"
                        title="${title}"
                        ${disabled}
                    >${asiento.numero}</button>
                `;
            });

            html += `</div></div>`;
        });

        html += '</div>';
        container.innerHTML = html;
    }

    function toggleAsiento(btn) {
        const id = btn.dataset.id;
        const fila = btn.dataset.fila;
        const numero = btn.dataset.numero;

        if (asientosSeleccionados[id]) {
            delete asientosSeleccionados[id];
            btn.classList.remove('bg-green-500', 'border-green-400', 'text-white', 'hover:bg-green-400');
            btn.classList.add('bg-slate-600', 'border-slate-500', 'text-slate-300', 'hover:bg-slate-500');
            btn.title = `Fila ${fila} · Asiento ${numero} · €${precioSectorActual.toFixed(2)}`;
        } else {
            asientosSeleccionados[id] = {
                asiento_id: id,
                evento_id: eventoId,
                sector_id: sectorActual,
                precio: precioSectorActual,
                descripcion: `Fila ${fila} - Asiento ${numero}`
            };
            btn.classList.remove('bg-slate-600', 'border-slate-500', 'text-slate-300', 'hover:bg-slate-500');
            btn.classList.add('bg-green-500', 'border-green-400', 'text-white', 'hover:bg-green-400');
            btn.title = `Fila ${fila} · Asiento ${numero} · SELECCIONADO`;
        }

        actualizarResumen();
    }

    function actualizarResumen() {
        const count = Object.keys(asientosSeleccionados).length;
        const total = Object.values(asientosSeleccionados).reduce((s, a) => s + a.precio, 0);
        document.getElementById('selected-count').textContent = count;
        document.getElementById('total-price').textContent = `€${total.toFixed(2)}`;
    }

    async function agregarAlCarrito() {
        if (Object.keys(asientosSeleccionados).length === 0) {
            alert('Selecciona al menos un asiento');
            return;
        }

        // Verificar sesión antes de intentar reservar
        const authCheck = await fetch('/api/user');
        if (!authCheck.ok) {
            window.location.href = '{{ route("login") }}';
            return;
        }

        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;

        try {
            const reservas = [];

            for (const [id, asiento] of Object.entries(asientosSeleccionados)) {
                const res = await fetch('/api/reservas', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                    },
                    body: JSON.stringify({
                        evento_id: asiento.evento_id,
                        asiento_id: asiento.asiento_id,
                    })
                });

                if (!res.ok) {
                    const err = await res.json();
                    throw new Error(err.error || `Error al reservar asiento ${asiento.descripcion}`);
                }

                const data = await res.json();
                reservas.push({ ...data.data, precio: asiento.precio });
            }

            alert(`✅ ${reservas.length} asiento(s) añadidos al carrito`);
            asientosSeleccionados = {};
            actualizarResumen();
            cargarAsientos();

        } catch (err) {
            alert('❌ ' + err.message);
        }
    }

    function mostrarError(msg) {
        document.getElementById('error-message').classList.remove('hidden');
        document.getElementById('error-text').textContent = msg;
    }

    // Iniciar
    if (eventoId) {
        cargarEvento();
    } else {
        document.getElementById('loading').classList.add('hidden');
        mostrarError('ID de evento no válido');
    }
</script>
@endsection
