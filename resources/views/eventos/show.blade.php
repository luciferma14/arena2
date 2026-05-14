@extends('layouts.app')

@section('title', 'Evento — Roig Arena')

@section('content')

<a href="{{ route('eventos.index') }}" class="back-link">&#8592; Todos los eventos</a>

<div id="cargando" class="state-msg"><span class="spinner"></span></div>

<div id="contenido" style="display:none">

    <div id="evento-cabecera" class="page-header"></div>

    <div class="event-detail-layout">

        <div>
            <div id="evento-info"></div>

            {{-- Selector de sector --}}
            <div class="sector-bar">
                <p class="sector-bar-title">Selecciona un sector</p>
                <select id="sector-select" class="form-control" style="max-width:340px">
                    <option value="">-- Elige un sector --</option>
                </select>
                <p id="precio-sector" style="margin-top:8px;font-size:.85rem;color:var(--muted)"></p>
            </div>

            {{-- Mapa de asientos --}}
            <div id="mapa-wrap" class="seat-map-wrap" style="display:none; margin-top:16px">
                <div class="seat-map-stage">Escenario / Pista</div>
                <div id="mapa-asientos" class="seat-map"></div>
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

        {{-- Carrito --}}
        <div>
            <div class="cart-sidebar">
                <p class="cart-title">Tu selección</p>
                <div id="carrito-items">
                    <p class="cart-empty">Ningún asiento seleccionado.</p>
                </div>
                <div id="carrito-total" class="cart-total" style="display:none">
                    <span>Total</span>
                    <span id="total-precio" class="cart-total-amount">0,00 EUR</span>
                </div>
                <div style="margin-top:20px; display:flex; flex-direction:column; gap:10px">
                    <button id="btn-reservar" class="btn btn-gold btn-full" disabled>Reservar asientos</button>
                    <button id="btn-comprar"  class="btn btn-success btn-full" style="display:none">Confirmar compra</button>
                    <button id="btn-cancelar" class="btn btn-ghost btn-full"   style="display:none">Cancelar reserva</button>
                </div>
                <div id="msg-carrito" style="margin-top:12px"></div>
            </div>
        </div>

    </div>
</div>

@endsection

@section('scripts')
<script>
var eventoId     = {{ $id }};
var precioAsiento = 0;
var asientosSeleccionados = [];   // [{id, fila, numero}]
var reservasActivas       = [];   // IDs de estado_asientos (json.data.id)

document.addEventListener('DOMContentLoaded', function() {
    cargarEvento();
    document.getElementById('sector-select').addEventListener('change', cambiarSector);
    document.getElementById('btn-reservar').addEventListener('click', reservar);
    document.getElementById('btn-comprar').addEventListener('click', comprar);
    document.getElementById('btn-cancelar').addEventListener('click', cancelar);
});

// ─── Cargar evento ────────────────────────────────────────
function cargarEvento() {
    fetch('/api/eventos/' + eventoId)
    .then(function(r) { return r.json(); })
    .then(function(json) {
        var ev = json.data.evento;

        document.getElementById('evento-cabecera').innerHTML =
            '<p class="page-header-label">Evento</p>' +
            '<h1 class="page-header-title">' + ev.nombre + '</h1>';

        var img = ev.poster_url
            ? '<img src="' + ev.poster_url + '" class="event-banner" alt="' + ev.nombre + '">'
            : '<div class="event-banner-placeholder">' + ev.nombre.substring(0,2).toUpperCase() + '</div>';

        document.getElementById('evento-info').innerHTML =
            img +
            '<div class="event-meta-row">' +
              '<div class="event-meta-item"><p class="event-meta-label">Fecha</p><p class="event-meta-value">' + fmtFecha(ev.fecha) + '</p></div>' +
              '<div class="event-meta-item"><p class="event-meta-label">Hora</p><p class="event-meta-value">' + fmtHora(ev.hora) + '</p></div>' +
              '<div class="event-meta-item"><p class="event-meta-label">Disponibles</p><p class="event-meta-value">' + (json.data.asientos_disponibles || '-') + '</p></div>' +
            '</div>' +
            '<p class="event-desc">' + (ev.descripcion_larga || ev.descripcion_corta || '') + '</p>';

        var sel = document.getElementById('sector-select');
        var sectores = json.data.sectores_disponibles || [];
        sectores.forEach(function(s) {
            var precio = (s.pivot && s.pivot.precio) ? s.pivot.precio : (s.precio || 0);
            var opt = document.createElement('option');
            opt.value          = s.id;
            opt.dataset.precio = precio;
            opt.textContent    = s.nombre + ' — ' + parseFloat(precio).toFixed(2) + ' EUR';
            sel.appendChild(opt);
        });

        document.getElementById('cargando').style.display  = 'none';
        document.getElementById('contenido').style.display = 'block';
    })
    .catch(function() {
        document.getElementById('cargando').innerHTML = '<p class="state-msg-title">Error al cargar el evento</p>';
    });
}

// ─── Cambiar sector ───────────────────────────────────────
function cambiarSector() {
    var sel = document.getElementById('sector-select');
    var sId = sel.value;
    document.getElementById('mapa-wrap').style.display = 'none';

    if (!sId) return;

    var opt = sel.options[sel.selectedIndex];
    precioAsiento = parseFloat(opt.dataset.precio) || 0;
    document.getElementById('precio-sector').textContent = 'Precio por asiento: ' + precioAsiento.toFixed(2) + ' EUR';

    asientosSeleccionados = [];
    reservasActivas = [];
    actualizarCarrito();
    resetBotones();

    document.getElementById('mapa-asientos').innerHTML = '<div class="state-msg"><span class="spinner"></span></div>';
    document.getElementById('mapa-wrap').style.display = 'block';

    fetch('/api/eventos/' + eventoId + '/sectores/' + sId + '/asientos')
    .then(function(r) { return r.json(); })
    .then(function(json) {
        dibujarMapa(json.data.asientos || []);
    })
    .catch(function() {
        document.getElementById('mapa-asientos').innerHTML = '<p style="color:var(--error);text-align:center">Error al cargar los asientos</p>';
    });
}

// ─── Dibujar mapa ─────────────────────────────────────────
function dibujarMapa(asientos) {
    var mapa = document.getElementById('mapa-asientos');
    mapa.innerHTML = '';

    // Agrupar por fila
    var filas = {};
    asientos.forEach(function(a) {
        if (!filas[a.fila]) filas[a.fila] = [];
        filas[a.fila].push(a);
    });

    // Ordenar filas numéricamente
    var numFilas = Object.keys(filas).sort(function(a, b) { return a - b; });

    // Ajustar el ancho del escenario al ancho real de las filas
    var maxAsientos = 0;
    numFilas.forEach(function(f) { if (filas[f].length > maxAsientos) maxAsientos = filas[f].length; });
    var anchoFila = maxAsientos * (38 + 3) + 22 + 5; // seats * (size+gap) + label + margin
    var stage = document.querySelector('.seat-map-stage');
    if (stage) stage.style.width = anchoFila + 'px';

    numFilas.forEach(function(fila) {
        var fila_div = document.createElement('div');
        fila_div.className = 'seat-row';

        var etiqueta = document.createElement('span');
        etiqueta.className   = 'seat-row-label';
        etiqueta.textContent = fila;
        fila_div.appendChild(etiqueta);

        filas[fila].sort(function(a, b) { return a.numero - b.numero; });

        filas[fila].forEach(function(a) {
            var btn = document.createElement('button');
            btn.className   = 'seat ' + (a.disponible ? 'seat-available' : 'seat-taken');
            btn.textContent = a.numero;
            btn.title       = 'Fila ' + fila + ' — Asiento ' + a.numero;
            btn.disabled    = !a.disponible;

            if (a.disponible) {
                btn.onclick = function() { seleccionarAsiento(a.id, fila, a.numero, btn); };
            }

            fila_div.appendChild(btn);
        });

        mapa.appendChild(fila_div);
    });
}

// ─── Seleccionar asiento ──────────────────────────────────
function seleccionarAsiento(id, fila, numero, btn) {
    if (reservasActivas.length > 0) return;

    var idx = asientosSeleccionados.findIndex(function(s) { return s.id === id; });
    if (idx === -1) {
        asientosSeleccionados.push({ id: id, fila: fila, numero: numero });
        btn.classList.remove('seat-available');
        btn.classList.add('seat-selected');
    } else {
        asientosSeleccionados.splice(idx, 1);
        btn.classList.remove('seat-selected');
        btn.classList.add('seat-available');
    }
    actualizarCarrito();
}

// ─── Carrito ──────────────────────────────────────────────
function actualizarCarrito() {
    var items   = document.getElementById('carrito-items');
    var totDiv  = document.getElementById('carrito-total');
    var totEl   = document.getElementById('total-precio');
    var btnRes  = document.getElementById('btn-reservar');

    if (asientosSeleccionados.length === 0) {
        items.innerHTML      = '<p class="cart-empty">Ningún asiento seleccionado.</p>';
        totDiv.style.display = 'none';
        btnRes.disabled      = true;
        return;
    }

    items.innerHTML = asientosSeleccionados.map(function(s) {
        return '<div class="cart-item">' +
               '<div class="cart-item-info">Fila ' + s.fila + ' &mdash; Asiento ' + s.numero + '</div>' +
               '<div class="cart-item-price">' + precioAsiento.toFixed(2) + ' EUR</div>' +
               '</div>';
    }).join('');

    totEl.textContent    = (asientosSeleccionados.length * precioAsiento).toFixed(2) + ' EUR';
    totDiv.style.display = 'flex';
    btnRes.disabled      = false;
}

// ─── Reservar ─────────────────────────────────────────────
async function reservar() {
    var btn = document.getElementById('btn-reservar');
    btn.disabled    = true;
    btn.textContent = 'Reservando...';
    setMsg('', '');
    reservasActivas = [];

    for (var i = 0; i < asientosSeleccionados.length; i++) {
        var asiento = asientosSeleccionados[i];
        try {
            var res  = await fetch('/api/reservas', {
                method:  'POST',
                headers: { 'Content-Type': 'application/json' },
                body:    JSON.stringify({ asiento_id: asiento.id, evento_id: eventoId })
            });
            var json = await res.json();

            if (res.status === 401 || res.status === 403) {
                throw new Error('no_auth');
            }
            if (!res.ok) {
                throw new Error(json.error || json.message || 'Error al reservar');
            }

            // ReservaResource devuelve "id", no "reserva_id"
            reservasActivas.push(json.data.id);

        } catch(e) {
            for (var j = 0; j < reservasActivas.length; j++) {
                fetch('/api/reservas/' + reservasActivas[j], { method: 'DELETE' }).catch(function(){});
            }
            reservasActivas = [];
            btn.disabled    = false;
            btn.textContent = 'Reservar asientos';

            if (e.message === 'no_auth') {
                setMsg('error', 'No estás autenticado. <a href="/login" style="color:var(--gold)">Inicia sesión</a> para comprar entradas.');
            } else {
                setMsg('error', 'Error: ' + e.message);
            }
            return;
        }
    }

    btn.style.display = 'none';
    document.getElementById('btn-comprar').style.display  = 'block';
    document.getElementById('btn-cancelar').style.display = 'block';
    setMsg('info', 'Asientos reservados 15 minutos. Confirma la compra.');
}

// ─── Comprar ──────────────────────────────────────────────
async function comprar() {
    var btn = document.getElementById('btn-comprar');
    btn.disabled    = true;
    btn.textContent = 'Procesando...';

    try {
        var res  = await fetch('/api/compras', {
            method:  'POST',
            headers: { 'Content-Type': 'application/json' },
            body:    JSON.stringify({ reservas: reservasActivas })
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

// ─── Cancelar ─────────────────────────────────────────────
async function cancelar() {
    for (var i = 0; i < reservasActivas.length; i++) {
        await fetch('/api/reservas/' + reservasActivas[i], { method: 'DELETE' }).catch(function(){});
    }
    reservasActivas       = [];
    asientosSeleccionados = [];
    actualizarCarrito();
    resetBotones();

    var sId = document.getElementById('sector-select').value;
    if (sId) {
        fetch('/api/eventos/' + eventoId + '/sectores/' + sId + '/asientos')
        .then(function(r) { return r.json(); })
        .then(function(json) { dibujarMapa(json.data.asientos || []); });
    }
    setMsg('', '');
}

// ─── Helpers ──────────────────────────────────────────────
function resetBotones() {
    var b = document.getElementById('btn-reservar');
    b.style.display  = 'block';
    b.disabled       = true;
    b.textContent    = 'Reservar asientos';
    document.getElementById('btn-comprar').style.display  = 'none';
    document.getElementById('btn-cancelar').style.display = 'none';
}

function setMsg(tipo, texto) {
    var el = document.getElementById('msg-carrito');
    if (!texto) { el.innerHTML = ''; return; }
    var cls = tipo === 'error' ? 'alert-error' : tipo === 'info' ? 'alert-info' : 'alert-success';
    el.innerHTML = '<div class="alert ' + cls + '">' + texto + '</div>';
}
</script>
@endsection
