<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Mesa extends Model
{
    protected $fillable = [
        'juego_id', 'estado'
    ];

    // Relación: una Mesa pertenece a un Juego
    public function juego()
    {
        return $this->belongsTo(Juego::class);
    }
}