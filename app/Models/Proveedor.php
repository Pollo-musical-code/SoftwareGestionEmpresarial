<?php

namespace App\Models;

use App\Models\Concerns\Auditable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Proveedor extends Model
{
    use Auditable, SoftDeletes;

    protected string $auditLog = 'proveedores';

    // Laravel deduciría "proveedors"; se indica el nombre real de la tabla
    protected $table = 'proveedores';

    protected $fillable = [
        'tipo_documento', 'numero_documento', 'nombre', 'contacto',
        'email', 'telefono', 'direccion', 'activo',
    ];

    protected function casts(): array
    {
        return ['activo' => 'boolean'];
    }

    public function compras()
    {
        return $this->hasMany(Compra::class);
    }
}