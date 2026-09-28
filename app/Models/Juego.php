<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Juego extends Model
{
    protected $fillable = [
        'nombre', 'apuesta_min', 'apuesta_max', 'activo'
    ];
        // Relación: un Juego tiene muchas Mesas
    public function mesas()
    {
        return $this->hasMany(Mesa::class);
    }
}