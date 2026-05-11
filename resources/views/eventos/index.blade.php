@extends('layouts.app')

@section('title', 'Catálogo - TicketLand')

@section('content')
<div class="mb-12">
    <h1 class="text-5xl font-black text-white mb-3">Catálogo de Eventos</h1>
    <p class="text-slate-400">Descubre y compra entradas para eventos increíbles</p>
</div>

<div id="eventList" class="space-y-4">
    <div class="text-center py-12 text-slate-400">⏳ Cargando catálogo...</div>
</div>

<script>
const EventsLib = {
    container: document.getElementById('eventList'),

    async init() {
        try {
            const { data } = await window.axios.get('/eventos');

            this.container.innerHTML = '';

            if (!data || data.length === 0) {
                this.container.innerHTML = '<div class="text-center py-12 text-slate-400">📭 No hay eventos disponibles</div>';
                return;
            }

            data.forEach(event => {
                const dateObj = new Date(event.fecha_evento);
                const dateStr = dateObj.toLocaleDateString('es-ES', { weekday: 'short', year: 'numeric', month: 'short', day: 'numeric' });
                this.container.appendChild(this.createEventCard(event, dateStr));
            });
        } catch (err) {
            this.container.innerHTML = '<div class="text-center py-12 text-red-400">⚠️ Error cargando eventos</div>';
        }
    },

    createEventCard(event, dateStr) {
        const card = document.createElement('div');
        card.className = 'group bg-gradient-to-r from-slate-800 to-slate-900 border border-slate-700 rounded-lg p-6 hover:border-cyan-500/50 transition cursor-pointer';
        card.onclick = () => location.href = `/eventos/${event.id}`;

        card.innerHTML = `
            <div class="flex justify-between items-start mb-3">
                <div>
                    <h2 class="text-2xl font-bold text-white group-hover:text-cyan-300 transition">${event.nombre}</h2>
                    <p class="text-slate-400 text-sm mt-1">${event.descripcion_corta || event.nombre}</p>
                </div>
                <div class="text-right"><div class="text-3xl">🎟</div></div>
            </div>
            <div class="flex gap-6 text-sm text-slate-300">
                <span>📅 ${dateStr}</span>
                <span>⏰ ${event.hora || 'N/A'}</span>
            </div>
            <div class="mt-4 inline-block px-4 py-2 bg-cyan-600/20 border border-cyan-600/50 rounded text-cyan-300 text-sm hover:bg-cyan-600/30">Explorar →</div>
        `;

        return card;
    }
};

document.addEventListener('DOMContentLoaded', () => EventsLib.init());
</script>
@endsection
