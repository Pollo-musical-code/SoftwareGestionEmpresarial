<?php

use App\Http\Controllers\NotificationController;
use Illuminate\Support\Facades\Route;

Route::get('notificaciones', [NotificationController::class, 'index'])->name('notificaciones.index');
Route::post('notificaciones/leer-todas', [NotificationController::class, 'leerTodas'])->name('notificaciones.leer-todas');
Route::post('notificaciones/{id}/leer', [NotificationController::class, 'leer'])->name('notificaciones.leer');
