<?php

namespace App\Support;

use App\Models\User;
use Illuminate\Support\Collection;

class Destinatarios
{
    /** Administradores activos: reciben las alertas del casino. */
    public static function administradores(): Collection
    {
        return User::role(PermissionCatalog::superRole())->where('active', true)->get();
    }
}
