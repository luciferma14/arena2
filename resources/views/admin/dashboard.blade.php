@extends('layouts.app')

@section('title', 'Panel Admin - Roig Arena')

@section('content')
<div class="space-y-8">
    <!-- Header -->
    <div class="flex justify-between items-center mb-12">
        <div>
            <h1 class="text-4xl font-bold mb-3 text-white">
                ⚙️ Panel de Administrador
            </h1>
            <p class="text-slate-400">Gestiona eventos, sectores y asientos</p>
        </div>
        <a href="{{ route('home') }}" class="px-6 py-3 bg-slate-700 hover:bg-slate-600 text-white font-semibold rounded-lg transition">
            ← Volver
        </a>
    </div>

    <!-- Tabs Navigation -->
    <div class="flex gap-4 border-b border-slate-700 mb-8 overflow-x-auto">
        <button onclick="cambiarTab('eventos')" class="tab-btn px-6 py-3 font-semibold border-b-2 border-red-600 text-red-400 transition" data-tab="eventos">
            🎭 Eventos
        </button>
        <button onclick="cambiarTab('sectores')" class="tab-btn px-6 py-3 font-semibold border-b-2 border-transparent text-slate-400 hover:text-slate-300 transition" data-tab="sectores">
            🪑 Sectores
        </button>
        <button onclick="cambiarTab('asientos')" class="tab-btn px-6 py-3 font-semibold border-b-2 border-transparent text-slate-400 hover:text-slate-300 transition" data-tab="asientos">
            💺 Asientos
        </button>
    </div>

    <!-- EVENTOS TAB -->
    <div id="tab-eventos" class="tab-content">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <div class="bg-slate-800 rounded-lg border border-slate-700 p-6">
                <h3 class="text-xl font-bold text-white mb-6">Crear/Editar Evento</h3>
                <form id="evento-form" class="space-y-4" onsubmit="guardarEvento(event)">
                    <input type="hidden" id="evento-id">

                    <div>
                        <label class="block text-sm font-medium text-slate-300 mb-2">Nombre</label>
                        <input type="text" id="evento-nombre" required class="w-full px-3 py-2 bg-slate-700 border border-slate-600 rounded text-white focus:outline-none focus:border-red-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-300 mb-2">Descripción Corta</label>
                        <input type="text" id="evento-desc-corta" required class="w-full px-3 py-2 bg-slate-700 border border-slate-600 rounded text-white focus:outline-none focus:border-red-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-300 mb-2">Descripción Larga</label>
                        <textarea id="evento-desc-larga" required rows="3" class="w-full px-3 py-2 bg-slate-700 border border-slate-600 rounded text-white focus:outline-none focus:border-red-500"></textarea>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-300 mb-2">URL Poster</label>
                        <input type="url" id="evento-poster" class="w-full px-3 py-2 bg-slate-700 border border-slate-600 rounded text-white focus:outline-none focus:border-red-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-300 mb-2">Fecha</label>
                        <input type="date" id="evento-fecha" required class="w-full px-3 py-2 bg-slate-700 border border-slate-600 rounded text-white focus:outline-none focus:border-red-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-300 mb-2">Hora</label>
                        <input type="time" id="evento-hora" required class="w-full px-3 py-2 bg-slate-700 border border-slate-600 rounded text-white focus:outline-none focus:border-red-500">
                    </div>

                    <div class="flex gap-2">
                        <button type="submit" id="btn-guardar-evento" class="flex-1 py-2 bg-red-600 hover:bg-red-700 text-white font-bold rounded transition">
                            ✅ Guardar
                        </button>
                        <button type="button" onclick="limpiarEventoForm()" class="flex-1 py-2 bg-slate-700 hover:bg-slate-600 text-white font-bold rounded transition">
                            ✕ Limpiar
                        </button>
                    </div>
                </form>
            </div>

            <div class="lg:col-span-2">
                <div id="eventos-list" class="space-y-4"></div>
            </div>
        </div>
    </div>

    <!-- SECTORES TAB -->
    <div id="tab-sectores" class="tab-content hidden">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <div class="bg-slate-800 rounded-lg border border-slate-700 p-6">
                <h3 class="text-xl font-bold text-white mb-6">Crear/Editar Sector</h3>
                <form id="sector-form" class="space-y-4" onsubmit="guardarSector(event)">
                    <input type="hidden" id="sector-id">

                    <div>
                        <label class="block text-sm font-medium text-slate-300 mb-2">Nombre</label>
                        <input type="text" id="sector-nombre" required class="w-full px-3 py-2 bg-slate-700 border border-slate-600 rounded text-white focus:outline-none focus:border-red-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-300 mb-2">Descripción</label>
                        <input type="text" id="sector-descripcion" class="w-full px-3 py-2 bg-slate-700 border border-slate-600 rounded text-white focus:outline-none focus:border-red-500">
                    </div>

                    <div class="flex gap-2">
                        <button type="submit" id="btn-guardar-sector" class="flex-1 py-2 bg-red-600 hover:bg-red-700 text-white font-bold rounded transition">
                            ✅ Guardar
                        </button>
                        <button type="button" onclick="limpiarSectorForm()" class="flex-1 py-2 bg-slate-700 hover:bg-slate-600 text-white font-bold rounded transition">
                            ✕ Limpiar
                        </button>
                    </div>
                </form>
            </div>

            <div class="lg:col-span-2">
                <div id="sectores-list" class="space-y-4"></div>
            </div>
        </div>
    </div>

    <!-- ASIENTOS TAB -->
    <div id="tab-asientos" class="tab-content hidden">
        <div class="bg-slate-800 rounded-lg border border-slate-700 p-6">
            <h3 class="text-xl font-bold text-white mb-6">Gestionar Asientos</h3>
            <p class="text-slate-400">Los asientos se crean automáticamente cuando creas un evento con sectores.</p>

            <div class="mt-6 space-y-4">
                <div>
                    <label class="block text-sm font-medium text-slate-300 mb-2">Selecciona un Sector</label>
                    <select id="sector-selector" onchange="cargarAsientos()" class="w-full px-3 py-2 bg-slate-700 border border-slate-600 rounded text-white focus:outline-none focus:border-red-500">
                        <option value="">-- Selecciona un sector --</option>
                    </select>
                </div>

                <div id="asientos-info" class="hidden mt-6 p-4 bg-blue-900/20 border border-blue-600/30 rounded-lg">
                    <div id="asientos-stats" class="grid grid-cols-3 gap-4 text-center"></div>
                </div>

                <div id="asientos-list" class="mt-6 space-y-3"></div>
            </div>
        </div>
    </div>

    <!-- Error Message -->
    <div id="error-message" class="hidden p-4 rounded-lg bg-red-950/40 border-l-4 border-red-500 text-red-200">
        <p id="error-text"></p>
    </div>
</div>
@endsection

@section('scripts')
<script>
    function cambiarTab(tab) {
        document.querySelectorAll('.tab-content').forEach(el => el.classList.add('hidden'));
        document.querySelectorAll('.tab-btn').forEach(el => {
            el.classList.remove('border-red-600', 'text-red-400');
            el.classList.add('border-transparent', 'text-slate-400');
        });

        document.getElementById(`tab-${tab}`).classList.remove('hidden');
        const btn = document.querySelector(`[data-tab="${tab}"]`);
        btn.classList.remove('border-transparent', 'text-slate-400');
        btn.classList.add('border-red-600', 'text-red-400');

        if (tab === 'eventos') cargarEventos();
        if (tab === 'sectores') cargarSectores();
        if (tab === 'asientos') cargarSectorSelector();
    }

    // ===== EVENTOS =====
    async function cargarEventos() {
        try {
            const response = await fetch('/api/eventos');
            const data = await response.json();
            // La API devuelve data.data con array de eventos
            const eventos = data.data;

            const lista = document.getElementById('eventos-list');
            lista.innerHTML = eventos.map(evento => `
                <div class="bg-slate-700 rounded-lg p-4 hover:bg-slate-600 transition">
                    <div class="flex justify-between items-start mb-3">
                        <div>
                            <h4 class="font-bold text-white">${evento.nombre}</h4>
                            <p class="text-sm text-slate-400">📅 ${new Date(evento.fecha).toLocaleDateString('es-ES')}</p>
                        </div>
                        <div class="flex gap-2">
                            <button onclick="editarEvento(${evento.id})" class="px-3 py-1 bg-blue-600 hover:bg-blue-700 text-white text-sm rounded transition">
                                ✏️ Editar
                            </button>
                            <button onclick="eliminarEvento(${evento.id})" class="px-3 py-1 bg-red-600 hover:bg-red-700 text-white text-sm rounded transition">
                                🗑️ Eliminar
                            </button>
                        </div>
                    </div>
                    <p class="text-sm text-slate-300">${evento.descripcion_corta}</p>
                </div>
            `).join('');
        } catch (error) {
            mostrarError('Error al cargar eventos: ' + error.message);
        }
    }

    function editarEvento(id) {
        fetch(`/api/eventos/${id}`)
            .then(r => r.json())
            .then(data => {
                // ✅ CORREGIDO: la API devuelve data.data.evento
                const evento = data.data.evento;

                document.getElementById('evento-id').value = evento.id;
                document.getElementById('evento-nombre').value = evento.nombre ?? '';
                document.getElementById('evento-desc-corta').value = evento.descripcion_corta ?? '';
                document.getElementById('evento-desc-larga').value = evento.descripcion_larga ?? '';
                document.getElementById('evento-poster').value = evento.poster_url ?? '';
                // La fecha viene como "2026-06-15T00:00:00.000000Z", hay que recortarla
                document.getElementById('evento-fecha').value = evento.fecha ? evento.fecha.substring(0, 10) : '';
                document.getElementById('evento-hora').value = evento.hora ?? '';

                document.getElementById('btn-guardar-evento').textContent = '✏️ Actualizar';
            })
            .catch(error => mostrarError('Error al cargar evento: ' + error.message));
    }

    function limpiarEventoForm() {
        document.getElementById('evento-form').reset();
        document.getElementById('evento-id').value = '';
        document.getElementById('btn-guardar-evento').textContent = '✅ Guardar';
    }

    function guardarEvento(e) {
        e.preventDefault();
        const id = document.getElementById('evento-id').value;
        const metodo = id ? 'PUT' : 'POST';
        const url = id ? `/api/admin/eventos/${id}` : '/api/admin/eventos';

        const evento = {
            nombre: document.getElementById('evento-nombre').value,
            descripcion_corta: document.getElementById('evento-desc-corta').value,
            descripcion_larga: document.getElementById('evento-desc-larga').value,
            poster_url: document.getElementById('evento-poster').value,
            fecha: document.getElementById('evento-fecha').value,
            hora: document.getElementById('evento-hora').value,
        };

        fetch(url, {
            method: metodo,
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            },
            body: JSON.stringify(evento)
        })
        .then(r => r.json())
        .then(data => {
            if (data.data) {
                alert('✅ ' + (id ? 'Evento actualizado' : 'Evento creado'));
                limpiarEventoForm();
                cargarEventos();
            } else {
                throw new Error(data.message || data.error || 'Error desconocido');
            }
        })
        .catch(error => mostrarError('Error: ' + error.message));
    }

    function eliminarEvento(id) {
        if (!confirm('¿Estás seguro de que deseas eliminar este evento?')) return;

        fetch(`/api/admin/eventos/${id}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            }
        })
        .then(r => r.json())
        .then(() => {
            alert('✅ Evento eliminado');
            cargarEventos();
        })
        .catch(error => mostrarError('Error: ' + error.message));
    }

    // ===== SECTORES =====
    async function cargarSectores() {
        try {
            const response = await fetch('/api/sectores');
            const data = await response.json();
            const sectores = data.data;

            const lista = document.getElementById('sectores-list');
            lista.innerHTML = sectores.map(sector => `
                <div class="bg-slate-700 rounded-lg p-4 hover:bg-slate-600 transition">
                    <div class="flex justify-between items-start mb-2">
                        <h4 class="font-bold text-white">${sector.nombre}</h4>
                        <div class="flex gap-2">
                            <button onclick="editarSector(${sector.id})" class="px-3 py-1 bg-blue-600 hover:bg-blue-700 text-white text-sm rounded transition">
                                ✏️ Editar
                            </button>
                            <button onclick="eliminarSector(${sector.id})" class="px-3 py-1 bg-red-600 hover:bg-red-700 text-white text-sm rounded transition">
                                🗑️ Eliminar
                            </button>
                        </div>
                    </div>
                    <p class="text-sm text-slate-400">${sector.descripcion || 'Sin descripción'}</p>
                </div>
            `).join('');
        } catch (error) {
            mostrarError('Error al cargar sectores: ' + error.message);
        }
    }

    function editarSector(id) {
        fetch(`/api/sectores/${id}`)
            .then(r => r.json())
            .then(data => {
                // Ajusta según lo que devuelva tu API para un sector individual
                const sector = data.data.sector ?? data.data;

                document.getElementById('sector-id').value = sector.id;
                document.getElementById('sector-nombre').value = sector.nombre ?? '';
                document.getElementById('sector-descripcion').value = sector.descripcion ?? '';

                document.getElementById('btn-guardar-sector').textContent = '✏️ Actualizar';
            })
            .catch(error => mostrarError('Error al cargar sector: ' + error.message));
    }

    function limpiarSectorForm() {
        document.getElementById('sector-form').reset();
        document.getElementById('sector-id').value = '';
        document.getElementById('btn-guardar-sector').textContent = '✅ Guardar';
    }

    function guardarSector(e) {
        e.preventDefault();
        const id = document.getElementById('sector-id').value;
        const metodo = id ? 'PUT' : 'POST';
        const url = id ? `/api/admin/sectores/${id}` : '/api/admin/sectores';

        const sector = {
            nombre: document.getElementById('sector-nombre').value,
            descripcion: document.getElementById('sector-descripcion').value,
        };

        fetch(url, {
            method: metodo,
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            },
            body: JSON.stringify(sector)
        })
        .then(r => r.json())
        .then(data => {
            if (data.data) {
                alert('✅ ' + (id ? 'Sector actualizado' : 'Sector creado'));
                limpiarSectorForm();
                cargarSectores();
            } else {
                throw new Error(data.message || data.error || 'Error desconocido');
            }
        })
        .catch(error => mostrarError('Error: ' + error.message));
    }

    function eliminarSector(id) {
        if (!confirm('¿Estás seguro de que deseas eliminar este sector?')) return;

        fetch(`/api/admin/sectores/${id}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            }
        })
        .then(r => r.json())
        .then(() => {
            alert('✅ Sector eliminado');
            cargarSectores();
        })
        .catch(error => mostrarError('Error: ' + error.message));
    }

    // ===== ASIENTOS =====
    async function cargarSectorSelector() {
        try {
            const response = await fetch('/api/sectores');
            const data = await response.json();
            const sectores = data.data;

            const selector = document.getElementById('sector-selector');
            selector.innerHTML = '<option value="">-- Selecciona un sector --</option>' +
                sectores.map(s => `<option value="${s.id}">${s.nombre}</option>`).join('');
        } catch (error) {
            mostrarError('Error al cargar sectores: ' + error.message);
        }
    }

    async function cargarAsientos() {
        const sectorId = document.getElementById('sector-selector').value;
        if (!sectorId) return;

        try {
            const response = await fetch(`/api/sectores/${sectorId}`);
            const data = await response.json();
            // Ajusta según lo que devuelva tu API
            const sector = data.data.sector ?? data.data;
            const asientos = sector.asientos || [];

            const info = document.getElementById('asientos-info');
            const stats = document.getElementById('asientos-stats');
            const lista = document.getElementById('asientos-list');

            const disponibles = asientos.filter(a => a.estado === 'disponible').length;
            const vendidos = asientos.filter(a => a.estado === 'vendido').length;

            stats.innerHTML = `
                <div>
                    <p class="text-slate-400 text-sm mb-1">Total Asientos</p>
                    <p class="text-2xl font-bold text-white">${asientos.length}</p>
                </div>
                <div>
                    <p class="text-slate-400 text-sm mb-1">Disponibles</p>
                    <p class="text-2xl font-bold text-green-400">${disponibles}</p>
                </div>
                <div>
                    <p class="text-slate-400 text-sm mb-1">Vendidos</p>
                    <p class="text-2xl font-bold text-red-400">${vendidos}</p>
                </div>
            `;

            lista.innerHTML = asientos.map(asiento => `
                <div class="flex justify-between items-center bg-slate-700 p-3 rounded">
                    <span class="text-white">Fila ${asiento.fila ?? '?'} - Asiento ${asiento.numero ?? asiento.numero_asiento ?? '?'}</span>
                    <span class="px-2 py-1 rounded text-xs font-semibold ${
                        asiento.estado === 'disponible' ? 'bg-green-900 text-green-300' :
                        asiento.estado === 'vendido'    ? 'bg-red-900 text-red-300' :
                                                          'bg-yellow-900 text-yellow-300'
                    }">${asiento.estado ?? 'desconocido'}</span>
                </div>
            `).join('');

            info.classList.remove('hidden');
        } catch (error) {
            mostrarError('Error al cargar asientos: ' + error.message);
        }
    }

    function mostrarError(mensaje) {
        const errorDiv = document.getElementById('error-message');
        document.getElementById('error-text').textContent = mensaje;
        errorDiv.classList.remove('hidden');
        setTimeout(() => errorDiv.classList.add('hidden'), 5000);
    }

    // Cargar eventos al iniciar
    cargarEventos();
</script>
@endsection
