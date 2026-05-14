@extends('layouts.app')

@section('title', 'Eventos — Roig Arena')

@section('content')

<div class="page-header">
    <p class="page-header-label">Cartelera</p>
    <h1 class="page-header-title">Todos los eventos</h1>
</div>

<div id="eventos-container">
    <div class="state-msg"><span class="spinner"></span></div>
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
            container.innerHTML = '<div class="state-msg"><p class="state-msg-title">Sin eventos disponibles</p><p>No hay eventos proximos en este momento.</p></div>';
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
        container.innerHTML = '<div class="state-msg"><p class="state-msg-title">Error al cargar</p><p>Intentalo de nuevo mas tarde.</p></div>';
    }
});
</script>
@endsection
