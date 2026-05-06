@extends('layouts.app')

@section('title', 'Checkout - TicketLand')

@section('content')
<div class="mb-8">
    <a href="{{ route('home') }}" class="text-cyan-400 hover:text-cyan-300 text-sm">← Volver al catálogo</a>
    <h1 class="text-5xl font-black text-white mt-4">Carrito de Compra</h1>
    <p class="text-slate-400 mt-2">Revisa tus asientos reservados antes de finalizar</p>
</div>

<div class="grid gap-8 lg:grid-cols-[1.6fr_0.9fr]">
    <section id="cartItems" class="space-y-4 text-slate-300">
        <div class="p-8 rounded-3xl border border-slate-700 bg-slate-900/70 text-center">
            <p class="text-slate-500">Cargando contenido del carrito...</p>
        </div>
    </section>

    <aside class="space-y-4">
        <div class="rounded-3xl border border-slate-700 bg-slate-900/70 p-6">
            <h2 class="text-2xl font-bold text-white mb-4">Resumen</h2>
            <div id="cartStatus" class="space-y-4 text-slate-300">
                <p>Esperando selección...</p>
            </div>
        </div>
        <div class="rounded-3xl border border-slate-700 bg-slate-900/70 p-6">
            <h2 class="text-2xl font-bold text-white mb-4">Información</h2>
            <p class="text-slate-500 text-sm leading-relaxed">Las reservas se bloquean de forma temporal hasta completar la compra.</p>
        </div>
    </aside>
</div>

<script>
const CartApp = {
    cartKey: 'cart_tickets',
    cart: {},
    cartItems: document.getElementById('cartItems'),
    cartStatus: document.getElementById('cartStatus'),

    async init() {
        this.cart = JSON.parse(localStorage.getItem(this.cartKey) || '{}');
        const eventIds = Object.keys(this.cart);

        if (eventIds.length === 0) {
            this.renderEmpty();
            return;
        }

        this.cartItems.innerHTML = '';
        let totalPrice = 0;
        let totalQty = 0;

        for (const eventId of eventIds) {
            try {
                const res = await fetch(`/api/eventos/${eventId}`);
                const json = await res.json();
                const event = json.data.evento;
                const items = this.cart[eventId] || [];

                const section = document.createElement('div');
                section.className = 'rounded-3xl border border-slate-700 bg-slate-900/70 p-6';
                section.innerHTML = `
                    <div class="flex items-center justify-between mb-6">
                        <div>
                            <h3 class="text-2xl font-semibold text-white">${event.nombre}</h3>
                            <p class="text-slate-500 text-sm mt-1">${new Date(event.fecha_evento).toLocaleDateString('es-ES')} · ${event.hora}</p>
                        </div>
                        <span class="inline-flex items-center gap-2 rounded-full bg-cyan-500/10 px-4 py-2 text-cyan-300 text-sm">${items.length} sector${items.length > 1 ? 'es' : ''}</span>
                    </div>
                `;

                items.forEach((item, idx) => {
                    const lineTotal = item.price * item.cantidad;
                    totalPrice += lineTotal;
                    totalQty += item.cantidad;
                    section.innerHTML += `
                        <div class="mb-4 rounded-2xl border border-slate-700 bg-slate-950/70 p-4">
                            <div class="flex justify-between items-start gap-4">
                                <div>
                                    <p class="text-white font-semibold">${item.sectorName}</p>
                                    <p class="text-slate-500 text-sm">Cantidad: ${item.cantidad}</p>
                                </div>
                                <div class="text-right">
                                    <p class="font-bold text-cyan-300">$${lineTotal.toFixed(2)}</p>
                                    <button onclick="CartApp.removeItem(${eventId}, ${idx})" class="text-xs text-rose-400 hover:text-rose-200 mt-2">Eliminar</button>
                                </div>
                            </div>
                        </div>
                    `;
                });

                this.cartItems.appendChild(section);
            } catch (err) {
                console.error(err);
            }
        }

        this.renderSummary(totalQty, totalPrice);
    },

    renderSummary(totalQty, totalPrice) {
        this.cartStatus.innerHTML = `
            <div class="space-y-4">
                <div class="flex justify-between text-slate-400">
                    <span>Total entradas:</span>
                    <span>${totalQty}</span>
                </div>
                <div class="flex justify-between text-white font-bold text-lg">
                    <span>Total a pagar:</span>
                    <span>$${totalPrice.toFixed(2)}</span>
                </div>
                <button onclick="CartApp.checkout()" class="w-full mt-4 rounded-2xl bg-cyan-500 py-3 text-sm font-semibold text-slate-950 hover:bg-cyan-400 transition">
                    Continuar a Pago
                </button>
            </div>
        `;
    },

    renderEmpty() {
        this.cartItems.innerHTML = `
            <div class="rounded-3xl border border-slate-700 bg-slate-900/60 p-10 text-center text-slate-500">
                <p class="mb-4 text-lg">Tu carrito está vacío.</p>
                <a href="{{ route('home') }}" class="inline-flex px-5 py-3 rounded-full bg-cyan-500 text-slate-950 font-semibold hover:bg-cyan-400 transition">Ver eventos</a>
            </div>
        `;
        this.cartStatus.innerHTML = '';
    },

    removeItem(eventId, idx) {
        if (!this.cart[eventId]) return;
        this.cart[eventId].splice(idx, 1);
        if (this.cart[eventId].length === 0) delete this.cart[eventId];
        localStorage.setItem(this.cartKey, JSON.stringify(this.cart));
        this.init();
    },

    async checkout() {
        const token = localStorage.getItem('auth_token');
        if (!token) {
            location.href = '{{ route('login') }}';
            return;
        }

        const payload = [];
        for (const [eventId, items] of Object.entries(this.cart)) {
            for (const item of items) {
                payload.push({
                    evento_id: parseInt(eventId),
                    sector_id: item.sectorId,
                    cantidad: item.cantidad
                });
            }
        }

        try {
            const res = await fetch('/api/compras', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Authorization': `Bearer ${token}`
                },
                body: JSON.stringify({ reservas: payload })
            });

            if (res.ok) {
                localStorage.removeItem(this.cartKey);
                location.href = '{{ route('entradas') }}';
            } else {
                const err = await res.json();
                alert(err.message || 'No se pudo completar la compra');
            }
        } catch (error) {
            console.error(error);
            alert('Error al procesar la compra');
        }
    }
};

document.addEventListener('DOMContentLoaded', () => CartApp.init());
</script>
@endsection
function removeItem(eventoId, index) {
    let carrito = JSON.parse(localStorage.getItem('carrito') || '{}');
    if (carrito[eventoId]) {
        carrito[eventoId].splice(index, 1);
        if (carrito[eventoId].length === 0) {
            delete carrito[eventoId];
        }
        localStorage.setItem('carrito', JSON.stringify(carrito));
        actualizarCarrito();
    }
}

async function procederAComprar() {
    const token = localStorage.getItem('token');
    if (!token) {
        alert('Debes iniciar sesión para comprar');
        return;
    }

    const carrito = JSON.parse(localStorage.getItem('carrito') || '{}');

    try {
        // Crear reservas primero
        const reservas = [];
        for (const [eventoId, items] of Object.entries(carrito)) {
            for (const item of items) {
                const reservaResponse = await fetch('/api/reservas', {
                    method: 'POST',
                    headers: {
                        'Authorization': `Bearer ${token}`,
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        evento_id: parseInt(eventoId),
                        sector_id: item.sectorId,
                        cantidad: item.cantidad
                    })
                });

                if (reservaResponse.ok) {
                    const reservaData = await reservaResponse.json();
                    reservas.push({
                        evento_id: parseInt(eventoId),
                        sector_id: item.sectorId,
                        cantidad: item.cantidad
                    });
                } else {
                    alert('Error al realizar la reserva');
                    return;
                }
            }
        }

        // Proceder con la compra
        const compraResponse = await fetch('/api/compras', {
            method: 'POST',
            headers: {
                'Authorization': `Bearer ${token}`,
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                reservas: reservas
            })
        });

        if (compraResponse.ok) {
            alert('¡Compra exitosa! Revisa tus entradas en "Mis Entradas"');
            localStorage.removeItem('carrito');
            window.location.href = '{{ route("entradas") }}';
        } else {
            alert('Error al procesar la compra');
        }
    } catch (error) {
        console.error('Error:', error);
        alert('Error al procesar la compra');
    }
}
</script>
@endsection
