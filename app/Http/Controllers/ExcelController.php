<?php

namespace App\Http\Controllers;

use App\Exports\ModeloExport;
use App\Exports\PlantillaClientesExport;
use App\Imports\ClientesImport;
use App\Models\Cliente;
use App\Models\Ficha;
use App\Models\Venta;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Maatwebsite\Excel\Facades\Excel;

class ExcelController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('permission:exportar-clientes', only: ['exportarClientes']),
            new Middleware('permission:exportar-fichas', only: ['exportarFichas']),
            new Middleware('permission:exportar-ventas', only: ['exportarVentas']),
            new Middleware('permission:importar-clientes', only: ['formImportarClientes', 'importarClientes', 'plantillaClientes']),
        ];
    }

    public function exportarClientes()
    {
        return Excel::download(new ModeloExport(Cliente::class), 'clientes.xlsx');
    }

    public function exportarFichas()
    {
        return Excel::download(new ModeloExport(Ficha::class), 'fichas.xlsx');
    }

    public function exportarVentas()
    {
        return Excel::download(new ModeloExport(Venta::class), 'ventas.xlsx');
    }

    public function plantillaClientes()
    {
        return Excel::download(new PlantillaClientesExport, 'plantilla_clientes.xlsx');
    }

    public function formImportarClientes()
    {
        return view('excel.importar-clientes');
    }

    public function importarClientes(Request $request)
    {
        $request->validate(['archivo' => ['required', 'file', 'mimes:xlsx,xls,csv', 'max:2048']]);

        $import = new ClientesImport;
        $import->import($request->file('archivo'));

        activity('importaciones')->causedBy($request->user())
            ->withProperties(['creados' => $import->creados, 'rechazados' => $import->failures()->count()])
            ->log('Importó clientes desde Excel');

        return view('excel.importar-clientes', [
            'creados' => $import->creados,
            'fallos' => $import->failures(),
        ]);
    }
}
