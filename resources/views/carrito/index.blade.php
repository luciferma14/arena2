@extends('layouts.app')

@section('content')
<div class="py-8">
    <h1 class="text-4xl font-bold mb-8 text-gray-800">Mi Carrito</h1>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Reservas -->
        <div class="lg:col-span-2">
            <div id="carrito-items" class="bg-white rounded-lg shadow-md p-6">
                <p class="text-gray-600 text-center py-8">Cargando carrito...</p>
            </div>
        </div>

        <!-- Resumen -->
        <div>
            <div class="bg-white rounded-lg shadow-md p-6 sticky top-4">
                <h2 class="text-2xl font-bold mb-4">Resumen</h2>
                <div id="carrito-resumen" class="space-y-2 mb-4">
                    <p class="text-gray-600">Cargando...</p>
                </div>
                <div class="border-t pt-4 mb-4">
                    <p class="flex justify-between font-bold text-lg">
                        <span>Total:</span>
                        <span id="total-precio">$0</span>
                    </p>
                </div>
                <button id="btn-comprar" class="w-full bg-green-600 text-white py-3 rounded font-bold hover:bg-green-700 disabled:bg-gray-400">
                    Proceder al Pago
                </button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', async () => {
    try {
        const response = await window.api.get('/reservas')
        const reservas = response.data || []

        const container = document.getElementById('carrito-items')
        const resumen = document.getElementById('carrito-resumen')

        if (reservas.length === 0) {
            container.innerHTML = '<p class="text-gray-600 text-center py-8">Tu carrito está vacío</p>'
            resumen.innerHTML = '<p class="text-gray-600">Sin items</p>'
            document.getElementById('btn-comprar').disabled = true
            return
        }

        let totalPrecio = 0
        container.innerHTML = reservas.map(res => {
            const precio = res.precio || 0
            totalPrecio += precio
            return `
                <div class="border-b last:border-b-0 pb-4 last:pb-0">
                    <div class="flex justify-between items-start mb-2">
                        <div>
                            <p class="font-bold">${res.evento?.nombre || 'Evento'}</p>
                            <p class="text-gray-600">Sector: ${res.sector?.nombre}</p>
                            <p class="text-gray-600">Asiento: ${res.asiento?.numero}</p>
                        </div>
                        <div class="text-right">
                            <p class="font-bold">$${precio}</p>
                            <p class="text-sm text-gray-500">${new Date(res.evento?.fecha).toLocaleDateString('es-ES')}</p>
                        </div>
                    </div>
                    <button onclick="eliminarDelCarrito(${res.id})" class="text-red-600 hover:text-red-800 text-sm">
                        Eliminar
                    </button>
                </div>
            `
        }).join('')

        resumen.innerHTML = `<p class="font-bold">Items: ${reservas.length}</p>`
        document.getElementById('total-precio').textContent = `$${totalPrecio}`
    } catch (error) {
        console.error('Error:', error)
    }
})

async function eliminarDelCarrito(id) {
    if (!confirm('¿Eliminar del carrito?')) return
    try {
        await window.api.delete(`/reservas/${id}`)
        location.reload()
    } catch (error) {
        alert('Error: ' + error.message)
    }
}

document.getElementById('btn-comprar')?.addEventListener('click', async () => {
    // Aquí iría la lógica para procesar la compra
    alert('Procesando pago...')
})
</script>
@endsection
