@extends('layouts.app')

@section('title', 'Registro — Roig Arena')

@section('content')

<div style="min-height:60vh;display:flex;align-items:center;justify-content:center;padding:40px 0">
    <div class="form-card">
        <h1 class="form-title">Crear cuenta</h1>

        @if($errors->any())
            <div class="alert alert-error">
                <ul style="margin:0;padding-left:18px">
                    @foreach($errors->all() as $e)
                        <li>{{ $e }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('register.post') }}">
            @csrf

            <div class="form-group">
                <label class="form-label" for="name">Nombre completo</label>
                <input type="text" id="name" name="name" class="form-control"
                       value="{{ old('name') }}" required autocomplete="name" autofocus>
                @error('name')
                    <p class="form-error">{{ $message }}</p>
                @enderror
            </div>

            <div class="form-group">
                <label class="form-label" for="email">Correo electronico</label>
                <input type="email" id="email" name="email" class="form-control"
                       value="{{ old('email') }}" required autocomplete="email">
                @error('email')
                    <p class="form-error">{{ $message }}</p>
                @enderror
            </div>

            <div class="form-group">
                <label class="form-label" for="password">Contrasena</label>
                <input type="password" id="password" name="password" class="form-control"
                       required autocomplete="new-password">
                <p class="form-hint">Minimo 8 caracteres.</p>
                @error('password')
                    <p class="form-error">{{ $message }}</p>
                @enderror
            </div>

            <div class="form-group">
                <label class="form-label" for="password_confirmation">Confirmar contrasena</label>
                <input type="password" id="password_confirmation" name="password_confirmation"
                       class="form-control" required autocomplete="new-password">
            </div>

            <div style="margin-top:28px">
                <button type="submit" class="btn btn-gold btn-full">Crear cuenta</button>
            </div>
        </form>

        <p class="form-footer">
            Ya tienes cuenta? <a href="{{ route('login') }}">Inicia sesion</a>
        </p>
    </div>
</div>

@endsection
