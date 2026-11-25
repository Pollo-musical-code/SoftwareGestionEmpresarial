<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MovimientoInventario extends Model
{
    // Laravel deduciría «movimiento_inventarios»; se indica el nombre real
    protected $table = 'movimientos_inventario';

    public const CONCEPTOS = [
        'inventario_inicial' => 'Inventario inicial',
        'compra' => 'Compra (recepción)',
        'venta' => 'Venta',
        'anulacion_venta' => 'Anulación de venta',
        'ajuste' => 'Ajuste de inventario',
    ];

    protected $fillable = [
        'ficha_id', 'tipo', 'concepto', 'cantidad', 'saldo',
        'costo_unitario', 'referencia_type', 'referencia_id', 'user_id', 'notas',
    ];

    protected function casts(): array
    {
        return [
            'cantidad' => 'integer',
            'saldo' => 'integer',
            'costo_unitario' => 'decimal:2',
        ];
    }

    public function ficha()
    {
        return $this->belongsTo(Ficha::class)->withTrashed();
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Una venta anulada queda en la papelera, pero su número debe seguir en el kardex
    public function referencia()
    {
        return $this->morphTo()->withTrashed();
    }

    public function etiquetaConcepto(): string
    {
        return self::CONCEPTOS[$this->concepto] ?? $this->concepto;
    }

    // Texto corto del documento: FV-00001, OC-00001...
    public function etiquetaReferencia(): ?string
    {
        return $this->referencia?->numero_factura ?? $this->referencia?->numero_compra;
    }
}
