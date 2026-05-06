@extends('layouts.app')

@section('title', 'Mis Entradas - Roig Arena')

@section('content')
<div class="mb-8">
    <a href="{{ route('home') }}" class="text-red-600 hover:underline">← Volver a Eventos</a>
    <h1 class="text-4xl font-bold text-gray-800 dark:text-white mt-4">Mis Entradas</h1>
</div>

<div id="entradasContainer" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
    <div class="text-center text-gray-600">Cargando entradas...</div>
</div>

<script>
document.addEventListener('DOMContentLoaded', async () => {
    const token = localStorage.getItem('token');

    if (!token) {
        document.getElementById('entradasContainer').innerHTML = `
            <div class="col-span-full text-center">
                <p class="text-gray-600 mb-4">Debes iniciar sesión para ver tus entradas</p>
                <a href="{{ route('login') }}" class="text-red-600 hover:underline font-bold">Iniciar Sesión</a>
            </div>
        `;
        return;
    }

    try {
        const response = await fetch('/api/entradas', {
            headers: {
                'Authorization': `Bearer ${token}`
            }
        });

        const data = await response.json();
        const container = document.getElementById('entradasContainer');
        container.innerHTML = '';

        if (data.data && data.data.length > 0) {
            data.data.forEach(entrada => {
                const card = document.createElement('div');
                card.className = 'bg-white dark:bg-gray-800 rounded-lg shadow-lg p-6 border-l-4 border-red-600';
                card.innerHTML = `
                    <h3 class="text-xl font-bold text-gray-800 dark:text-white mb-3">${entrada.evento.nombre}</h3>
                    <div class="space-y-2 text-sm text-gray-600 dark:text-gray-400">
                        <p><strong>Sector:</strong> ${entrada.asiento.sector.nombre}</p>
                        <p><strong>Fila:</strong> ${entrada.asiento.fila}</p>
                        <p><strong>Asiento:</strong> ${entrada.asiento.numero}</p>
                        <p><strong>Precio:</strong> $${entrada.precio}</p>
                    </div>
                    <div class="mt-4 pt-4 border-t border-gray-200 dark:border-gray-700">
                        <p class="text-xs text-gray-500">
                            <strong>Entrada:</strong> #${entrada.id}
                        </p>
                    </div>
                `;
                container.appendChild(card);
            });
        } else {
            container.innerHTML = '<div class="col-span-full text-center text-gray-600">No tienes entradas compradas aún</div>';
        }
    } catch (error) {
        console.error('Error:', error);
        document.getElementById('entradasContainer').innerHTML = '<div class="col-span-full text-center text-red-600">Error al cargar tus entradas</div>';
    }
});
</script>
@endsection
