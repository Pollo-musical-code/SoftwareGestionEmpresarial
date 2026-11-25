<?php

namespace App\Models;

use App\Models\Concerns\Auditable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Cliente extends Model
{
    use Auditable, SoftDeletes;
    use HasFactory;

    protected string $auditLog = 'clientes';

    protected $fillable = [
        'documento', 'nombre', 'fecha_nacimiento', 'nivel_vip', 'saldo_fichas',
    ];

    protected function casts(): array
    {
        return [
            'fecha_nacimiento' => 'date',
            'saldo_fichas' => 'decimal:2',
        ];
    }

    public function ventas()
    {
        return $this->hasMany(Venta::class);
    }
}
