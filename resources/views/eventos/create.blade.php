@extends('layouts.app')

@section('title', 'Crear evento — Roig Arena')

@section('content')

<a href="{{ route('admin.index') }}" class="back-link">&#8592; Panel admin</a>

<div class="form-card" style="max-width:640px">
    <h1 class="form-title">Nuevo evento</h1>

    @if($errors->any())
        <div class="alert alert-error">
            <ul style="margin:0;padding-left:18px">
                @foreach($errors->all() as $e)
                    <li>{{ $e }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('eventos.store') }}">
        @csrf

        <div class="form-group">
            <label class="form-label" for="nombre">Nombre del evento</label>
            <input type="text" id="nombre" name="nombre" class="form-control"
                   value="{{ old('nombre') }}" required maxlength="255">
        </div>

        <div class="form-group">
            <label class="form-label" for="descripcion_corta">Descripción corta</label>
            <input type="text" id="descripcion_corta" name="descripcion_corta" class="form-control"
                   value="{{ old('descripcion_corta') }}" required maxlength="255">
            <p class="form-hint">Aparece en la tarjeta del evento (max 255 caracteres).</p>
        </div>

        <div class="form-group">
            <label class="form-label" for="descripcion_larga">Descripción completa</label>
            <textarea id="descripcion_larga" name="descripcion_larga" class="form-control"
                      rows="5" required>{{ old('descripcion_larga') }}</textarea>
        </div>

        <div class="form-group">
            <label class="form-label" for="poster_url">URL del póster (opcional)</label>
            <input type="url" id="poster_url" name="poster_url" class="form-control"
                   value="{{ old('poster_url') }}" placeholder="https://...">
        </div>

        <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px">
            <div class="form-group">
                <label class="form-label" for="fecha">Fecha</label>
                <input type="date" id="fecha" name="fecha" class="form-control"
                       value="{{ old('fecha') }}" required>
            </div>
            <div class="form-group">
                <label class="form-label" for="hora">Hora</label>
                <input type="time" id="hora" name="hora" class="form-control"
                       value="{{ old('hora') }}" required>
            </div>
        </div>

        <div class="form-group">
            <label class="form-label" for="precio_base">Precio base (EUR)</label>
            <input type="number" id="precio_base" name="precio_base" class="form-control"
                   value="{{ old('precio_base') }}" min="0" step="0.01" required>
            <p class="form-hint">Se asignara a todos los sectores activos del recinto.</p>
        </div>

        <div style="display:flex;gap:12px;margin-top:32px">
            <button type="submit" class="btn btn-gold btn-lg">Crear evento</button>
            <a href="{{ route('admin.index') }}" class="btn btn-ghost">Cancelar</a>
        </div>
    </form>
</div>

@endsection
