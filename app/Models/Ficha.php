<?php

namespace App\Models;

use App\Models\Concerns\Auditable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Ficha extends Model
{
    use Auditable, SoftDeletes;

    protected string $auditLog = 'fichas';

    // El stock NO se audita aquí: sus cambios quedan en el kardex (movimientos_inventario)
    protected array $auditExcept = ['stock'];

    protected $fillable = [
        'denominacion', 'valor', 'stock', 'activo',
    ];

    protected function casts(): array
    {
        return [
            'valor' => 'decimal:2',
            'stock' => 'integer',
            'activo' => 'boolean',
        ];
    }

    /**
     * Colores de la ficha según su valor, como en un casino real:
     * [color del cuerpo, color del anillo y del centro].
     */
    public static function paletaChip(float $valor): array
    {
        return match (true) {
            $valor >= 500000 => ['#7c3aed', '#ede9fe'], // morado
            $valor >= 100000 => ['#111827', '#e5e7eb'], // negra
            $valor >= 50000 => ['#15803d', '#dcfce7'],  // verde
            $valor >= 10000 => ['#1d4ed8', '#dbeafe'],  // azul
            $valor >= 5000 => ['#b91c1c', '#fee2e2'],   // roja
            default => ['#9ca3af', '#f3f4f6'],          // blanca
        };
    }

    public function detalles()
    {
        return $this->hasMany(VentaDetalle::class);
    }

    public function compraDetalles()
    {
        return $this->hasMany(CompraDetalle::class);
    }

    // Movimientos del kardex
    public function movimientos()
    {
        return $this->hasMany(MovimientoInventario::class);
    }
}
