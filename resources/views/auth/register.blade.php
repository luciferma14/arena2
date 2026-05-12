@extends('layouts.app')

@section('title', 'Registrarse - Roig Arena')

@section('content')
<div class="min-h-screen flex items-center justify-center -mx-6 -my-8">
    <div class="w-full max-w-md p-8 bg-slate-800 rounded-lg border border-slate-700 shadow-xl">
        <h1 class="text-3xl font-bold text-center mb-8 bg-gradient-to-r from-red-500 to-red-600 bg-clip-text text-transparent">
            Crear Cuenta
        </h1>

        <form action="{{ route('register') }}" method="POST" class="space-y-5">
            @csrf

            <!-- Nombre -->
            <div>
                <label for="nombre" class="block text-sm font-medium text-slate-300 mb-2">Nombre</label>
                <input
                    type="text"
                    id="nombre"
                    name="nombre"
                    value="{{ old('nombre') }}"
                    required
                    class="w-full px-4 py-2 bg-slate-700 border border-slate-600 rounded-lg text-white placeholder-slate-400 focus:outline-none focus:border-red-500 focus:ring-1 focus:ring-red-500 transition"
                    placeholder="Juan"
                >
                @error('nombre')
                    <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Apellido -->
            <div>
                <label for="apellido" class="block text-sm font-medium text-slate-300 mb-2">Apellido</label>
                <input
                    type="text"
                    id="apellido"
                    name="apellido"
                    value="{{ old('apellido') }}"
                    required
                    class="w-full px-4 py-2 bg-slate-700 border border-slate-600 rounded-lg text-white placeholder-slate-400 focus:outline-none focus:border-red-500 focus:ring-1 focus:ring-red-500 transition"
                    placeholder="Pérez"
                >
                @error('apellido')
                    <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Email -->
            <div>
                <label for="email" class="block text-sm font-medium text-slate-300 mb-2">Email</label>
                <input
                    type="email"
                    id="email"
                    name="email"
                    value="{{ old('email') }}"
                    required
                    class="w-full px-4 py-2 bg-slate-700 border border-slate-600 rounded-lg text-white placeholder-slate-400 focus:outline-none focus:border-red-500 focus:ring-1 focus:ring-red-500 transition"
                    placeholder="tu@email.com"
                >
                @error('email')
                    <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Password -->
            <div>
                <label for="password" class="block text-sm font-medium text-slate-300 mb-2">Contraseña</label>
                <input
                    type="password"
                    id="password"
                    name="password"
                    required
                    class="w-full px-4 py-2 bg-slate-700 border border-slate-600 rounded-lg text-white placeholder-slate-400 focus:outline-none focus:border-red-500 focus:ring-1 focus:ring-red-500 transition"
                    placeholder="••••••••"
                >
                @error('password')
                    <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Confirm Password -->
            <div>
                <label for="password_confirmation" class="block text-sm font-medium text-slate-300 mb-2">Confirmar Contraseña</label>
                <input
                    type="password"
                    id="password_confirmation"
                    name="password_confirmation"
                    required
                    class="w-full px-4 py-2 bg-slate-700 border border-slate-600 rounded-lg text-white placeholder-slate-400 focus:outline-none focus:border-red-500 focus:ring-1 focus:ring-red-500 transition"
                    placeholder="••••••••"
                >
            </div>

            <!-- Submit Button -->
            <button
                type="submit"
                class="w-full py-2 bg-gradient-to-r from-red-600 to-red-700 text-white font-semibold rounded-lg hover:from-red-700 hover:to-red-800 transition duration-200"
            >
                Registrarse
            </button>
        </form>

        <!-- Login Link -->
        <p class="text-center text-slate-400 text-sm mt-6">
            ¿Ya tienes cuenta?
            <a href="{{ route('login') }}" class="text-red-400 hover:text-red-300 font-semibold">
                Inicia sesión aquí
            </a>
        </p>
    </div>
</div>
@endsection
