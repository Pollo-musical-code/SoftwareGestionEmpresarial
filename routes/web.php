<?php

use App\Http\Controllers\JuegoController;
use App\Http\Controllers\MesaController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    $user = auth()->user();

    $modules = collect([
        ['title' => 'Mesas', 'route' => 'mesas.index', 'permission' => 'ver-mesas'],
        ['title' => 'Juegos', 'route' => 'juegos.index', 'permission' => 'ver-juegos'],
    ])->filter(fn ($m) => $user->can($m['permission']))->values();

    return view('dashboard', [
        'roles' => $user->getRoleNames(),
        'modules' => $modules,
        'mesasActivas' => $user->can('ver-mesas') ? \App\Models\Mesa::where('estado', 'abierta')->count() : null,
        'totalClientes' => $user->can('ver-clientes') ? \App\Models\Cliente::count() : null,
        'fichasCirculacion' => $user->can('ver-clientes') ? \App\Models\Cliente::sum('saldo_fichas') : null,
    ]);
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Mesas (los permisos se validan dentro del controlador)
    Route::patch('mesas/{mesa}/restore', [MesaController::class, 'restore'])
        ->withTrashed()
        ->name('mesas.restore');
    Route::resource('mesas', MesaController::class)->except('show');

    // Juegos
    Route::patch('juegos/{juego}/restore', [JuegoController::class, 'restore'])
        ->withTrashed()
        ->name('juegos.restore');
    Route::resource('juegos', JuegoController::class)->except('show');
});

require __DIR__.'/auth.php';