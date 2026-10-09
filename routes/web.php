<?php

use App\Http\Controllers\ClienteController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\FichaController;
use App\Http\Controllers\JuegoController;
use App\Http\Controllers\MesaController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\VentaController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', DashboardController::class)
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Mesas
    Route::patch('mesas/{mesa}/restore', [MesaController::class, 'restore'])
        ->withTrashed()
        ->name('mesas.restore');
    Route::resource('mesas', MesaController::class)->except('show');

    // Juegos
    Route::patch('juegos/{juego}/restore', [JuegoController::class, 'restore'])
        ->withTrashed()
        ->name('juegos.restore');
    Route::resource('juegos', JuegoController::class)->except('show');

    // Clientes
    Route::patch('clientes/{cliente}/restore', [ClienteController::class, 'restore'])
        ->withTrashed()
        ->name('clientes.restore');
    Route::resource('clientes', ClienteController::class)->except('show');

    // Fichas (solo consulta; el stock lo mueven las ventas)
    Route::get('fichas', [FichaController::class, 'index'])->name('fichas.index');

    // Ventas de fichas
    Route::resource('ventas', VentaController::class)->only(['index', 'create', 'store', 'show', 'destroy']);
});

require __DIR__.'/auth.php';