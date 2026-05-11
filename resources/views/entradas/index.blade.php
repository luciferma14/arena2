@extends('layouts.app')

@section('title', 'Mis Entradas - TicketLand')

@section('content')
<div class="mb-8">
    <a href="{{ route('home') }}" class="text-cyan-400 hover:text-cyan-300">← Volver a Eventos</a>
    <h1 class="text-5xl font-black text-white mt-4">Mis Entradas</h1>
</div>

<div id="entradasContainer" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
    <div class="text-center text-slate-400 col-span-full py-12">Cargando entradas...</div>
</div>

<script>
const EntradasApp = {
    async init() {
        if (!Auth.get()) {
            document.getElementById('entradasContainer').innerHTML = `
                <div class="col-span-full text-center py-12">
                    <p class="text-slate-400 mb-4">Debes iniciar sesión para ver tus entradas</p>
                    <a href="{{ route('login') }}" class="text-cyan-400 hover:text-cyan-300 font-bold">Iniciar Sesión</a>
                </div>
            `;
            return;
        }

        try {
            const { data } = await window.axios.get('/entradas');
            const container = document.getElementById('entradasContainer');
            container.innerHTML = '';

            if (data && data.length > 0) {
                data.forEach(entrada => {
                    const card = document.createElement('div');
                    card.className = 'bg-slate-800/50 border border-slate-700 rounded-lg p-6 hover:border-cyan-500/50 transition';
                    card.innerHTML = `
                        <h3 class="text-lg font-bold text-white mb-3">${entrada.evento.nombre}</h3>
                        <div class="space-y-2 text-sm text-slate-300">
                            <p><strong>Sector:</strong> ${entrada.asiento.sector.nombre}</p>
                            <p><strong>Fila:</strong> ${entrada.asiento.fila}</p>
                            <p><strong>Asiento:</strong> ${entrada.asiento.numero}</p>
                            <p><strong>Precio:</strong> $${(entrada.precio || 0).toFixed(2)}</p>
                        </div>
                        <div class="mt-4 pt-4 border-t border-slate-700">
                            <p class="text-xs text-slate-500"><strong>Entrada:</strong> #${entrada.id}</p>
                        </div>
                    `;
                    container.appendChild(card);
                });
            } else {
                container.innerHTML = '<div class="col-span-full text-center py-12 text-slate-400">📭 Sin entradas aún. ¡Compra algunas!</div>';
            }
        } catch (err) {
            document.getElementById('entradasContainer').innerHTML = '<div class="col-span-full text-center py-12 text-red-400">⚠️ Error cargando entradas</div>';
        }
    }
};

document.addEventListener('DOMContentLoaded', () => EntradasApp.init());
</script>
@endsection
            container.innerHTML = '<div class="col-span-full text-center text-gray-600">No tienes entradas compradas aún</div>';
        }
    } catch (error) {
        console.error('Error:', error);
        document.getElementById('entradasContainer').innerHTML = '<div class="col-span-full text-center text-red-600">Error al cargar tus entradas</div>';
    }
});
</script>
@endsection
