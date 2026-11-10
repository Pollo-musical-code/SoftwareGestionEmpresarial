<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;

/**
 * Cambios de seguridad (roles y permisos) con registro en la bitácora «seguridad».
 */
class SeguridadService
{
    /**
     * Asigna roles y permisos DIRECTOS a un usuario y deja registro de lo que cambió.
     */
    public function asignarRolesYPermisos(User $user, array $roles, array $permisosDirectos = []): User
    {
        return DB::transaction(function () use ($user, $roles, $permisosDirectos) {
            $antes = $this->estadoDe($user);

            $user->syncRoles($roles);
            $user->syncPermissions($permisosDirectos);

            $despues = $this->estadoDe($user);

            if ($antes !== $despues) {
                $this->registrar($user, 'roles y permisos del usuario modificados', $antes, $despues);
            }

            return $user;
        });
    }

    /**
     * Cambia los permisos de un rol y deja registro de lo que cambió.
     */
    public function sincronizarPermisosDeRol(Role $rol, array $permisos): void
    {
        $antes = ['permisos' => $rol->permissions()->pluck('name')->sort()->values()->all()];

        $rol->syncPermissions($permisos);

        $despues = ['permisos' => $rol->permissions()->pluck('name')->sort()->values()->all()];

        if ($antes !== $despues) {
            $this->registrar($rol, "permisos del rol «{$rol->name}» modificados", $antes, $despues);
        }
    }

    private function estadoDe(User $user): array
    {
        return [
            'roles' => $user->roles()->pluck('name')->sort()->values()->all(),
            'permisos_directos' => $user->permissions()->pluck('name')->sort()->values()->all(),
        ];
    }

    // Mismo formato (old / attributes) que usan los modelos auditados
    private function registrar($sujeto, string $descripcion, array $antes, array $despues): void
    {
        activity('seguridad')
            ->performedOn($sujeto)
            ->causedBy(auth()->user())
            ->event('updated')
            ->withChanges(['old' => $antes, 'attributes' => $despues])
            ->log($descripcion);
    }
}