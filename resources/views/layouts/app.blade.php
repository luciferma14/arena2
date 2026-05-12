<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Roig Arena - Venta de Entradas')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        [x-cloak] { display: none; }
    </style>
</head>
<body class="bg-slate-950 text-slate-200">
    <!-- Header/Navbar -->
    <header class="sticky top-0 z-50 border-b border-slate-700/50 bg-slate-900/80 backdrop-blur-md">
        <div class="max-w-7xl mx-auto px-6 py-4 flex justify-between items-center">
            <a href="{{ route('home') }}" class="text-2xl font-bold tracking-wider bg-gradient-to-r from-red-500 to-red-600 bg-clip-text text-transparent">
                🎭 ROIG ARENA
            </a>

            <nav class="hidden md:flex gap-6 items-center">
                @auth
                    @if (auth()->user()->is_admin)
                        <a href="{{ route('admin.dashboard') }}" class="text-sm text-slate-300 hover:text-red-400 transition">
                            ⚙️ Admin
                        </a>
                    @endif
                    <a href="{{ route('home') }}" class="text-sm text-slate-300 hover:text-red-400 transition">
                        🎫 Eventos
                    </a>
                    <a href="{{ route('carrito') }}" class="text-sm text-slate-300 hover:text-red-400 transition relative">
                        🛒 Carrito
                        <span id="carrito-count" class="absolute -top-2 -right-2 bg-red-600 text-white text-xs rounded-full w-5 h-5 flex items-center justify-center">0</span>
                    </a>
                    <a href="{{ route('entradas') }}" class="text-sm text-slate-300 hover:text-red-400 transition">
                        🎟️ Mis Entradas
                    </a>
                    <a href="{{ route('dashboard') }}" class="text-sm text-slate-300 hover:text-red-400 transition">
                        👤 Mi Perfil
                    </a>
                    <button onclick="handleLogout()" class="text-xs px-4 py-2 rounded-md bg-red-600/10 text-red-400 border border-red-600/20 hover:bg-red-600/20 transition">
                        Cerrar Sesión
                    </button>
                @else
                    <a href="{{ route('home') }}" class="text-sm text-slate-300 hover:text-red-400 transition">
                        🎫 Eventos
                    </a>
                    <a href="{{ route('login') }}" class="text-sm text-slate-300 hover:text-red-400 transition">
                        Ingresar
                    </a>
                    <a href="{{ route('register') }}" class="text-xs px-4 py-2 rounded-md bg-red-600 text-white hover:bg-red-700 transition">
                        Registrarse
                    </a>
                @endauth
            </nav>

            <!-- Mobile Menu Button -->
            <button class="md:hidden text-slate-300 hover:text-red-400" onclick="toggleMobileMenu()">
                ☰
            </button>
        </div>

        <!-- Mobile Menu -->
        <div id="mobile-menu" class="hidden md:hidden border-t border-slate-700 bg-slate-800">
            <nav class="flex flex-col gap-3 px-6 py-4">
                @auth
                    @if (auth()->user()->is_admin)
                        <a href="{{ route('admin.dashboard') }}" class="text-sm text-slate-300 hover:text-red-400 transition">
                            ⚙️ Admin
                        </a>
                    @endif
                    <a href="{{ route('home') }}" class="text-sm text-slate-300 hover:text-red-400 transition">
                        🎫 Eventos
                    </a>
                    <a href="{{ route('carrito') }}" class="text-sm text-slate-300 hover:text-red-400 transition">
                        🛒 Carrito
                    </a>
                    <a href="{{ route('entradas') }}" class="text-sm text-slate-300 hover:text-red-400 transition">
                        🎟️ Mis Entradas
                    </a>
                    <a href="{{ route('dashboard') }}" class="text-sm text-slate-300 hover:text-red-400 transition">
                        👤 Mi Perfil
                    </a>
                    <button onclick="handleLogout()" class="text-xs px-4 py-2 rounded-md bg-red-600/10 text-red-400 border border-red-600/20 hover:bg-red-600/20 transition">
                        Cerrar Sesión
                    </button>
                @else
                    <a href="{{ route('home') }}" class="text-sm text-slate-300 hover:text-red-400 transition">
                        🎫 Eventos
                    </a>
                    <a href="{{ route('login') }}" class="text-sm text-slate-300 hover:text-red-400 transition">
                        Ingresar
                    </a>
                    <a href="{{ route('register') }}" class="text-xs px-4 py-2 rounded-md bg-red-600 text-white hover:bg-red-700 transition">
                        Registrarse
                    </a>
                @endauth
            </nav>
        </div>
    </header>

    <!-- Main Content -->
    <main class="min-h-screen">
        <div class="max-w-7xl mx-auto px-6 py-8">
            <!-- Alerts -->
            @if (session('success'))
                <div class="mb-6 p-4 rounded-lg bg-green-950/40 border-l-4 border-green-500 text-green-200 text-sm" id="alert-success">
                    ✅ {{ session('success') }}
                </div>
            @endif

            @if (session('error'))
                <div class="mb-6 p-4 rounded-lg bg-red-950/40 border-l-4 border-red-500 text-red-200 text-sm" id="alert-error">
                    ❌ {{ session('error') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="mb-6 p-4 rounded-lg bg-red-950/40 border-l-4 border-red-500 text-red-200 text-sm">
                    <p class="font-semibold mb-2">❌ Errores en el formulario:</p>
                    <ul class="list-disc list-inside">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @yield('content')
        </div>
    </main>

    <!-- Footer -->
    <footer class="border-t border-slate-700 bg-slate-900/50 text-slate-400 text-center py-6 mt-16">
        <p>&copy; {{ date('Y') }} Roig Arena. Todos los derechos reservados.</p>
    </footer>

    <script>
        // Funciones globales
        function toggleMobileMenu() {
            const menu = document.getElementById('mobile-menu');
            menu.classList.toggle('hidden');
        }

        function handleLogout() {
            if (confirm('¿Estás seguro de que deseas cerrar sesión?')) {
                fetch('/api/logout', {
                    method: 'POST',
                    headers: {
                        'Authorization': 'Bearer ' + localStorage.getItem('token'),
                        'Content-Type': 'application/json'
                    }
                }).then(() => {
                    localStorage.removeItem('token');
                    window.location.href = '{{ route("login") }}';
                });
            }
        }

        // Actualizar contador del carrito
        function actualizarCarrito() {
            const reservas = JSON.parse(localStorage.getItem('reservas') || '[]');
            const count = document.getElementById('carrito-count');
            if (count) {
                count.textContent = reservas.length;
                count.style.display = reservas.length > 0 ? 'flex' : 'none';
            }
        }

        // Cerrar alertas automáticamente
        setTimeout(() => {
            const alerts = document.querySelectorAll('[id^="alert-"]');
            alerts.forEach(alert => {
                alert.style.opacity = '1';
                setTimeout(() => {
                    alert.style.transition = 'opacity 0.5s';
                    alert.style.opacity = '0';
                    setTimeout(() => alert.remove(), 500);
                }, 5000);
            });
        }, 0);

        // Inicializar carrito al cargar
        actualizarCarrito();
        window.addEventListener('storage', actualizarCarrito);
    </script>

    @yield('scripts')
</body>
</html>
