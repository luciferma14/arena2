@extends('layouts.app')

@section('title', 'Evento — Roig Arena')

@section('content')

<a href="{{ route('eventos.index') }}" class="back-link">&#8592; Todos los eventos</a>

<div id="evento-loading" class="state-msg"><span class="spinner"></span></div>

<div id="evento-wrap" style="display:none">

    {{-- Cabecera del evento --}}
    <div id="evento-header" class="page-header"></div>

    <div class="event-detail-layout">

        {{-- Columna principal: sector + mapa de asientos --}}
        <div>
            <div id="evento-info"></div>

            <div class="sector-bar">
                <p class="sector-bar-title">Selecciona un sector</p>
                <select id="sector-select" class="form-control" style="max-width:320px">
                    <option value="">-- Elige sector --</option>
                </select>
                <p id="sector-precio" style="margin-top:10px;font-size:.85rem;color:var(--muted)"></p>
            </div>

            <div id="seat-map-wrap" class="seat-map-wrap" style="display:none">
                <div class="seat-map-stage">Escenario / Pista</div>
                <div id="seat-map" class="seat-map"></div>
                <div class="seat-legend">
                    <div class="seat-legend-item">
                        <div class="seat-legend-dot" style="border-color:var(--gold);background:transparent"></div>
                        Disponible
                    </div>
                    <div class="seat-legend-item">
                        <div class="seat-legend-dot" style="border-color:var(--border);background:var(--surface)"></div>
                        Ocupado
                    </div>
                    <div class="seat-legend-item">
                        <div class="seat-legend-dot" style="border-color:var(--gold);background:var(--gold)"></div>
                        Seleccionado
                    </div>
                </div>
            </div>

            <div id="seat-loading" style="display:none" class="state-msg"><span class="spinner"></span></div>
        </div>

        {{-- Carrito lateral --}}
        <div>
            <div class="cart-sidebar">
                <p class="cart-title">Tu seleccion</p>
                <div id="cart-items">
                    <p class="cart-empty">Ningún asiento seleccionado.</p>
                </div>
                <div id="cart-total-row" class="cart-total" style="display:none">
                    <span>Total</span>
                    <span id="cart-total" class="cart-total-amount">0,00 EUR</span>
                </div>
                <div style="margin-top:20px;display:flex;flex-direction:column;gap:10px">
                    <button id="btn-reservar" class="btn btn-gold btn-full" disabled>Reservar asientos</button>
                    <button id="btn-comprar" class="btn btn-success btn-full" style="display:none">Confirmar compra</button>
                    <button id="btn-cancelar" class="btn btn-ghost btn-full" style="display:none">Cancelar reserva</button>
                </div>
                <div id="cart-msg" style="margin-top:12px"></div>
            </div>
        </div>

    </div>
</div>

@endsection

@section('scripts')
<script>
const eventoId = {{ $id }};
let eventoData    = null;
let sectorActual  = null;
let precioPorSeat = 0;
let selectedSeats = [];   // [{asientoId, fila, numero}]
let reservaIds    = [];   // tras reservar, antes de comprar

// ── Inicializar ─────────────────────────────────────────
document.addEventListener('DOMContentLoaded', () => {
    cargarEvento();
    document.getElementById('sector-select').addEventListener('change', onSectorChange);
    document.getElementById('btn-reservar').addEventListener('click', handleReservar);
    document.getElementById('btn-comprar').addEventListener('click', handleComprar);
    document.getElementById('btn-cancelar').addEventListener('click', handleCancelar);
});

async function cargarEvento() {
    try {
        const res  = await fetch(`/api/eventos/${eventoId}`);
        const json = await res.json();
        eventoData = json.data;

        renderHeader();
        renderSectores();

        document.getElementById('evento-loading').style.display = 'none';
        document.getElementById('evento-wrap').style.display    = 'block';
    } catch (e) {
        document.getElementById('evento-loading').innerHTML =
            '<p class="state-msg-title">Error al cargar el evento</p>';
    }
}

function renderHeader() {
    const ev    = eventoData.evento;
    const fecha = ev.fecha ? fmtFecha(ev.fecha + 'T00:00:00') : '-';
    const hora  = fmtHora(ev.hora);

    const img = ev.poster_url
        ? `<img src="${ev.poster_url}" class="event-banner" alt="${ev.nombre}">`
        : `<div class="event-banner-placeholder">${ev.nombre.substring(0,2).toUpperCase()}</div>`;

    document.getElementById('evento-header').innerHTML = `
        <p class="page-header-label">Evento</p>
        <h1 class="page-header-title">${ev.nombre}</h1>
    `;

    document.getElementById('evento-info').innerHTML = `
        ${img}
        <div class="event-meta-row">
            <div class="event-meta-item">
                <p class="event-meta-label">Fecha</p>
                <p class="event-meta-value">${fecha}</p>
            </div>
            <div class="event-meta-item">
                <p class="event-meta-label">Hora</p>
                <p class="event-meta-value">${hora}</p>
            </div>
            <div class="event-meta-item">
                <p class="event-meta-label">Asientos disponibles</p>
                <p class="event-meta-value">${eventoData.asientos_disponibles ?? '-'}</p>
            </div>
        </div>
        <p class="event-desc">${ev.descripcion_larga || ev.descripcion_corta || ''}</p>
    `;
}

function renderSectores() {
    const sectores = eventoData.sectores_disponibles || [];
    const sel = document.getElementById('sector-select');
    sel.innerHTML = '<option value="">-- Elige sector --</option>';
    sectores.forEach(s => {
        const precio = s.pivot?.precio ?? s.precio ?? 0;
        const opt = document.createElement('option');
        opt.value       = s.id;
        opt.dataset.precio = precio;
        opt.textContent = `${s.nombre} — ${parseFloat(precio).toFixed(2)} EUR`;
        sel.appendChild(opt);
    });
}

async function onSectorChange() {
    const sel   = document.getElementById('sector-select');
    const sId   = sel.value;
    if (!sId) {
        document.getElementById('seat-map-wrap').style.display = 'none';
        return;
    }

    const opt     = sel.options[sel.selectedIndex];
    precioPorSeat = parseFloat(opt.dataset.precio) || 0;
    document.getElementById('sector-precio').textContent = `Precio por asiento: ${precioPorSeat.toFixed(2)} EUR`;

    // Resetear seleccion al cambiar de sector
    selectedSeats = [];
    reservaIds    = [];
    actualizarCarrito();
    resetBotones();

    document.getElementById('seat-loading').style.display    = 'block';
    document.getElementById('seat-map-wrap').style.display   = 'none';

    try {
        const res  = await fetch(`/api/eventos/${eventoId}/sectores/${sId}/asientos`);
        const json = await res.json();
        sectorActual = json.data;
        renderSeatMap(sectorActual.asientos || []);
        document.getElementById('seat-map-wrap').style.display = 'block';
    } catch(e) {
        setCartMsg('error', 'Error al cargar los asientos.');
    } finally {
        document.getElementById('seat-loading').style.display = 'none';
    }
}

function renderSeatMap(asientos) {
    const mapEl = document.getElementById('seat-map');
    mapEl.innerHTML = '';

    // Agrupar por fila
    const filas = {};
    asientos.forEach(a => {
        if (!filas[a.fila]) filas[a.fila] = [];
        filas[a.fila].push(a);
    });

    Object.keys(filas).sort().forEach(fila => {
        const rowEl = document.createElement('div');
        rowEl.className = 'seat-row';

        const label = document.createElement('span');
        label.className   = 'seat-row-label';
        label.textContent = fila;
        rowEl.appendChild(label);

        filas[fila].sort((a,b) => a.numero - b.numero).forEach(a => {
            const btn = document.createElement('button');
            btn.className   = `seat ${a.disponible ? 'seat-available' : 'seat-taken'}`;
            btn.textContent = a.numero;
            btn.title       = `Fila ${fila} — Asiento ${a.numero}`;
            btn.disabled    = !a.disponible;

            if (a.disponible) {
                btn.addEventListener('click', () => toggleSeat(a.id, fila, a.numero, btn));
            }
            rowEl.appendChild(btn);
        });

        mapEl.appendChild(rowEl);
    });
}

function toggleSeat(asientoId, fila, numero, btn) {
    if (reservaIds.length > 0) return; // ya reservado

    const idx = selectedSeats.findIndex(s => s.asientoId === asientoId);
    if (idx === -1) {
        selectedSeats.push({ asientoId, fila, numero });
        btn.classList.replace('seat-available', 'seat-selected');
    } else {
        selectedSeats.splice(idx, 1);
        btn.classList.replace('seat-selected', 'seat-available');
    }
    actualizarCarrito();
}

function actualizarCarrito() {
    const itemsEl    = document.getElementById('cart-items');
    const totalRowEl = document.getElementById('cart-total-row');
    const totalEl    = document.getElementById('cart-total');
    const btnRes     = document.getElementById('btn-reservar');

    if (selectedSeats.length === 0) {
        itemsEl.innerHTML  = '<p class="cart-empty">Ningun asiento seleccionado.</p>';
        totalRowEl.style.display = 'none';
        btnRes.disabled = true;
        return;
    }

    itemsEl.innerHTML = selectedSeats.map(s => `
        <div class="cart-item">
            <div>
                <div class="cart-item-info">Fila ${s.fila} &mdash; Asiento ${s.numero}</div>
            </div>
            <div class="cart-item-price">${precioPorSeat.toFixed(2)} EUR</div>
        </div>
    `).join('');

    const total = selectedSeats.length * precioPorSeat;
    totalEl.textContent      = `${total.toFixed(2)} EUR`;
    totalRowEl.style.display = 'flex';
    btnRes.disabled = false;
}

async function handleReservar() {
    const btn = document.getElementById('btn-reservar');
    btn.disabled = true;
    btn.textContent = 'Reservando...';
    setCartMsg('', '');
    reservaIds = [];

    for (const seat of selectedSeats) {
        try {
            const res  = await fetch('/api/reservas', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ asiento_id: seat.asientoId, evento_id: eventoId }),
            });
            const json = await res.json();
            if (!res.ok) throw new Error(json.error || json.message || 'Error al reservar');
            reservaIds.push(json.data.reserva_id);
        } catch (e) {
            // Cancelar las reservas ya hechas
            for (const rid of reservaIds) {
                await fetch(`/api/reservas/${rid}`, { method: 'DELETE' }).catch(() => {});
            }
            reservaIds = [];
            btn.disabled    = false;
            btn.textContent = 'Reservar asientos';
            setCartMsg('error', `Error: ${e.message}`);
            return;
        }
    }

    // Reserva OK
    btn.style.display = 'none';
    document.getElementById('btn-comprar').style.display   = 'block';
    document.getElementById('btn-cancelar').style.display  = 'block';
    setCartMsg('info', 'Asientos reservados durante 15 minutos. Confirma la compra.');
}

async function handleComprar() {
    @auth
    const btn = document.getElementById('btn-comprar');
    btn.disabled    = true;
    btn.textContent = 'Procesando...';

    try {
        const res  = await fetch('/api/compras', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ reserva_ids: reservaIds }),
        });
        const json = await res.json();
        if (!res.ok) throw new Error(json.error || json.message || 'Error al comprar');

        window.location.href = '/mis-entradas';
    } catch (e) {
        btn.disabled    = false;
        btn.textContent = 'Confirmar compra';
        setCartMsg('error', `Error: ${e.message}`);
    }
    @else
    window.location.href = '/login';
    @endauth
}

async function handleCancelar() {
    for (const rid of reservaIds) {
        await fetch(`/api/reservas/${rid}`, { method: 'DELETE' }).catch(() => {});
    }
    reservaIds    = [];
    selectedSeats = [];
    actualizarCarrito();
    resetBotones();

    // Recargar mapa de asientos
    const sId = document.getElementById('sector-select').value;
    if (sId) {
        const res  = await fetch(`/api/eventos/${eventoId}/sectores/${sId}/asientos`);
        const json = await res.json();
        renderSeatMap(json.data?.asientos || []);
    }
    setCartMsg('', '');
}

function resetBotones() {
    document.getElementById('btn-reservar').style.display  = 'block';
    document.getElementById('btn-reservar').disabled       = true;
    document.getElementById('btn-reservar').textContent    = 'Reservar asientos';
    document.getElementById('btn-comprar').style.display   = 'none';
    document.getElementById('btn-cancelar').style.display  = 'none';
}

function setCartMsg(type, msg) {
    const el = document.getElementById('cart-msg');
    if (!msg) { el.innerHTML = ''; return; }
    const cls = type === 'error' ? 'alert-error' : type === 'info' ? 'alert-info' : 'alert-success';
    el.innerHTML = `<div class="alert ${cls}">${msg}</div>`;
}
</script>
@endsection
