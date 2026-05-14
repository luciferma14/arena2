@extends('layouts.app')

@section('title', 'Roig Arena — Entradas')

@section('content')

<div class="hero" style="margin: -48px -24px 0; padding: 100px 24px 80px;">
    <p class="hero-label">Valencia &mdash; Palau de les Arts</p>
    <h1 class="hero-title">Roig Arena</h1>
    <p class="hero-sub">Los mejores eventos en directo. Reserva tu asiento antes de que se agote.</p>
    <div class="hero-actions">
        <a href="{{ route('eventos.index') }}" class="btn btn-gold btn-lg">Ver todos los eventos</a>
        @guest
        <a href="{{ route('register') }}" class="btn btn-outline btn-lg">Crear cuenta</a>
        @endguest
    </div>
</div>

<div class="section">
    <p class="section-label">Proximos eventos</p>
    <h2 class="section-title">En cartelera</h2>

    <div id="eventos-container">
        <div class="state-msg"><span class="spinner"></span></div>
    </div>
</div>

@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', async () => {
    const container = document.getElementById('eventos-container');
    try {
        const res    = await fetch('/api/eventos');
        const json   = await res.json();
        const eventos = json.data || [];

        if (eventos.length === 0) {
            container.innerHTML = '<div class="state-msg"><p class="state-msg-title">Sin eventos disponibles</p><p>Vuelve pronto.</p></div>';
            return;
        }

        const grid = document.createElement('div');
        grid.className = 'grid-events';

        eventos.forEach(ev => {
            const fecha = ev.fecha ? fmtFecha(ev.fecha + 'T00:00:00') : '-';
            const hora  = fmtHora(ev.hora);
            const img   = ev.poster_url
                ? `<img src="${ev.poster_url}" class="event-card-image" alt="${ev.nombre}" loading="lazy">`
                : `<div class="event-card-image-placeholder">${ev.nombre.substring(0,2).toUpperCase()}</div>`;

            const card = document.createElement('div');
            card.className = 'event-card';
            card.innerHTML = `
                ${img}
                <div class="event-card-body">
                    <p class="event-card-date">${fecha} &mdash; ${hora}</p>
                    <h3 class="event-card-title">${ev.nombre}</h3>
                    <p class="event-card-desc">${ev.descripcion_corta || ''}</p>
                    <a href="/eventos/${ev.id}" class="btn btn-outline">Ver evento</a>
                </div>
            `;
            grid.appendChild(card);
        });

        container.innerHTML = '';
        container.appendChild(grid);
    } catch (e) {
        container.innerHTML = '<div class="state-msg"><p class="state-msg-title">Error al cargar eventos</p><p>Intentalo de nuevo mas tarde.</p></div>';
    }
});
</script>
@endsection
