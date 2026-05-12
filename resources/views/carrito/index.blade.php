@extends('layouts.app')

@section('title', 'Carrito de Compras - Roig Arena')

@section('content')
<div class="space-y-8">
    <!-- Header -->
    <div class="text-center mb-12">
        <h1 class="text-4xl font-bold mb-3 bg-gradient-to-r from-red-500 to-red-600 bg-clip-text text-transparent">
            🛒 Carrito de Compras
        </h1>
        <p class="text-slate-400">Revisa y confirma tu compra</p>
    </div>

    <!-- Loading State -->
    <div id="loading" class="text-center py-12 hidden">
        <div class="inline-block animate-spin">
            <div class="h-8 w-8 border-4 border-red-600 border-t-transparent rounded-full"></div>
        </div>
        <p class="text-slate-400 mt-3">Cargando carrito...</p>
    </div>

    <!-- Empty Cart -->
    <div id="empty-cart" class="text-center py-12">
        <p class="text-6xl mb-4">🛒</p>
        <p class="text-slate-400 text-lg mb-6">Tu carrito está vacío</p>
        <a href="{{ route('home') }}" class="inline-block px-6 py-3 bg-red-600 hover:bg-red-700 text-white font-bold rounded-lg transition">
            Ver Eventos
        </a>
    </div>

    <!-- Cart Items -->
    <div id="cart-content" class="hidden">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Items List -->
            <div class="lg:col-span-2">
                <div id="cart-items" class="space-y-4">
                    <!-- Items will be loaded here -->
                </div>
            </div>

            <!-- Sidebar - Summary -->
            <div class="bg-slate-800 rounded-lg border border-slate-700 p-6 h-fit sticky top-24">
                <h3 class="text-xl font-bold text-white mb-6">Resumen de Compra</h3>

                <div class="space-y-4 mb-6 pb-6 border-b border-slate-600">
                    <div class="flex justify-between text-slate-300">
                        <span>Asientos:</span>
                        <span id="summary-cantidad" class="font-semibold">0</span>
                    </div>
                    <div class="flex justify-between text-lg font-bold text-white">
                        <span>Total:</span>
                        <span id="summary-total" class="text-red-400">€0.00</span>
                    </div>
                </div>

                <button onclick="confirmarCompra()" class="w-full py-3 bg-red-600 hover:bg-red-700 text-white font-bold rounded-lg transition duration-200 mb-3">
                    ✅ Confirmar Compra
                </button>

                <button onclick="vaciarCarrito()" class="w-full py-2 bg-slate-700 hover:bg-slate-600 text-slate-300 font-semibold rounded-lg transition duration-200">
                    🗑️ Vaciar Carrito
                </button>
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
    let carrito = [];

    function cargarCarrito() {
        carrito = JSON.parse(localStorage.getItem('reservas') || '[]');

        if (carrito.length === 0) {
            document.getElementById('empty-cart').classList.remove('hidden');
            document.getElementById('cart-content').classList.add('hidden');
            return;
        }

        document.getElementById('empty-cart').classList.add('hidden');
        document.getElementById('loading').classList.remove('hidden');

        cargarDetallesReservas();
    }

    async function cargarDetallesReservas() {
        try {
            const response = await fetch('/api/reservas', {
                headers: {
                    'Authorization': `Bearer ${localStorage.getItem('token')}`
                }
            });

            if (!response.ok) {
                throw new Error('Error al cargar reservas');
            }

            const data = await response.json();
            const reservasAPI = data.data;

            // Enriquecer carrito con datos de API
            const carritoEnriquecido = carrito.map(item => {
                const reserva = reservasAPI.find(r => r.id === item.id);
                if (reserva) {
                    return {
                        ...item,
                        evento_nombre: reserva.evento?.nombre || 'Evento desconocido',
                        evento_id: reserva.evento_id,
                        evento_fecha: reserva.evento?.fecha,
                        evento_hora: reserva.evento?.hora,
                        asiento_nombre: reserva.asiento?.nombreCompleto || 'Asiento desconocido',
                        tiempo_restante: this.getTiempoRestante(reserva.reservado_hasta)
                    };
                }
                return item;
            });

            renderizarCarrito(carritoEnriquecido);
            document.getElementById('loading').classList.add('hidden');
            document.getElementById('cart-content').classList.remove('hidden');

        } catch (error) {
            console.error('Error:', error);
            document.getElementById('loading').classList.add('hidden');
            document.getElementById('error-message').classList.remove('hidden');
            document.getElementById('error-text').textContent = error.message;
        }
    }

    getTiempoRestante = (fecha) => {
        if (!fecha) return null;
        const ahora = new Date();
        const reservado = new Date(fecha);
        const diff = reservado - ahora;
        if (diff <= 0) return '0m';

        const minutos = Math.floor(diff / 60000);
        const horas = Math.floor(minutos / 60);

        if (horas > 0) {
            return `${horas}h ${minutos % 60}m`;
        }
        return `${minutos}m`;
    };

    function renderizarCarrito(items) {
        const container = document.getElementById('cart-items');
        let total = 0;

        container.innerHTML = items.map((item, index) => {
            total += item.precio || 0;
            return `
                <div class="bg-slate-800 rounded-lg border border-slate-700 p-4 flex justify-between items-start gap-4">
                    <div class="flex-1">
                        <h4 class="font-bold text-white mb-2">${item.evento_nombre}</h4>
                        <div class="space-y-1 text-sm text-slate-400">
                            <p>📅 ${item.evento_fecha ? new Date(item.evento_fecha).toLocaleDateString('es-ES', { year: 'numeric', month: 'long', day: 'numeric' }) : 'Fecha desconocida'}</p>
                            <p>🕐 ${item.evento_hora || 'Hora desconocida'}</p>
                            <p>💺 ${item.asiento_nombre}</p>
                            <p class="text-yellow-400">⏱️ ${item.tiempo_restante || '15m'} restantes</p>
                        </div>
                    </div>
                    <div class="text-right">
                        <p class="text-lg font-bold text-red-400 mb-3">€${(item.precio || 0).toFixed(2)}</p>
                        <button onclick="eliminarDelCarrito(${index})" class="px-3 py-1 bg-red-600/20 text-red-400 border border-red-600/50 rounded hover:bg-red-600/30 transition text-sm font-semibold">
                            ❌ Quitar
                        </button>
                    </div>
                </div>
            `;
        }).join('');

        // Actualizar resumen
        document.getElementById('summary-cantidad').textContent = items.length;
        document.getElementById('summary-total').textContent = `€${total.toFixed(2)}`;
    }

    function eliminarDelCarrito(index) {
        carrito.splice(index, 1);
        localStorage.setItem('reservas', JSON.stringify(carrito));
        window.dispatchEvent(new Event('storage'));
        cargarCarrito();
    }

    function vaciarCarrito() {
        if (!confirm('¿Estás seguro de que deseas vaciar el carrito?')) {
            return;
        }

        carrito.forEach(async (item) => {
            try {
                await fetch(`/api/reservas/${item.id}`, {
                    method: 'DELETE',
                    headers: {
                        'Authorization': `Bearer ${localStorage.getItem('token')}`
                    }
                });
            } catch (error) {
                console.error('Error al eliminar reserva:', error);
            }
        });

        carrito = [];
        localStorage.setItem('reservas', JSON.stringify(carrito));
        window.dispatchEvent(new Event('storage'));
        cargarCarrito();
    }

    async function confirmarCompra() {
        if (carrito.length === 0) {
            alert('El carrito está vacío');
            return;
        }

        try {
            const response = await fetch('/api/compras', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Authorization': `Bearer ${localStorage.getItem('token')}`
                },
                body: JSON.stringify({
                    reservas: carrito.map(item => item.id)
                })
            });

            const data = await response.json();

            if (!response.ok) {
                throw new Error(data.error || 'Error al procesar la compra');
            }

            // Limpiar carrito
            localStorage.setItem('reservas', JSON.stringify([]));
            window.dispatchEvent(new Event('storage'));

            alert('✅ ¡Compra realizada exitosamente!\n\nTus entradas están en la sección "Mis Entradas"');
            window.location.href = '{{ route("entradas") }}';

        } catch (error) {
            console.error('Error:', error);
            alert('❌ Error: ' + error.message);
        }
    }

    // Cargar carrito al iniciar
    cargarCarrito();

    // Actualizar carrito cada segundo para mostrar tiempo restante
    setInterval(() => {
        if (carrito.length > 0) {
            cargarDetallesReservas();
        }
    }, 60000); // Cada minuto
</script>
@endsection
