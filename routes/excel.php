<?php

use App\Http\Controllers\ExcelController;
use Illuminate\Support\Facades\Route;

Route::prefix('excel')->name('excel.')->group(function () {
    Route::get('exportar/clientes', [ExcelController::class, 'exportarClientes'])->name('clientes.exportar');
    Route::get('exportar/fichas', [ExcelController::class, 'exportarFichas'])->name('fichas.exportar');
    Route::get('exportar/ventas', [ExcelController::class, 'exportarVentas'])->name('ventas.exportar');
    Route::get('plantilla/clientes', [ExcelController::class, 'plantillaClientes'])->name('clientes.plantilla');
    Route::get('importar/clientes', [ExcelController::class, 'formImportarClientes'])->name('clientes.importar');
    Route::post('importar/clientes', [ExcelController::class, 'importarClientes'])->name('clientes.importar.guardar');
});
