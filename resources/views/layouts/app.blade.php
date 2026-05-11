<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Roig Arena')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body class="bg-slate-950">
    <!-- Header -->
    <header class="sticky top-0 z-50 border-b border-slate-700/50 bg-slate-900/80 backdrop-blur-md">
        <div class="max-w-7xl mx-auto px-6 py-3 flex justify-between items-center">
            <a href="{{ route('home') }}" class="text-xl font-black tracking-wider bg-gradient-to-r from-indigo-400 via-purple-400 to-pink-400 bg-clip-text text-transparent">
                ✨ TICKETLAND
            </a>

            <nav class="hidden md:flex gap-8 items-center">
                @auth
                    <a href="{{ route('carrito') }}" class="text-slate-300 text-sm hover:text-indigo-400 transition">
                        🛒 Carrito
                    </a>
                    <a href="{{ route('entradas') }}" class="text-slate-300 text-sm hover:text-indigo-400 transition">
                        🎟️ Mis Entradas
                    </a>
                    <a href="{{ route('dashboard') }}" class="text-slate-300 text-sm hover:text-indigo-400 transition">
                        ⚙️ Cuenta
                    </a>
                    <button onclick="handleLogout()" class="text-xs px-3 py-1.5 rounded-md bg-red-600/10 text-red-400 border border-red-600/20 hover:bg-red-600/20 transition">
                        Cerrar
                    </button>
                @else
                    <a href="{{ route('login') }}" class="text-slate-300 text-sm hover:text-indigo-400">
                        Ingresar
                    </a>
                    <a href="{{ route('register') }}" class="text-xs px-3 py-1.5 rounded-md bg-indigo-600 text-white hover:bg-indigo-700">
                        Crear Cuenta
                    </a>
                @endauth
            </nav>
        </div>
    </header>

    <!-- Main Content -->
    <main class="min-h-screen">
        <div class="max-w-7xl mx-auto px-6 py-8">
            @if (session('error'))
                <div class="mb-6 p-4 rounded-lg bg-red-950/40 border-l-4 border-red-500 text-red-200 text-sm">
                    ❌ {{ session('error') }}
                </div>
            @endif

            @if (session('success'))
                <div class="mb-6 p-4 rounded-lg bg-green-950/40 border-l-4 border-green-500 text-green-200 text-sm">
                    ✅ {{ session('success') }}
                </div>
            @endif

            @yield('content')
        </div>
    </main>

    <!-- Footer -->
    <footer class="border-t border-slate-700/50 bg-slate-900/50 py-12 mt-16">
        <div class="max-w-7xl mx-auto px-6 text-center text-slate-400 text-xs">
            <p>TicketLand © 2026 | Plataforma de Eventos</p>
        </div>
    </footer>

    <script>
        // Configuración global de Axios
        window.axios = axios;
        window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
        window.axios.defaults.headers.common['X-CSRF-TOKEN'] = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        window.axios.defaults.baseURL = '/api';

        const TOKEN_KEY = 'auth_token';

        const Auth = {
            get() { return localStorage.getItem(TOKEN_KEY); },
            set(token) {
                localStorage.setItem(TOKEN_KEY, token);
                window.axios.defaults.headers.common['Authorization'] = `Bearer ${token}`;
            },
            clear() {
                localStorage.removeItem(TOKEN_KEY);
                delete window.axios.defaults.headers.common['Authorization'];
            }
        };

        const handleLogout = async () => {
            if (confirm('¿Deseas cerrar sesión?')) {
                try {
                    await window.axios.post('/logout');
                } catch(e) {}
                finally {
                    Auth.clear();
                    location.href = '/';
                }
            }
        };

        // Initialize auth on page load
        if (Auth.get()) Auth.set(Auth.get());
    </script>

    @yield('scripts')
</body>
</html>
