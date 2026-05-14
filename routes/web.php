<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Web\AuthWebController;
use App\Http\Controllers\Web\EventoWebController;
use App\Http\Controllers\Web\EntradaWebController;
use App\Http\Controllers\Web\AdminWebController;

// ── Home ─────────────────────────────────────────────────
Route::get('/', function () {
    return view('home');
})->name('home');

// ── Autenticación (solo guests) ──────────────────────────
Route::middleware('guest')->group(function () {
    Route::get('/login',     [AuthWebController::class, 'showLogin'])->name('login');
    Route::post('/login',    [AuthWebController::class, 'login'])->name('login.post');
    Route::get('/register',  [AuthWebController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthWebController::class, 'register'])->name('register.post');
});

Route::post('/logout', [AuthWebController::class, 'logout'])
    ->middleware('auth:sanctum')
    ->name('logout');

// ── Eventos: create ANTES de {id} para evitar conflicto de wildcard ──
Route::get('/eventos', [EventoWebController::class, 'index'])->name('eventos.index');

Route::middleware(['auth:sanctum', 'admin'])->group(function () {
    Route::get('/eventos/create', [EventoWebController::class, 'create'])->name('eventos.create');
    Route::post('/eventos',       [EventoWebController::class, 'store'])->name('eventos.store');
});

// Wildcard va después de la ruta estática /eventos/create
Route::get('/eventos/{id}', [EventoWebController::class, 'show'])->name('eventos.show');

// ── Rutas autenticadas ───────────────────────────────────
Route::middleware('auth:sanctum')->group(function () {

    Route::get('/dashboard',     [AuthWebController::class, 'dashboard'])->name('dashboard');
    Route::get('/mis-entradas',  [EntradaWebController::class, 'index'])->name('mis-entradas');
    Route::get('/entradas/{id}', [EntradaWebController::class, 'show'])->name('entradas.show');

    // ── Panel admin ───────────────────────────────────────
    Route::middleware('admin')->group(function () {
        Route::get('/admin',                    [AdminWebController::class, 'index'])->name('admin.index');
        Route::get('/admin/eventos/{id}/edit',  [AdminWebController::class, 'editEvento'])->name('admin.eventos.edit');
        Route::get('/admin/sectores/create',    [AdminWebController::class, 'createSector'])->name('admin.sectores.create');
        Route::get('/admin/sectores/{id}/edit', [AdminWebController::class, 'editSector'])->name('admin.sectores.edit');
    });
});
