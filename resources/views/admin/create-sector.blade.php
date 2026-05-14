@extends('layouts.app')

@section('title', 'Nuevo sector — Roig Arena')

@section('content')

<a href="{{ route('admin.index') }}" class="back-link">&#8592; Panel admin</a>

<div class="form-card" style="max-width:520px">
    <h1 class="form-title">Nuevo sector</h1>

    @if($errors->any())
        <div class="alert alert-error">
            <ul style="margin:0;padding-left:18px">
                @foreach($errors->all() as $e)
                    <li>{{ $e }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('admin.sectores.store') }}">
        @csrf

        <div class="form-group">
            <label class="form-label" for="nombre">Nombre del sector</label>
            <input type="text" id="nombre" name="nombre" class="form-control"
                   value="{{ old('nombre') }}" required maxlength="255"
                   placeholder="Ej: Pista, Platea Alta...">
            @error('nombre')
                <p class="form-error">{{ $message }}</p>
            @enderror
        </div>

        <div class="form-group">
            <label class="form-label" for="descripcion">Descripcion (opcional)</label>
            <textarea id="descripcion" name="descripcion" class="form-control"
                      rows="3" placeholder="Descripcion del sector...">{{ old('descripcion') }}</textarea>
        </div>

        <div class="form-group">
            <label class="form-label" for="precio_base">Precio base (EUR)</label>
            <input type="number" id="precio_base" name="precio_base" class="form-control"
                   value="{{ old('precio_base') }}" min="0" step="0.01" required>
            <p class="form-hint">Se asignara a todos los eventos existentes y futuros.</p>
            @error('precio_base')
                <p class="form-error">{{ $message }}</p>
            @enderror
        </div>

        <div class="form-group">
            <label class="form-check">
                <input type="checkbox" name="activo" value="1"
                       {{ old('activo', '1') ? 'checked' : '' }}>
                Sector activo (disponible para venta)
            </label>
        </div>

        <div style="display:flex;gap:12px;margin-top:32px">
            <button type="submit" class="btn btn-gold btn-lg">Crear sector</button>
            <a href="{{ route('admin.index') }}" class="btn btn-ghost">Cancelar</a>
        </div>
    </form>
</div>

@endsection
