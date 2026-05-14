<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token"  content="{{ csrf_token() }}">
    <meta name="api-token"   content="{{ session('api_token', '') }}">
    <title>@yield('title', 'Roig Arena')</title>
    <link rel="stylesheet" href="{{ asset('css/arena.css') }}">
</head>
<body>

<nav class="navbar">
    <div class="container navbar-inner">
        <a href="{{ route('home') }}" class="navbar-brand">Roig Arena</a>

        <div class="navbar-links">
            <a href="{{ route('eventos.index') }}" class="nav-link">Eventos</a>

            @auth
                <span class="nav-user">
                    {{ auth()->user()->nombre ?? auth()->user()->name ?? 'Usuario' }}
                </span>
                <a href="{{ route('dashboard') }}" class="nav-link">Mi cuenta</a>
                <a href="{{ route('mis-entradas') }}" class="nav-link">Entradas</a>
                @if(auth()->user()->isAdmin())
                    <a href="{{ route('admin.index') }}" class="nav-link">Admin</a>
                @endif
                <form method="POST" action="{{ route('logout') }}" style="display:inline">
                    @csrf
                    <button type="submit" class="nav-btn">Salir</button>
                </form>
            @else
                <a href="{{ route('login') }}" class="nav-btn">Entrar</a>
                <a href="{{ route('register') }}" class="nav-btn nav-btn-filled">Registro</a>
            @endauth
        </div>
    </div>
</nav>

<main>
    <div class="container">
        @if(session('success'))
            <div class="alert alert-success" style="margin-bottom:24px">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="alert alert-error" style="margin-bottom:24px">{{ session('error') }}</div>
        @endif

        @yield('content')
    </div>
</main>

<footer>
    <div class="container">
        &copy; {{ date('Y') }} Roig Arena. Todos los derechos reservados.
    </div>
</footer>

{{-- Interceptor global de fetch: añade X-CSRF-TOKEN + Authorization a todas las mutaciones /api --}}
<script>
window.csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
window.apiToken  = document.querySelector('meta[name="api-token"]')?.content || '';

(function () {
    const _fetch = window.fetch;
    window.fetch = function (url, opts) {
        opts = opts || {};
        const method = (opts.method || 'GET').toUpperCase();
        const isMutable = ['POST', 'PUT', 'DELETE', 'PATCH'].includes(method);
        if (isMutable && typeof url === 'string' && url.startsWith('/api')) {
            opts.headers = Object.assign({
                'X-CSRF-TOKEN':  window.csrfToken || '',
                'Authorization': 'Bearer ' + (window.apiToken || ''),
                'Accept':        'application/json',
            }, opts.headers || {});
        }
        if (typeof url === 'string' && url.startsWith('/api') && !isMutable) {
            opts.headers = Object.assign({
                'Accept': 'application/json',
            }, opts.headers || {});
        }
        return _fetch.call(this, url, opts);
    };
})();

function fmtFecha(str) {
    if (!str) return '-';
    const d = new Date(str);
    if (isNaN(d)) return str;
    return d.toLocaleDateString('es-ES', { day:'2-digit', month:'2-digit', year:'numeric' });
}

function fmtHora(h) {
    if (!h) return '-';
    if (h.length > 5) return h.substring(11, 16);
    return h.substring(0, 5);
}
</script>

@yield('scripts')
</body>
</html>
