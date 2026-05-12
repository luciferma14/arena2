<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;

Route::get('/', function () {
    return redirect()->route('home');
});

Route::get('/home', function () {
    return view('eventos.index');
})->name('home');

Route::get('/eventos/{id}', function ($id) {
    return view('eventos.show', ['id' => $id]);
})->name('evento.show');

Route::middleware('guest')->group(function () {
    Route::get('/login', fn() => view('auth.login'))->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.post');

    Route::get('/register', fn() => view('auth.register'))->name('register');
    Route::post('/register', [AuthController::class, 'register'])->name('register.post');
});

Route::post('/logout', [AuthController::class, 'logout'])
    ->middleware('auth:sanctum')
    ->name('logout');

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

    Route::middleware('admin')->group(function () {
        Route::get('/admin', function () {
            return view('admin.dashboard');
        })->name('admin.dashboard');
    });
});
