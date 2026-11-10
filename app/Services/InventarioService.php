<?php

namespace App\Services;

use App\Models\Ficha;
use App\Models\MovimientoInventario;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

/**
 * ÚNICO punto del ERP que modifica el stock de fichas. Cada cambio deja un movimiento
 * en el kardex con el saldo resultante, quién lo hizo y el documento que lo originó.
 */
class InventarioService
{
    // Entrada de fichas (aumenta el stock)
    public function entrada(Ficha $ficha, int $cantidad, string $concepto, ?Model $referencia = null,
        ?float $costoUnitario = null, ?string $notas = null): MovimientoInventario
    {
        return $this->mover($ficha, 'entrada', $cantidad, $concepto, $referencia, $costoUnitario, $notas);
    }

    // Salida de fichas (disminuye el stock). Nunca deja el stock en negativo.
    public function salida(Ficha $ficha, int $cantidad, string $concepto, ?Model $referencia = null,
        ?float $costoUnitario = null, ?string $notas = null): MovimientoInventario
    {
        return $this->mover($ficha, 'salida', $cantidad, $concepto, $referencia, $costoUnitario, $notas);
    }

    // Ajuste por conteo físico: lleva el stock al valor contado y registra solo la diferencia
    public function ajustar(Ficha $ficha, int $stockContado, string $motivo): ?MovimientoInventario
    {
        return DB::transaction(function () use ($ficha, $stockContado, $motivo) {
            $ficha = Ficha::withTrashed()->lockForUpdate()->findOrFail($ficha->id);
            $diferencia = $stockContado - $ficha->stock;

            if ($diferencia === 0) {
                return null; // el conteo coincide: nada que ajustar
            }

            return $diferencia > 0
                ? $this->mover($ficha, 'entrada', $diferencia, 'ajuste', null, null, $motivo)
                : $this->mover($ficha, 'salida', -$diferencia, 'ajuste', null, null, $motivo);
        });
    }

    // Registra el stock que ya existía como «inventario inicial»
    public function apertura(Ficha $ficha): ?MovimientoInventario
    {
        if ($ficha->stock <= 0 || $ficha->movimientos()->exists()) {
            return null;
        }

        return MovimientoInventario::create([
            'ficha_id' => $ficha->id,
            'tipo' => 'entrada',
            'concepto' => 'inventario_inicial',
            'cantidad' => $ficha->stock,
            'saldo' => $ficha->stock,
            'user_id' => auth()->id(),
            'notas' => 'Saldo inicial de la ficha',
        ]);
    }

    private function mover(Ficha $ficha, string $tipo, int $cantidad, string $concepto, ?Model $referencia,
        ?float $costoUnitario, ?string $notas): MovimientoInventario
    {
        if ($cantidad <= 0) {
            throw ValidationException::withMessages([
                'cantidad' => 'La cantidad del movimiento debe ser mayor que cero.',
            ]);
        }

        return DB::transaction(function () use ($ficha, $tipo, $cantidad, $concepto, $referencia, $costoUnitario, $notas) {
            // Se vuelve a leer y bloquear la fila: el saldo siempre parte del valor real en la base de datos
            $ficha = Ficha::withTrashed()->lockForUpdate()->findOrFail($ficha->id);

            $saldo = $tipo === 'entrada' ? $ficha->stock + $cantidad : $ficha->stock - $cantidad;

            if ($saldo < 0) {
                throw ValidationException::withMessages([
                    'items' => "Stock insuficiente para «{$ficha->denominacion}». Disponible: {$ficha->stock}, solicitado: {$cantidad}.",
                ]);
            }

            $ficha->update(['stock' => $saldo]);

            return MovimientoInventario::create([
                'ficha_id' => $ficha->id,
                'tipo' => $tipo,
                'concepto' => $concepto,
                'cantidad' => $cantidad,
                'saldo' => $saldo,
                'costo_unitario' => $costoUnitario,
                'referencia_type' => $referencia?->getMorphClass(),
                'referencia_id' => $referencia?->getKey(),
                'user_id' => auth()->id(),
                'notas' => $notas,
            ]);
        });
    }
}