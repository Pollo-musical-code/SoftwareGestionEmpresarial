<?php

namespace App\Models\Concerns;

use Laravel\Sanctum\PersonalAccessToken;

/**
 * Rol activo: un usuario puede tener varios roles, pero trabaja con UNO a la vez.
 * - Web: se guarda en sesión ('active_role').
 * - API: viaja en la habilidad del token ("rol:cajero").
 * Sus permisos efectivos = permisos del rol activo + permisos directos.
 */
trait HasActiveRole
{
    public function hasMultipleRoles(): bool
    {
        return $this->roles->count() > 1;
    }

    public function activeRole(): ?string
    {
        $nombres = $this->roles->pluck('name');

        if ($nombres->isEmpty()) {
            return null;
        }

        $token = method_exists($this, 'currentAccessToken') ? $this->currentAccessToken() : null;

        if ($token instanceof PersonalAccessToken) {
            foreach (($token->abilities ?? []) as $habilidad) {
                if (is_string($habilidad) && str_starts_with($habilidad, 'rol:')) {
                    $rol = substr($habilidad, 4);

                    return $nombres->contains($rol) ? $rol : null;
                }
            }

            foreach ($nombres as $nombre) {
                try {
                    if ($token->can("rol:$nombre")) {
                        return $nombre;
                    }
                } catch (\Throwable) {
                    // token de prueba sin esa habilidad
                }
            }
        }

        if ($nombres->count() === 1) {
            return $nombres->first();
        }

        $enSesion = session('active_role');

        return $nombres->contains($enSesion) ? $enSesion : null;
    }

    public function hasPermissionInActiveRole(string $permiso): bool
    {
        if ($this->permissions->contains('name', $permiso)) {
            return true; // permiso directo
        }

        $activo = $this->activeRole();
        if ($activo === null) {
            return false;
        }

        $rol = $this->roles->firstWhere('name', $activo);

        return $rol !== null && $rol->permissions->contains('name', $permiso);
    }
}
