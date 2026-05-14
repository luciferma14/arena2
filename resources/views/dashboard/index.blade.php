@extends('layouts.app')

@section('title', 'Mi cuenta — Roig Arena')

@section('content')

<div class="page-header">
    <p class="page-header-label">Bienvenido</p>
    <h1 class="page-header-title">
        {{ trim((auth()->user()->nombre ?? '') . ' ' . (auth()->user()->apellido ?? '')) ?: 'Usuario' }}
    </h1>
</div>

<div class="dash-grid">
    <div class="dash-card">
        <p class="dash-card-label">Entradas compradas</p>
        <p class="dash-card-value">{{ count($entradas) }}</p>
        <a href="{{ route('mis-entradas') }}" class="dash-card-link">Ver todas &rarr;</a>
    </div>
    <div class="dash-card">
        <p class="dash-card-label">Explorar</p>
        <p class="dash-card-value" style="font-size:1.2rem;padding-top:8px">Proximos eventos</p>
        <a href="{{ route('eventos.index') }}" class="dash-card-link">Ver cartelera &rarr;</a>
    </div>
    @if(auth()->user()->isAdmin())
    <div class="dash-card">
        <p class="dash-card-label">Administracion</p>
        <p class="dash-card-value" style="font-size:1.2rem;padding-top:8px">Panel admin</p>
        <a href="{{ route('admin.index') }}" class="dash-card-link">Ir al panel &rarr;</a>
    </div>
    @endif
</div>

<div class="panel">
    <div class="panel-header">
        <span class="panel-title">Ultimas entradas</span>
        <a href="{{ route('mis-entradas') }}" class="btn btn-ghost btn-sm">Ver todas</a>
    </div>

    @if(count($entradas) === 0)
        <div class="panel-body">
            <div class="state-msg">
                <p class="state-msg-title">Sin entradas todavia</p>
                <p>Compra tus primeras entradas en la <a href="{{ route('eventos.index') }}">cartelera</a>.</p>
            </div>
        </div>
    @else
        <div class="table-wrap">
            <table class="table">
                <thead>
                    <tr>
                        <th>Evento</th>
                        <th>Asiento</th>
                        <th>Fecha</th>
                        <th>Precio</th>
                        <th>Estado</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($entradas as $e)
                    <tr>
                        <td>{{ $e['evento'] ?? '-' }}</td>
                        <td>{{ $e['asiento'] ?? '-' }}</td>
                        <td>{{ $e['fecha'] ?? '-' }}</td>
                        <td>{{ $e['precio'] ?? '-' }}</td>
                        <td>
                            @if($e['valida'] ?? false)
                                <span class="badge badge-success">Valida</span>
                            @else
                                <span class="badge badge-danger">Expirada</span>
                            @endif
                        </td>
                        <td>
                            <a href="{{ route('entradas.show', $e['id']) }}" class="btn btn-ghost btn-sm">Ver QR</a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>

@endsection
