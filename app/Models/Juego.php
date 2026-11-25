<?php

namespace App\Models;

use App\Models\Concerns\Auditable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Juego extends Model
{
    use Auditable, SoftDeletes;
    use HasFactory;

    protected string $auditLog = 'juegos';

    protected $fillable = [
        'nombre', 'apuesta_min', 'apuesta_max', 'activo',
    ];

    protected function casts(): array
    {
        return [
            'apuesta_min' => 'decimal:2',
            'apuesta_max' => 'decimal:2',
            'activo' => 'boolean',
        ];
    }

    public function mesas()
    {
        return $this->hasMany(Mesa::class);
    }
}
