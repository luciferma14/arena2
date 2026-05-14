@extends('layouts.app')

@section('title', 'Evento — Roig Arena')

@section('content')

<a href="{{ route('eventos.index') }}" class="back-link">&#8592; Todos los eventos</a>

<div id="evento-loading" class="state-msg"><span class="spinner"></span></div>

<div id="evento-wrap" style="display:none">

    <div id="evento-header" class="page-header"></div>

    <div class="event-detail-layout">

        {{-- Columna principal --}}
        <div>
            <div id="evento-info"></div>

            {{-- Selector de sector --}}
            <div class="sector-bar">
                <p class="sector-bar-title">Selecciona un sector</p>
                <select id="sector-select" class="form-control" style="max-width:320px">
                    <option value="">-- Elige sector --</option>
                </select>
                <p id="sector-precio" style="margin-top:8px;font-size:.85rem;color:var(--muted)"></p>
            </div>

            {{-- Mapa de asientos --}}
            <div id="seat-section" style="display:none">
                <div class="seat-map-wrap">
                    <div class="seat-map-stage">Escenario / Pista</div>
                    <div id="seat-map"></div>
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
            </div>
        </div>

        {{-- Carrito lateral --}}
        <div>
            <div class="cart-sidebar">
                <p class="cart-title">Tu seleccion</p>
                <div id="cart-items">
                    <p class="cart-empty">Ningun asiento seleccionado.</p>
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
const eventoId    = {{ $id }};
let precioPorSeat = 0;
let selectedSeats = [];  // [{asientoId, fila, numero}]
let reservaIds    = [];  // IDs de reservas activas

// ── Init ────────────────────────────────────────────────
document.addEventListener('DOMContentLoaded', () => {
    cargarEvento();
    document.getElementById('sector-select').addEventListener('change', onSectorChange);
    document.getElementById('btn-reservar').addEventListener('click', handleReservar);
    document.getElementById('btn-comprar').addEventListener('click', handleComprar);
    document.getElementById('btn-cancelar').addEventListener('click', handleCancelar);
});

// ── Cargar evento ────────────────────────────────────────
async function cargarEvento() {
    try {
        const res  = await fetch('/api/eventos/' + eventoId);
        const json = await res.json();
        const d    = json.data;

        // Cabecera
        document.getElementById('evento-header').innerHTML =
            '<p class="page-header-label">Evento</p>' +
            '<h1 class="page-header-title">' + d.evento.nombre + '</h1>';

        // Info
        const img = d.evento.poster_url
            ? '<img src="' + d.evento.poster_url + '" class="event-banner" alt="' + d.evento.nombre + '">'
            : '<div class="event-banner-placeholder">' + d.evento.nombre.substring(0,2).toUpperCase() + '</div>';

        document.getElementById('evento-info').innerHTML =
            img +
            '<div class="event-meta-row">' +
                '<div class="event-meta-item"><p class="event-meta-label">Fecha</p><p class="event-meta-value">' + fmtFecha(d.evento.fecha) + '</p></div>' +
                '<div class="event-meta-item"><p class="event-meta-label">Hora</p><p class="event-meta-value">' + fmtHora(d.evento.hora) + '</p></div>' +
                '<div class="event-meta-item"><p class="event-meta-label">Disponibles</p><p class="event-meta-value">' + (d.asientos_disponibles ?? '-') + '</p></div>' +
            '</div>' +
            '<p class="event-desc">' + (d.evento.descripcion_larga || d.evento.descripcion_corta || '') + '</p>';

        // Sectores en el select
        var sel = document.getElementById('sector-select');
        (d.sectores_disponibles || []).forEach(function(s) {
            var precio = s.pivot && s.pivot.precio ? s.pivot.precio : (s.precio || 0);
            var opt = document.createElement('option');
            opt.value           = s.id;
            opt.dataset.precio  = precio;
            opt.textContent     = s.nombre + ' — ' + parseFloat(precio).toFixed(2) + ' EUR';
            sel.appendChild(opt);
        });

        document.getElementById('evento-loading').style.display = 'none';
        document.getElementById('evento-wrap').style.display    = 'block';
    } catch(e) {
        document.getElementById('evento-loading').innerHTML = '<p class="state-msg-title">Error al cargar el evento</p>';
    }
}

// ── Sector cambia ────────────────────────────────────────
async function onSectorChange() {
    var sId = document.getElementById('sector-select').value;
    document.getElementById('seat-section').style.display = 'none';

    if (!sId) return;

    var opt = document.getElementById('sector-select').options[document.getElementById('sector-select').selectedIndex];
    precioPorSeat = parseFloat(opt.dataset.precio) || 0;
    document.getElementById('sector-precio').textContent = 'Precio por asiento: ' + precioPorSeat.toFixed(2) + ' EUR';

    // Reset seleccion
    selectedSeats = [];
    reservaIds    = [];
    renderCarrito();
    resetBotones();

    document.getElementById('seat-map').innerHTML = '<div class="state-msg"><span class="spinner"></span></div>';
    document.getElementById('seat-section').style.display = 'block';

    try {
        var res  = await fetch('/api/eventos/' + eventoId + '/sectores/' + sId + '/asientos');
        var json = await res.json();
        renderMapa(json.data.asientos || []);
    } catch(e) {
        document.getElementById('seat-map').innerHTML = '<p class="state-msg">Error al cargar asientos.</p>';
    }
}

// ── Renderizar mapa ──────────────────────────────────────
function renderMapa(asientos) {
    var map = document.getElementById('seat-map');
    map.innerHTML = '';

    // Agrupar por fila
    var filas = {};
    asientos.forEach(function(a) {
        if (!filas[a.fila]) filas[a.fila] = [];
        filas[a.fila].push(a);
    });

    // Ordenar filas numéricamente
    var claves = Object.keys(filas).sort(function(a, b) {
        return parseInt(a) - parseInt(b);
    });

    claves.forEach(function(fila) {
        var row = document.createElement('div');
        row.className = 'seat-row';

        var label = document.createElement('span');
        label.className   = 'seat-row-label';
        label.textContent = fila;
        row.appendChild(label);

        // Ordenar asientos por número
        filas[fila].sort(function(a, b) { return a.numero - b.numero; });

        filas[fila].forEach(function(a) {
            var btn = document.createElement('button');
            btn.className   = 'seat ' + (a.disponible ? 'seat-available' : 'seat-taken');
            btn.textContent = a.numero;
            btn.title       = 'Fila ' + fila + ' — Asiento ' + a.numero;
            btn.disabled    = !a.disponible;
            if (a.disponible) {
                btn.onclick = function() { toggleAsiento(a.id, fila, a.numero, btn); };
            }
            row.appendChild(btn);
        });

        map.appendChild(row);
    });
}

// ── Toggle asiento ───────────────────────────────────────
function toggleAsiento(asientoId, fila, numero, btn) {
    if (reservaIds.length > 0) return;

    var idx = selectedSeats.findIndex(function(s) { return s.asientoId === asientoId; });
    if (idx === -1) {
        selectedSeats.push({ asientoId: asientoId, fila: fila, numero: numero });
        btn.classList.remove('seat-available');
        btn.classList.add('seat-selected');
    } else {
        selectedSeats.splice(idx, 1);
        btn.classList.remove('seat-selected');
        btn.classList.add('seat-available');
    }
    renderCarrito();
}

// ── Carrito ──────────────────────────────────────────────
function renderCarrito() {
    var items   = document.getElementById('cart-items');
    var totRow  = document.getElementById('cart-total-row');
    var totEl   = document.getElementById('cart-total');
    var btnRes  = document.getElementById('btn-reservar');

    if (selectedSeats.length === 0) {
        items.innerHTML      = '<p class="cart-empty">Ningun asiento seleccionado.</p>';
        totRow.style.display = 'none';
        btnRes.disabled      = true;
        return;
    }

    items.innerHTML = selectedSeats.map(function(s) {
        return '<div class="cart-item">' +
               '<div class="cart-item-info">Fila ' + s.fila + ' &mdash; Asiento ' + s.numero + '</div>' +
               '<div class="cart-item-price">' + precioPorSeat.toFixed(2) + ' EUR</div>' +
               '</div>';
    }).join('');

    var total = selectedSeats.length * precioPorSeat;
    totEl.textContent    = total.toFixed(2) + ' EUR';
    totRow.style.display = 'flex';
    btnRes.disabled      = false;
}

// ── Reservar ─────────────────────────────────────────────
async function handleReservar() {
    var btn = document.getElementById('btn-reservar');
    btn.disabled    = true;
    btn.textContent = 'Reservando...';
    setMsg('', '');
    reservaIds = [];

    for (var i = 0; i < selectedSeats.length; i++) {
        var seat = selectedSeats[i];
        try {
            var res  = await fetch('/api/reservas', {
                method:  'POST',
                headers: { 'Content-Type': 'application/json' },
                body:    JSON.stringify({ asiento_id: seat.asientoId, evento_id: eventoId }),
            });
            var json = await res.json();

            if (res.status === 401 || res.status === 403) {
                throw new Error('no_auth');
            }
            if (!res.ok) {
                throw new Error(json.error || json.message || 'Error al reservar');
            }
            reservaIds.push(json.data.reserva_id);
        } catch(e) {
            // Cancelar reservas ya creadas
            for (var r = 0; r < reservaIds.length; r++) {
                await fetch('/api/reservas/' + reservaIds[r], { method: 'DELETE' }).catch(function(){});
            }
            reservaIds = [];
            btn.disabled    = false;
            btn.textContent = 'Reservar asientos';
            if (e.message === 'no_auth') {
                setMsg('error', 'No estas autenticado. <a href="/login" style="color:var(--gold)">Inicia sesion</a> para reservar.');
            } else {
                setMsg('error', 'Error: ' + e.message);
            }
            return;
        }
    }

    btn.style.display = 'none';
    document.getElementById('btn-comprar').style.display  = 'block';
    document.getElementById('btn-cancelar').style.display = 'block';
    setMsg('info', 'Asientos reservados durante 15 minutos. Confirma la compra.');
}

// ── Comprar ──────────────────────────────────────────────
async function handleComprar() {
    var btn = document.getElementById('btn-comprar');
    btn.disabled    = true;
    btn.textContent = 'Procesando...';

    try {
        // El CompraController espera el campo "reservas" (no "reserva_ids")
        var res  = await fetch('/api/compras', {
            method:  'POST',
            headers: { 'Content-Type': 'application/json' },
            body:    JSON.stringify({ reservas: reservaIds }),
        });
        var json = await res.json();

        if (!res.ok) throw new Error(json.error || json.message || 'Error al confirmar');

        window.location.href = '/mis-entradas';
    } catch(e) {
        btn.disabled    = false;
        btn.textContent = 'Confirmar compra';
        setMsg('error', 'Error: ' + e.message);
    }
}

// ── Cancelar ─────────────────────────────────────────────
async function handleCancelar() {
    for (var i = 0; i < reservaIds.length; i++) {
        await fetch('/api/reservas/' + reservaIds[i], { method: 'DELETE' }).catch(function(){});
    }
    reservaIds    = [];
    selectedSeats = [];
    renderCarrito();
    resetBotones();

    // Recargar mapa
    var sId = document.getElementById('sector-select').value;
    if (sId) {
        var res  = await fetch('/api/eventos/' + eventoId + '/sectores/' + sId + '/asientos');
        var json = await res.json();
        renderMapa(json.data.asientos || []);
    }
    setMsg('', '');
}

// ── Helpers ──────────────────────────────────────────────
function resetBotones() {
    var btnRes = document.getElementById('btn-reservar');
    btnRes.style.display  = 'block';
    btnRes.disabled       = true;
    btnRes.textContent    = 'Reservar asientos';
    document.getElementById('btn-comprar').style.display  = 'none';
    document.getElementById('btn-cancelar').style.display = 'none';
}

function setMsg(tipo, texto) {
    var el = document.getElementById('cart-msg');
    if (!texto) { el.innerHTML = ''; return; }
    var cls = tipo === 'error' ? 'alert-error' : tipo === 'info' ? 'alert-info' : 'alert-success';
    el.innerHTML = '<div class="alert ' + cls + '">' + texto + '</div>';
}
</script>
@endsection
