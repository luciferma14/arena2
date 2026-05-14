@extends('layouts.app')

@section('title', 'Iniciar sesión — Roig Arena')

@section('content')

<div style="min-height:60vh;display:flex;align-items:center;justify-content:center;padding:40px 0">
    <div class="form-card">
        <h1 class="form-title">Iniciar sesión</h1>

        @if($errors->any())
            <div class="alert alert-error">
                @foreach($errors->all() as $e)
                    <p style="margin:0">{{ $e }}</p>
                @endforeach
            </div>
        @endif

        <form method="POST" action="{{ route('login.post') }}">
            @csrf

            <div class="form-group">
                <label class="form-label" for="email">Correo electrónico</label>
                <input type="email" id="email" name="email" class="form-control"
                       value="{{ old('email') }}" required autocomplete="email" autofocus>
                @error('email')
                    <p class="form-error">{{ $message }}</p>
                @enderror
            </div>

            <div class="form-group">
                <label class="form-label" for="password">Contraseña</label>
                <input type="password" id="password" name="password" class="form-control"
                       required autocomplete="current-password">
                @error('password')
                    <p class="form-error">{{ $message }}</p>
                @enderror
            </div>

            <div style="margin-top:28px">
                <button type="submit" class="btn btn-gold btn-full">Entrar</button>
            </div>
        </form>

        <p class="form-footer">
            ¿Sin cuenta? <a href="{{ route('register') }}">Regístrate aquí</a>
        </p>
    </div>
</div>

@endsection
