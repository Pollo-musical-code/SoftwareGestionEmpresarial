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

    public function detalles()
    {
        return $this->hasMany(VentaDetalle::class);
    }

    public function compraDetalles()
    {
        return $this->hasMany(CompraDetalle::class);
    }

    // Movimientos del kardex (Clase 12)
    public function movimientos()
    {
        return $this->hasMany(MovimientoInventario::class);
    }
}