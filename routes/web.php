<?php

use Illuminate\Support\Facades\Route;

// Página de inicio
Route::get('/', function () {
    return redirect()->route('home');
})->name('/');

Route::get('/home', function () {
    return view('eventos.index');
})->name('home');

// Autenticación
Route::get('/login', function () {
    if (auth()->check()) {
        return redirect()->route('dashboard');
    }
    return view('auth.login');
})->name('login');

Route::get('/register', function () {
    if (auth()->check()) {
        return redirect()->route('dashboard');
    }
    return view('auth.register');
})->name('register');

// Rutas protegidas
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard.index');
    })->name('dashboard');

    Route::get('/carrito', function () {
        return view('carrito.index');
    })->name('carrito');

    Route::get('/entradas', function () {
        return view('entradas.index');
    })->name('entradas');

    // Admin routes
    Route::middleware('admin')->group(function () {
        Route::get('/admin', function () {
            return view('admin.dashboard');
        })->name('admin.dashboard');
    });
});

// Evento individual
Route::get('/eventos/{id}', function ($id) {
    return view('eventos.show', ['id' => $id]);
})->name('evento.show');
