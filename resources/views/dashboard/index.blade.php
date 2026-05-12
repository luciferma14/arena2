@extends('layouts.app')

@section('title', 'Mi Dashboard - Roig Arena')

@section('content')
<div class="space-y-8">
    <!-- Header -->
    <div class="mb-12">
        <h1 class="text-4xl font-bold mb-3 text-white">
            👤 Mi Perfil
        </h1>
        <p class="text-slate-400">Información de tu cuenta</p>
    </div>

    <!-- User Info Card -->
    <div class="bg-slate-800 rounded-lg border border-slate-700 p-8 max-w-2xl">
        <div class="space-y-6">
            <div>
                <p class="text-slate-400 text-sm mb-2">Nombre Completo</p>
                <p class="text-xl font-bold text-white" id="user-name">{{ auth()->user()->nombre }} {{ auth()->user()->apellido }}</p>
            </div>

            <div>
                <p class="text-slate-400 text-sm mb-2">Email</p>
                <p class="text-xl font-bold text-white" id="user-email">{{ auth()->user()->email }}</p>
            </div>

            <div>
                <p class="text-slate-400 text-sm mb-2">Estado</p>
                <p class="text-xl font-bold">
                    @if (auth()->user()->is_admin)
                        <span class="text-blue-400">👑 Administrador</span>
                    @else
                        <span class="text-green-400">✅ Usuario Regular</span>
                    @endif
                </p>
            </div>

            <div class="border-t border-slate-600 pt-6">
                <p class="text-slate-400 text-sm mb-2">Miembro desde</p>
                <p class="text-white">{{ auth()->user()->created_at->format('d/m/Y') }}</p>
            </div>
        </div>
    </div>

    <!-- Quick Stats -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-slate-800 rounded-lg border border-slate-700 p-6 text-center">
            <p class="text-4xl mb-2">🎫</p>
            <p class="text-slate-400 text-sm mb-2">Mis Entradas</p>
            <a href="{{ route('entradas') }}" class="text-red-400 hover:text-red-300 font-bold">
                Ver Todas →
            </a>
        </div>

        <div class="bg-slate-800 rounded-lg border border-slate-700 p-6 text-center">
            <p class="text-4xl mb-2">🎭</p>
            <p class="text-slate-400 text-sm mb-2">Próximos Eventos</p>
            <a href="{{ route('home') }}" class="text-red-400 hover:text-red-300 font-bold">
                Explorar →
            </a>
        </div>

        <div class="bg-slate-800 rounded-lg border border-slate-700 p-6 text-center">
            <p class="text-4xl mb-2">🛒</p>
            <p class="text-slate-400 text-sm mb-2">Carrito</p>
            <a href="{{ route('carrito') }}" class="text-red-400 hover:text-red-300 font-bold">
                Ver Carrito →
            </a>
        </div>
    </div>

    <!-- Security Section -->
    <div class="bg-slate-800 rounded-lg border border-slate-700 p-8 max-w-2xl">
        <h2 class="text-2xl font-bold text-white mb-6">🔒 Seguridad</h2>

        <form method="POST" action="{{ route('logout') }}" class="space-y-4">
            @csrf

            <p class="text-slate-400 text-sm">
                Para cambiar tu contraseña o cerrar sesión desde otros dispositivos, por favor contacta con el administrador.
            </p>

            <button type="submit" class="px-6 py-2 bg-red-600/10 text-red-400 border border-red-600/20 hover:bg-red-600/20 rounded-lg transition font-semibold">
                🚪 Cerrar Sesión
            </button>
        </form>
    </div>
</div>
@endsection
