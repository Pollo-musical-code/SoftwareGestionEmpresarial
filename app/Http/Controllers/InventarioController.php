<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreAjusteRequest;
use App\Models\Ficha;
use App\Models\MovimientoInventario;
use App\Services\InventarioService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Validation\ValidationException;

class InventarioController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('permission:ver-inventario', only: ['kardex', 'pdf']),
            new Middleware('permission:ajustar-inventario', only: ['ajustar', 'guardarAjuste']),
        ];
    }

    // Kardex: movimientos de una ficha con entradas, salidas y saldo
    public function kardex(Request $request)
    {
        $fichas = Ficha::withTrashed()->orderBy('valor')->get(['id', 'denominacion', 'valor', 'stock', 'deleted_at']);
        $ficha = $request->filled('ficha') ? $fichas->firstWhere('id', (int) $request->input('ficha')) : null;

        $movimientos = $ficha
            ? $this->consulta($request, $ficha)->paginate(20)->withQueryString()
            : null;

        return view('inventario.kardex', [
            'fichas' => $fichas,
            'ficha' => $ficha,
            'movimientos' => $movimientos,
            'totales' => $ficha ? $this->totales($request, $ficha) : null,
            'conceptos' => MovimientoInventario::CONCEPTOS,
        ]);
    }

    public function pdf(Request $request, Ficha $ficha)
    {
        return Pdf::loadView('pdf.kardex', [
            'ficha' => $ficha,
            'movimientos' => $this->consulta($request, $ficha)->get(),
            'totales' => $this->totales($request, $ficha),
            'from' => $request->input('from'),
            'to' => $request->input('to'),
        ])->setPaper('letter', 'landscape')->stream("kardex-ficha-{$ficha->id}.pdf");
    }

    public function ajustar(Ficha $ficha)
    {
        return view('inventario.ajustar', [
            'ficha' => $ficha,
            'ultimos' => $ficha->movimientos()->latest('id')->limit(5)->get(),
        ]);
    }

    public function guardarAjuste(StoreAjusteRequest $request, Ficha $ficha, InventarioService $inventario)
    {
        try {
            $movimiento = $inventario->ajustar($ficha, $request->integer('stock_contado'), $request->validated('motivo'));
        } catch (ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();
        }

        if (! $movimiento) {
            return back()->with('error', 'El conteo coincide con el stock del sistema: no se registró ningún ajuste.');
        }

        return redirect()->route('inventario.kardex', ['ficha' => $ficha->id])
            ->with('success', "Ajuste registrado: {$movimiento->tipo} de {$movimiento->cantidad} unidades. Nuevo stock: {$movimiento->saldo}.");
    }

    private function consulta(Request $request, Ficha $ficha)
    {
        return $ficha->movimientos()
            ->with(['user:id,name', 'referencia'])
            ->when($request->filled('from'), fn ($q) => $q->whereDate('created_at', '>=', $request->input('from')))
            ->when($request->filled('to'), fn ($q) => $q->whereDate('created_at', '<=', $request->input('to')))
            ->when($request->filled('concepto'), fn ($q) => $q->where('concepto', $request->input('concepto')))
            ->orderBy('id');
    }

    private function totales(Request $request, Ficha $ficha): array
    {
        $query = $this->consulta($request, $ficha)->reorder();

        return [
            'entradas' => (int) (clone $query)->where('tipo', 'entrada')->sum('cantidad'),
            'salidas' => (int) (clone $query)->where('tipo', 'salida')->sum('cantidad'),
            // Control: el último saldo del kardex debe coincidir con el stock de la ficha
            'cuadra' => (int) ($ficha->movimientos()->latest('id')->value('saldo') ?? 0) === (int) $ficha->stock,
        ];
    }
}
