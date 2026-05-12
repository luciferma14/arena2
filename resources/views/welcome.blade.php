@extends('layouts.app')

@section('title', 'Roig Arena - Venta de Entradas para Eventos')

@section('content')
<div class="space-y-16">
    <!-- Hero Section -->
    <div class="min-h-screen flex items-center justify-center -mx-6 -my-8">
        <div class="max-w-4xl mx-auto px-6 text-center">
            <div class="mb-8">
                <span class="text-7xl animate-pulse">🎭</span>
            </div>
            <h1 class="text-6xl md:text-7xl font-bold mb-6 text-white">
                Roig Arena
            </h1>
            <p class="text-2xl text-slate-300 mb-6">
                Sistema de Venta de Entradas para Eventos
            </p>
            <p class="text-lg text-slate-400 mb-12">
                Compra tus entradas de forma segura y rápida. Múltiples sectores, asientos numerados y compra en línea garantizada.
            </p>

            <div class="flex gap-4 justify-center flex-wrap">
                <a href="{{ route('home') }}" class="px-8 py-4 bg-gradient-to-r from-red-600 to-red-700 text-white font-bold text-lg rounded-lg hover:from-red-700 hover:to-red-800 transition duration-200">
                    🎫 Ver Eventos
                </a>
                @guest
                    <a href="{{ route('register') }}" class="px-8 py-4 bg-slate-700 hover:bg-slate-600 text-white font-bold text-lg rounded-lg transition duration-200">
                        👤 Registrarse
                    </a>
                @endguest
            </div>
        </div>
    </div>

    <!-- Features Section -->
    <div class="max-w-6xl mx-auto px-6">
        <h2 class="text-4xl font-bold text-center mb-16 text-white">¿Por Qué Elegir Roig Arena?</h2>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
            <div class="bg-slate-800 rounded-lg border border-slate-700 p-6 text-center hover:border-red-500 transition">
                <p class="text-4xl mb-4">🎫</p>
                <h3 class="text-lg font-bold text-white mb-2">Entradas Seguras</h3>
                <p class="text-slate-400">Todas nuestras entradas son auténticas y seguras</p>
            </div>

            <div class="bg-slate-800 rounded-lg border border-slate-700 p-6 text-center hover:border-red-500 transition">
                <p class="text-4xl mb-4">⚡</p>
                <h3 class="text-lg font-bold text-white mb-2">Compra Rápida</h3>
                <p class="text-slate-400">Compra tus entradas en menos de 2 minutos</p>
            </div>

            <div class="bg-slate-800 rounded-lg border border-slate-700 p-6 text-center hover:border-red-500 transition">
                <p class="text-4xl mb-4">🛡️</p>
                <h3 class="text-lg font-bold text-white mb-2">Pagos Seguros</h3>
                <p class="text-slate-400">Protegemos tus datos con encriptación</p>
            </div>

            <div class="bg-slate-800 rounded-lg border border-slate-700 p-6 text-center hover:border-red-500 transition">
                <p class="text-4xl mb-4">📱</p>
                <h3 class="text-lg font-bold text-white mb-2">QR Instant</h3>
                <p class="text-slate-400">Recibe tu código QR inmediatamente</p>
            </div>
        </div>
    </div>

    <!-- How it Works -->
    <div class="max-w-6xl mx-auto px-6">
        <h2 class="text-4xl font-bold text-center mb-16 text-white">¿Cómo Funciona?</h2>

        <div class="space-y-8">
            <div class="flex gap-6 items-start">
                <div class="flex-shrink-0 w-12 h-12 bg-red-600 rounded-full flex items-center justify-center text-white font-bold text-lg">1</div>
                <div>
                    <h3 class="text-xl font-bold text-white mb-2">Crea tu Cuenta</h3>
                    <p class="text-slate-400">Regístrate con tu email y contraseña en menos de 1 minuto</p>
                </div>
            </div>

            <div class="flex gap-6 items-start">
                <div class="flex-shrink-0 w-12 h-12 bg-red-600 rounded-full flex items-center justify-center text-white font-bold text-lg">2</div>
                <div>
                    <h3 class="text-xl font-bold text-white mb-2">Elige tu Evento</h3>
                    <p class="text-slate-400">Explora todos los eventos disponibles y elige el que más te interese</p>
                </div>
            </div>

            <div class="flex gap-6 items-start">
                <div class="flex-shrink-0 w-12 h-12 bg-red-600 rounded-full flex items-center justify-center text-white font-bold text-lg">3</div>
                <div>
                    <h3 class="text-xl font-bold text-white mb-2">Selecciona tu Asiento</h3>
                    <p class="text-slate-400">Visualiza todos los asientos disponibles y elige los que más te gusten</p>
                </div>
            </div>

            <div class="flex gap-6 items-start">
                <div class="flex-shrink-0 w-12 h-12 bg-red-600 rounded-full flex items-center justify-center text-white font-bold text-lg">4</div>
                <div>
                    <h3 class="text-xl font-bold text-white mb-2">Completa tu Compra</h3>
                    <p class="text-slate-400">Revisa tu carrito y confirma la compra de forma segura</p>
                </div>
            </div>

            <div class="flex gap-6 items-start">
                <div class="flex-shrink-0 w-12 h-12 bg-red-600 rounded-full flex items-center justify-center text-white font-bold text-lg">5</div>
                <div>
                    <h3 class="text-xl font-bold text-white mb-2">Recibe tu Código QR</h3>
                    <p class="text-slate-400">Recibe instantáneamente tu código QR en tu cuenta</p>
                </div>
            </div>
        </div>
    </div>

    <!-- CTA Section -->
    <div class="max-w-4xl mx-auto px-6 text-center py-16">
        <h2 class="text-4xl font-bold mb-6 text-white">¡Comienza Ahora!</h2>
        <p class="text-xl text-slate-400 mb-8">
            Únete a miles de usuarios que disfrutan comprando sus entradas de forma rápida y segura
        </p>
        <div class="flex gap-4 justify-center">
            @guest
                <a href="{{ route('register') }}" class="px-8 py-4 bg-gradient-to-r from-red-600 to-red-700 text-white font-bold text-lg rounded-lg hover:from-red-700 hover:to-red-800 transition duration-200">
                    Crear Cuenta Gratis
                </a>
                <a href="{{ route('login') }}" class="px-8 py-4 bg-slate-700 hover:bg-slate-600 text-white font-bold text-lg rounded-lg transition duration-200">
                    Inicia Sesión
                </a>
            @else
                <a href="{{ route('home') }}" class="px-8 py-4 bg-gradient-to-r from-red-600 to-red-700 text-white font-bold text-lg rounded-lg hover:from-red-700 hover:to-red-800 transition duration-200">
                    Ver Eventos
                </a>
            @endguest
        </div>
    </div>
</div>
@endsection
