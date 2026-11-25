<?php

use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\ClienteController;
use App\Http\Controllers\Api\V1\FichaController;
use App\Http\Controllers\Api\V1\MesaController;
use App\Http\Controllers\Api\V1\VentaController;
use Illuminate\Support\Facades\Route;

// API REST v1 del casino — autenticación con Sanctum (token con habilidad "rol:<rol activo>")
Route::prefix('v1')->name('api.v1.')->group(function () {
    Route::post('login', [AuthController::class, 'login'])->middleware('throttle:5,1')->name('login');

    Route::middleware(['auth:sanctum', 'throttle:60,1'])->group(function () {
        Route::get('me', [AuthController::class, 'me'])->name('me');
        Route::post('logout', [AuthController::class, 'logout'])->name('logout');

        Route::get('clientes', [ClienteController::class, 'index'])->name('clientes.index');
        Route::post('clientes', [ClienteController::class, 'store'])->name('clientes.store');
        Route::get('clientes/{cliente}', [ClienteController::class, 'show'])->name('clientes.show');

        Route::get('fichas', [FichaController::class, 'index'])->name('fichas.index');
        Route::get('fichas/{ficha}', [FichaController::class, 'show'])->name('fichas.show');

        Route::get('ventas', [VentaController::class, 'index'])->name('ventas.index');
        Route::get('ventas/{venta}', [VentaController::class, 'show'])->name('ventas.show');

        Route::get('mesas', [MesaController::class, 'index'])->name('mesas.index');
    });
});
