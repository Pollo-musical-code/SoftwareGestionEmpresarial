<?php

namespace App\Models;

use App\Models\Concerns\Auditable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Mesa extends Model
{
    use Auditable, SoftDeletes;
    use HasFactory;

    protected string $auditLog = 'mesas';

    protected $fillable = [
        'juego_id', 'estado',
    ];

    public function juego()
    {
        return $this->belongsTo(Juego::class)->withTrashed();
    }
}
