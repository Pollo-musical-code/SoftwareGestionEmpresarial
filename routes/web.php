<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\MesaController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect('/login');
});

Route::get('/dashboard', function () {
    return view('dashboard', [
        'mesasActivas' => \App\Models\Mesa::where('estado', 'abierta')->count(),
        'totalClientes' => \App\Models\Cliente::count(),
        'fichasCirculacion' => \App\Models\Cliente::sum('saldo_fichas'),
    ]);
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Módulo de Mesas
    Route::resource('mesas', MesaController::class)->names('mesas');

    // Los siguientes módulos los crearemos en la Clase 5 (con sus controladores y modelos)
    // Route::resource('clientes', ClienteController::class)->names('clientes');
    // Route::resource('apuestas', ApuestaController::class)->names('apuestas');
    // Route::resource('transacciones', TransaccionController::class)->names('transacciones');
});

require __DIR__.'/auth.php';