@extends('layouts.app')

@section('title', 'Perfil - TicketLand')

@section('content')
<div class="mb-8">
    <h1 class="text-5xl font-black text-white">Mi Perfil</h1>
    <p class="text-slate-400 mt-2">Gestiona tu cuenta y accede a tus funciones</p>
</div>

<div class="grid gap-8 lg:grid-cols-3">
    <!-- Información del Usuario -->
    <div class="lg:col-span-2">
        <div class="rounded-3xl border border-slate-700 bg-gradient-to-br from-slate-900 to-slate-950 p-8">
            <h2 class="text-2xl font-bold text-white mb-6 flex items-center gap-3">
                <div class="w-8 h-8 bg-cyan-500 rounded-full flex items-center justify-center">
                    <span class="text-slate-950 text-sm font-bold">U</span>
                </div>
                Información Personal
            </h2>
            <div id="profileData" class="space-y-6">
                <div class="animate-pulse">
                    <div class="h-4 bg-slate-700 rounded w-3/4 mb-4"></div>
                    <div class="h-4 bg-slate-700 rounded w-1/2 mb-4"></div>
                    <div class="h-4 bg-slate-700 rounded w-2/3"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Panel de Acciones -->
    <div class="space-y-6">
        <div class="rounded-3xl border border-slate-700 bg-gradient-to-br from-slate-900 to-slate-950 p-6">
            <h3 class="text-xl font-bold text-white mb-4">Acciones Rápidas</h3>
            <div class="space-y-3">
                <a href="{{ route('home') }}" class="flex items-center gap-3 w-full p-4 rounded-2xl bg-cyan-500/10 border border-cyan-500/20 text-cyan-300 hover:bg-cyan-500/20 transition">
                    <span class="text-2xl">🎭</span>
                    <span class="font-semibold">Explorar Eventos</span>
                </a>
                <a href="{{ route('carrito') }}" class="flex items-center gap-3 w-full p-4 rounded-2xl bg-orange-500/10 border border-orange-500/20 text-orange-300 hover:bg-orange-500/20 transition">
                    <span class="text-2xl">🛒</span>
                    <span class="font-semibold">Mi Carrito</span>
                </a>
                <a href="{{ route('entradas') }}" class="flex items-center gap-3 w-full p-4 rounded-2xl bg-green-500/10 border border-green-500/20 text-green-300 hover:bg-green-500/20 transition">
                    <span class="text-2xl">🎫</span>
                    <span class="font-semibold">Mis Tickets</span>
                </a>
            </div>
        </div>

        <!-- Estadísticas -->
        <div class="rounded-3xl border border-slate-700 bg-gradient-to-br from-slate-900 to-slate-950 p-6">
            <h3 class="text-xl font-bold text-white mb-4">Estadísticas</h3>
            <div id="statsData" class="space-y-4">
                <div class="animate-pulse">
                    <div class="h-4 bg-slate-700 rounded w-full mb-3"></div>
                    <div class="h-4 bg-slate-700 rounded w-3/4"></div>
                </div>
            </div>
        </div>

        <!-- Cerrar Sesión -->
        <button onclick="ProfileApp.logout()" class="w-full p-4 rounded-2xl bg-red-500/10 border border-red-500/20 text-red-300 hover:bg-red-500/20 transition font-semibold">
            Cerrar Sesión
        </button>
    </div>
</div>

<script>
const ProfileApp = {
    profileData: document.getElementById('profileData'),
    statsData: document.getElementById('statsData'),

    async init() {
        const token = localStorage.getItem('auth_token');
        if (!token) {
            window.location.href = '{{ route("login") }}';
            return;
        }

        try {
            const [userRes, ticketsRes] = await Promise.all([
                fetch('/api/user', { headers: { 'Authorization': `Bearer ${token}` } }),
                fetch('/api/entradas', { headers: { 'Authorization': `Bearer ${token}` } })
            ]);

            const userData = await userRes.json();
            const ticketsData = await ticketsRes.json();

            this.renderProfile(userData.user);
            this.renderStats(ticketsData.data || []);
        } catch (err) {
            console.error('Error loading profile:', err);
            window.location.href = '{{ route("login") }}';
        }
    },

    renderProfile(user) {
        this.profileData.innerHTML = `
            <div class="flex items-center gap-4 mb-6">
                <div class="w-16 h-16 bg-gradient-to-br from-cyan-500 to-blue-600 rounded-full flex items-center justify-center text-2xl font-bold text-white">
                    ${user.nombre.charAt(0)}${user.apellido.charAt(0)}
                </div>
                <div>
                    <h4 class="text-xl font-bold text-white">${user.nombre} ${user.apellido}</h4>
                    <p class="text-slate-400">${user.email}</p>
                </div>
            </div>

            <div class="grid gap-4 md:grid-cols-2">
                <div class="p-4 rounded-xl bg-slate-800/50">
                    <p class="text-slate-400 text-sm mb-1">Tipo de Cuenta</p>
                    <p class="text-white font-semibold flex items-center gap-2">
                        ${user.is_admin ? '👑 Administrador' : '👤 Usuario'}
                        ${user.is_admin ? '<span class="text-xs bg-cyan-500/20 text-cyan-300 px-2 py-1 rounded-full">Premium</span>' : ''}
                    </p>
                </div>
                <div class="p-4 rounded-xl bg-slate-800/50">
                    <p class="text-slate-400 text-sm mb-1">Estado</p>
                    <p class="text-green-300 font-semibold flex items-center gap-2">
                        <span class="w-2 h-2 bg-green-400 rounded-full"></span>
                        Activo
                    </p>
                </div>
            </div>
        `;
    },

    renderStats(tickets) {
        const totalTickets = tickets.length;
        const totalSpent = tickets.reduce((sum, t) => sum + parseFloat(t.precio), 0);

        this.statsData.innerHTML = `
            <div class="flex justify-between items-center p-3 rounded-lg bg-slate-800/50">
                <span class="text-slate-400">Tickets Comprados</span>
                <span class="text-white font-bold text-xl">${totalTickets}</span>
            </div>
            <div class="flex justify-between items-center p-3 rounded-lg bg-slate-800/50">
                <span class="text-slate-400">Total Gastado</span>
                <span class="text-cyan-300 font-bold text-xl">$${totalSpent.toFixed(2)}</span>
            </div>
        `;
    },

    logout() {
        localStorage.removeItem('auth_token');
        window.location.href = '{{ route("home") }}';
    }
};

document.addEventListener('DOMContentLoaded', () => ProfileApp.init());
</script>
@endsection
