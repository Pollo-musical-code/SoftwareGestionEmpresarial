<?php

namespace App\Services;

use App\Models\Ficha;
use App\Models\Venta;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class VentaService
{
    public const TASA_IMPUESTO = 0.19;

    // El stock lo mueve el InventarioService (kardex)
    public function __construct(private InventarioService $inventario) {}

    /**
     * Registra una venta completa dentro de una transacción.
     *
     * @param  array  $data  ['cliente_id' => int, 'fecha_venta' => 'Y-m-d', 'notas' => string,
     *                       'items' => [['ficha_id' => int, 'cantidad' => int], ...]]
     */
    public function registrar(array $data, int $userId): Venta
    {
        return DB::transaction(function () use ($data, $userId) {
            // 0. Agrupar cantidades por ficha (si se agregó en dos filas)
            $cantidades = [];
            foreach ($data['items'] as $item) {
                $cantidades[$item['ficha_id']] = ($cantidades[$item['ficha_id']] ?? 0) + (int) $item['cantidad'];
            }

            // 1. Validar stock y calcular subtotales
            $lineas = [];
            $subtotal = 0;

            foreach ($cantidades as $fichaId => $cantidad) {
                $ficha = Ficha::lockForUpdate()->findOrFail($fichaId);

                if (! $ficha->activo) {
                    throw ValidationException::withMessages([
                        'items' => "La ficha «{$ficha->denominacion}» está inactiva y no se puede vender.",
                    ]);
                }

                if ($ficha->stock < $cantidad) {
                    throw ValidationException::withMessages([
                        'items' => "Stock insuficiente para «{$ficha->denominacion}». Disponible: {$ficha->stock}, solicitado: {$cantidad}.",
                    ]);
                }

                $subtotalLinea = $ficha->valor * $cantidad;
                $subtotal += $subtotalLinea;

                $lineas[] = [
                    'ficha' => $ficha,
                    'cantidad' => $cantidad,
                    'valor_unitario' => $ficha->valor,
                    'subtotal' => $subtotalLinea,
                ];
            }

            $impuesto = round($subtotal * self::TASA_IMPUESTO, 2);
            $total = round($subtotal + $impuesto, 2);

            // 2. Cabecera de la venta
            $venta = Venta::create([
                'numero_factura' => $this->siguienteNumeroFactura(),
                'cliente_id' => $data['cliente_id'],
                'user_id' => $userId,
                'fecha_venta' => $data['fecha_venta'],
                'subtotal' => $subtotal,
                'impuesto' => $impuesto,
                'total' => $total,
                'estado' => 'pagada',
                'notas' => $data['notas'] ?? null,
            ]);

            // 3. Detalles y salida de inventario por el kardex
            foreach ($lineas as $linea) {
                $venta->detalles()->create([
                    'ficha_id' => $linea['ficha']->id,
                    'cantidad' => $linea['cantidad'],
                    'valor_unitario' => $linea['valor_unitario'],
                    'subtotal' => $linea['subtotal'],
                ]);

                $this->inventario->salida(
                    $linea['ficha'], $linea['cantidad'], 'venta', $venta, (float) $linea['valor_unitario']
                );
            }

            return $venta;
        });
    }

    /**
     * Anula una venta: las fichas regresan al inventario (entrada «anulacion_venta» en el kardex).
     */
    public function anular(Venta $venta): void
    {
        if ($venta->estado === 'anulada') {
            throw ValidationException::withMessages([
                'venta' => "La venta {$venta->numero_factura} ya está anulada.",
            ]);
        }

        DB::transaction(function () use ($venta) {
            foreach ($venta->detalles as $detalle) {
                $this->inventario->entrada(
                    $detalle->ficha,
                    $detalle->cantidad,
                    'anulacion_venta',
                    $venta,
                    (float) $detalle->valor_unitario,
                    "Anulación de la venta {$venta->numero_factura}"
                );
            }

            $venta->update(['estado' => 'anulada']);
            $venta->delete();
        });
    }

    // FV-00001, FV-00002, ...
    private function siguienteNumeroFactura(): string
    {
        $ultima = Venta::withTrashed()->orderByDesc('id')->first();
        $siguiente = $ultima ? $ultima->id + 1 : 1;

        return 'FV-'.str_pad($siguiente, 5, '0', STR_PAD_LEFT);
    }
}
