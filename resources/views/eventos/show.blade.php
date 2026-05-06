@extends('layouts.app')

@section('title', 'Detalles del Evento - TicketLand')

@section('content')
<div class="mb-8">
    <a href="{{ route('home') }}" class="text-cyan-400 hover:text-cyan-300 text-sm flex items-center gap-2 mb-6">
        ← Volver al Catálogo
    </a>
</div>

<div id="eventDetail" class="space-y-8">
    <div class="text-slate-400 text-center py-12">Cargando detalles del evento...</div>
</div>

<script>
const EventDetailCtrl = {
    eventId: {{ $id }},
    container: document.getElementById('eventDetail'),
    cartData: {},

    async initialize() {
        try {
            const res = await fetch(`/api/eventos/${this.eventId}`);
            const json = await res.json();

            const event = json.data.evento;
            const sectors = json.data.sectores_disponibles;

            this.render(event, sectors);
            this.loadCart();
        } catch (err) {
            this.container.innerHTML = '<div class="text-red-400 text-center py-12">⚠️ Error al cargar evento</div>';
        }
    },

    render(event, sectors) {
        const eventDate = new Date(event.fecha_evento);
        const dateFormatted = eventDate.toLocaleDateString('es-ES', {
            weekday: 'long',
            year: 'numeric',
            month: 'long',
            day: 'numeric'
        }).toUpperCase();

        const heroHTML = `
            <div class="bg-gradient-to-br from-green-900 to-emerald-900 border border-emerald-700 rounded-xl p-8">
                <h1 class="text-5xl font-black text-white mb-3">${event.nombre}</h1>
                <p class="text-emerald-200 text-lg mb-6">${event.descripcion_corta}</p>
                <div class="flex gap-8 text-emerald-100">
                    <div class="flex items-center gap-2">
                        <span class="text-2xl">📅</span>
                        <div>
                            <p class="text-xs text-emerald-300">FECHA</p>
                            <p class="font-semibold">${dateFormatted}</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="text-2xl">⏰</span>
                        <div>
                            <p class="text-xs text-emerald-300">HORA</p>
                            <p class="font-semibold">${event.hora}</p>
                        </div>
                    </div>
                </div>
            </div>
        `;

        const descHTML = `
            <div class="grid md:grid-cols-3 gap-8">
                <div class="md:col-span-2">
                    <div class="bg-slate-800/50 border border-slate-700 rounded-lg p-8">
                        <h2 class="text-2xl font-bold text-white mb-4">Información del Evento</h2>
                        <p class="text-slate-300 leading-relaxed">${event.descripcion_larga}</p>
                    </div>

                    <div class="mt-8">
                        <h2 class="text-2xl font-bold text-white mb-6">Selecciona Sectores</h2>
                        <div id="sectoresGrid" class="grid md:grid-cols-2 gap-4"></div>
                    </div>
                </div>

                <div class="md:sticky md:top-24 h-fit">
                    <div class="bg-slate-800/80 border border-slate-700 rounded-lg p-6 backdrop-blur">
                        <h3 class="text-xl font-bold text-white mb-4">Tu Compra</h3>
                        <div id="cartSummary" class="space-y-2 min-h-12 text-slate-300">
                            <p class="text-sm">Agrega entradas para ver resumen</p>
                        </div>
                    </div>
                </div>
            </div>
        `;

        this.container.innerHTML = heroHTML + descHTML;

        // Render sectors
        const sectorsContainer = document.getElementById('sectoresGrid');
        sectors.forEach(sector => this.renderSector(sector, sectorsContainer));
    },

    renderSector(sector, container) {
        const sectorEl = document.createElement('div');
        sectorEl.className = 'bg-slate-800/50 border border-slate-700 rounded-lg p-5 hover:border-green-600/50 transition';
        sectorEl.innerHTML = `
            <div class="mb-4">
                <h3 class="text-lg font-bold text-white">${sector.nombre}</h3>
                <p class="text-xs text-slate-400 mt-1">${sector.asientos_disponibles} disponibles</p>
            </div>
            <div class="flex justify-between items-center mb-4">
                <span class="text-2xl font-bold text-green-400">$${sector.precio}</span>
            </div>
            <div class="flex gap-2">
                <input
                    type="number"
                    min="0"
                    max="${sector.asientos_disponibles}"
                    value="0"
                    class="flex-1 px-3 py-2 bg-slate-900 border border-slate-600 rounded text-white text-sm"
                    id="qty_${sector.id}"
                >
                <button
                    onclick="EventDetailCtrl.addToCart(${this.eventId}, ${sector.id}, '${sector.nombre}', ${sector.precio})"
                    class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded font-semibold transition"
                >
                    Añadir
                </button>
            </div>
        `;
        container.appendChild(sectorEl);
    },

    addToCart(eventId, sectorId, sectorName, price) {
        const qtyInput = document.getElementById(`qty_${sectorId}`);
        const qty = parseInt(qtyInput.value);

        if (qty <= 0) {
            alert('Ingresa cantidad válida');
            return;
        }

        if (!this.cartData[eventId]) this.cartData[eventId] = [];

        const exists = this.cartData[eventId].find(i => i.sectorId === sectorId);
        if (exists) {
            exists.cantidad += qty;
        } else {
            this.cartData[eventId].push({ sectorId, sectorName, price, cantidad: qty });
        }

        localStorage.setItem('cart_tickets', JSON.stringify(this.cartData));
        qtyInput.value = '0';
        this.updateSummary();
    },

    loadCart() {
        this.cartData = JSON.parse(localStorage.getItem('cart_tickets') || '{}');
        this.updateSummary();
    },

    updateSummary() {
        const items = this.cartData[this.eventId] || [];
        const summary = document.getElementById('cartSummary');

        if (items.length === 0) {
            summary.innerHTML = '<p class="text-sm text-slate-400">Sin artículos</p>';
            return;
        }

        let total = 0;
        let html = '<div class="space-y-2 mb-4 pb-4 border-b border-slate-700">';

        items.forEach(item => {
            const subtotal = item.price * item.cantidad;
            total += subtotal;
            html += `
                <div class="flex justify-between text-sm">
                    <span class="text-slate-300">${item.sectorName} ×${item.cantidad}</span>
                    <span class="text-green-400 font-semibold">$${subtotal.toFixed(2)}</span>
                </div>
            `;
        });

        html += '</div>';
        html += `
            <div class="mb-4">
                <div class="flex justify-between mb-3">
                    <span class="text-white font-bold">TOTAL</span>
                    <span class="text-2xl font-black text-green-400">$${total.toFixed(2)}</span>
                </div>
                <a href="{{ route('carrito') }}" class="block w-full py-3 bg-green-600 hover:bg-green-700 text-white text-center rounded font-bold transition">
                    Proceder al Carrito
                </a>
            </div>
        `;

        summary.innerHTML = html;
    }
};

document.addEventListener('DOMContentLoaded', () => EventDetailCtrl.initialize());
</script>
@endsection
