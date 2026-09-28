<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cliente extends Model
{
    protected $fillable = [
        'documento', 'nombre', 'fecha_nacimiento', 'nivel_vip', 'saldo_fichas'
    ];
}