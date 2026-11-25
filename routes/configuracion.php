<?php

use App\Http\Controllers\Settings\ActiveRoleController;
use App\Http\Controllers\Settings\PermissionController;
use App\Http\Controllers\Settings\RoleController;
use App\Http\Controllers\Settings\UserController;
use Illuminate\Support\Facades\Route;

// Rol activo (usuarios con varios roles)
Route::get('rol-activo', [ActiveRoleController::class, 'elegir'])->name('rol-activo.elegir');
Route::post('rol-activo', [ActiveRoleController::class, 'guardar'])->name('rol-activo.guardar');

// Configuración: usuarios, roles y matriz de permisos
Route::prefix('configuracion')->name('settings.')->group(function () {
    Route::get('usuarios', [UserController::class, 'index'])->name('users.index');
    Route::get('usuarios/crear', [UserController::class, 'create'])->name('users.create');
    Route::post('usuarios', [UserController::class, 'store'])->name('users.store');
    Route::get('usuarios/{user}/editar', [UserController::class, 'edit'])->name('users.edit');
    Route::put('usuarios/{user}', [UserController::class, 'update'])->name('users.update');
    Route::patch('usuarios/{user}/activo', [UserController::class, 'toggle'])->name('users.toggle');

    Route::get('roles', [RoleController::class, 'index'])->name('roles.index');
    Route::get('roles/crear', [RoleController::class, 'create'])->name('roles.create');
    Route::post('roles', [RoleController::class, 'store'])->name('roles.store');
    Route::get('roles/{role}/editar', [RoleController::class, 'edit'])->name('roles.edit');
    Route::put('roles/{role}', [RoleController::class, 'update'])->name('roles.update');
    Route::delete('roles/{role}', [RoleController::class, 'destroy'])->name('roles.destroy');

    Route::get('permisos', [PermissionController::class, 'index'])->name('permissions.index');
    Route::put('permisos', [PermissionController::class, 'update'])->name('permissions.update');
});
