@extends('layouts.app')

@section('content')
<div class="py-8">
    <h1 class="text-4xl font-bold mb-8 text-gray-800">Panel de Administración</h1>

    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <!-- Eventos -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-lg font-bold text-gray-800 mb-2">Eventos</h2>
            <p class="text-3xl font-bold text-blue-600" id="count-eventos">0</p>
            <a href="#" onclick="mostrarSeccion('eventos')" class="text-blue-600 hover:text-blue-800 mt-4 inline-block">Gestionar →</a>
        </div>

        <!-- Sectores -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-lg font-bold text-gray-800 mb-2">Sectores</h2>
            <p class="text-3xl font-bold text-green-600" id="count-sectores">0</p>
            <a href="#" onclick="mostrarSeccion('sectores')" class="text-green-600 hover:text-green-800 mt-4 inline-block">Gestionar →</a>
        </div>

        <!-- Artistas -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-lg font-bold text-gray-800 mb-2">Artistas</h2>
            <p class="text-3xl font-bold text-purple-600" id="count-artistas">0</p>
            <a href="#" onclick="mostrarSeccion('artistas')" class="text-purple-600 hover:text-purple-800 mt-4 inline-block">Gestionar →</a>
        </div>

        <!-- Entradas -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-lg font-bold text-gray-800 mb-2">Entradas Vendidas</h2>
            <p class="text-3xl font-bold text-orange-600" id="count-entradas">0</p>
        </div>
    </div>

    <!-- Secciones de Gestión -->
    <div id="seccion-eventos" class="seccion-admin hidden">
        <div class="bg-white rounded-lg shadow-md p-6 mb-8">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-2xl font-bold">Gestionar Eventos</h2>
                <button onclick="showFormEvento()" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                    + Nuevo Evento
                </button>
            </div>
            <div id="lista-eventos" class="space-y-2">
                <p class="text-gray-600">Cargando...</p>
            </div>
        </div>
    </div>

    <div id="seccion-sectores" class="seccion-admin hidden">
        <div class="bg-white rounded-lg shadow-md p-6 mb-8">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-2xl font-bold">Gestionar Sectores</h2>
                <button onclick="showFormSector()" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">
                    + Nuevo Sector
                </button>
            </div>
            <div id="lista-sectores" class="space-y-2">
                <p class="text-gray-600">Cargando...</p>
            </div>
        </div>
    </div>

    <div id="seccion-artistas" class="seccion-admin hidden">
        <div class="bg-white rounded-lg shadow-md p-6 mb-8">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-2xl font-bold">Gestionar Artistas</h2>
                <button onclick="showFormArtista()" class="bg-purple-600 text-white px-4 py-2 rounded hover:bg-purple-700">
                    + Nuevo Artista
                </button>
            </div>
            <div id="lista-artistas" class="space-y-2">
                <p class="text-gray-600">Cargando...</p>
            </div>
        </div>
    </div>
</div>

<script>
async function cargarDatos() {
    try {
        // Cargar eventos
        const eventosRes = await window.api.get('/eventos')
        document.getElementById('count-eventos').textContent = (eventosRes.data || []).length

        // Cargar sectores
        const sectoresRes = await window.api.get('/admin/sectores')
        document.getElementById('count-sectores').textContent = (sectoresRes.data || []).length

        // Cargar artistas
        const artistasRes = await window.api.get('/admin/artistas')
        document.getElementById('count-artistas').textContent = (artistasRes.data || []).length

        // Cargar entradas
        const entradasRes = await window.api.get('/admin/entradas')
        document.getElementById('count-entradas').textContent = (entradasRes.data || []).length
    } catch (error) {
        console.error('Error cargando datos:', error)
    }
}

function mostrarSeccion(seccion) {
    document.querySelectorAll('.seccion-admin').forEach(el => el.classList.add('hidden'))
    const sectionEl = document.getElementById(`seccion-${seccion}`)
    if (sectionEl) {
        sectionEl.classList.remove('hidden')
        cargarListado(seccion)
    }
}

async function cargarListado(seccion) {
    try {
        let endpoint = ''
        let container = ''

        if (seccion === 'eventos') {
            endpoint = '/eventos'
            container = 'lista-eventos'
            const res = await window.api.get(endpoint)
            const items = res.data || []
            document.getElementById(container).innerHTML = items.map(item => `
                <div class="bg-gray-50 p-4 rounded flex justify-between items-center">
                    <div>
                        <p class="font-bold">${item.nombre}</p>
                        <p class="text-sm text-gray-600">${new Date(item.fecha).toLocaleDateString('es-ES')}</p>
                    </div>
                    <div class="space-x-2">
                        <button onclick="editarEvento(${item.id})" class="bg-blue-600 text-white px-3 py-1 rounded text-sm">Editar</button>
                        <button onclick="eliminarEvento(${item.id})" class="bg-red-600 text-white px-3 py-1 rounded text-sm">Eliminar</button>
                    </div>
                </div>
            `).join('')
        }
    } catch (error) {
        console.error('Error:', error)
    }
}

function showFormEvento() {
    alert('Crear nuevo evento - Formulario próximamente')
}

function showFormSector() {
    alert('Crear nuevo sector - Formulario próximamente')
}

function showFormArtista() {
    alert('Crear nuevo artista - Formulario próximamente')
}

cargarDatos()
mostrarSeccion('eventos')
</script>
@endsection
