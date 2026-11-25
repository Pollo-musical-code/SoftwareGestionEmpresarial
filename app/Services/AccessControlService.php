<?php

namespace App\Services;

use App\Models\User;
use App\Support\PermissionCatalog;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

/** Reglas de negocio de Configuración (usuarios, roles, matriz). Todo queda en la bitácora 'seguridad'. */
class AccessControlService
{
    public function guardarUsuario(User $actor, ?User $usuario, array $d): User
    {
        return DB::transaction(function () use ($actor, $usuario, $d) {
            $nuevo = $usuario === null;
            $usuario ??= new User;
            $activo = (bool) ($d['active'] ?? false);
            $roles = $d['roles'] ?? [];
            $admin = PermissionCatalog::superRole();

            if (! $nuevo) {
                if ($usuario->is($actor) && ! $activo) {
                    $this->fallar('No puedes desactivar tu propio usuario.');
                }
                if ($usuario->hasRole($admin) && (! in_array($admin, $roles, true) || ! $activo)
                    && ! $this->hayOtroAdminActivo($usuario)) {
                    $this->fallar('Debe existir al menos un administrador activo en el casino.');
                }
            }

            $usuario->forceFill(['name' => $d['name'], 'email' => $d['email'], 'active' => $activo]);
            if (! empty($d['password'])) {
                $usuario->forceFill(['password' => Hash::make($d['password'])]);
            }
            $usuario->save();

            $usuario->syncRoles($roles);
            $usuario->syncPermissions($d['permisos'] ?? []);
            $this->limpiarCache();

            $this->bitacora($actor, $usuario, $nuevo ? 'Creó usuario' : 'Actualizó usuario', [
                'roles' => $roles, 'permisos_directos' => $d['permisos'] ?? [], 'activo' => $activo,
            ]);

            return $usuario;
        });
    }

    public function alternarActivo(User $actor, User $usuario): User
    {
        if ((bool) $usuario->active) {
            if ($usuario->is($actor)) {
                $this->fallar('No puedes desactivar tu propio usuario.');
            }
            if ($usuario->hasRole(PermissionCatalog::superRole()) && ! $this->hayOtroAdminActivo($usuario)) {
                $this->fallar('Debe existir al menos un administrador activo en el casino.');
            }
        }

        $usuario->forceFill(['active' => ! (bool) $usuario->active])->save();
        $this->bitacora($actor, $usuario, $usuario->active ? 'Activó usuario' : 'Desactivó usuario');

        return $usuario;
    }

    public function guardarRol(User $actor, ?Role $rol, string $nombre, array $permisos): Role
    {
        return DB::transaction(function () use ($actor, $rol, $nombre, $permisos) {
            $nuevo = $rol === null;

            if (! $nuevo && PermissionCatalog::isSuperRole($rol->name) && $nombre !== $rol->name) {
                $this->fallar('El rol administrador no se puede renombrar.');
            }

            $rol ??= Role::create(['name' => $nombre, 'guard_name' => 'web']);
            if (! $nuevo) {
                $rol->update(['name' => $nombre]);
            }

            // El administrador siempre conserva TODOS los permisos.
            $rol->syncPermissions(PermissionCatalog::isSuperRole($rol->name) ? Permission::all() : $permisos);
            $this->limpiarCache();
            $this->bitacora($actor, $rol, $nuevo ? 'Creó rol' : 'Actualizó rol', ['permisos' => $permisos]);

            return $rol;
        });
    }

    public function eliminarRol(User $actor, Role $rol): void
    {
        if (PermissionCatalog::isSuperRole($rol->name) || in_array($rol->name, config('permisos.roles_base', []), true)) {
            $this->fallar('Los roles base del casino no se pueden eliminar.');
        }
        if ($rol->users()->exists()) {
            $this->fallar('No se puede eliminar un rol que tiene usuarios asignados.');
        }

        $this->bitacora($actor, $rol, 'Eliminó rol');
        $rol->delete();
        $this->limpiarCache();
    }

    /** $matriz = [role_id => [nombres de permisos]] */
    public function sincronizarMatriz(User $actor, array $matriz): void
    {
        DB::transaction(function () use ($actor, $matriz) {
            foreach (Role::all() as $rol) {
                if (PermissionCatalog::isSuperRole($rol->name)) {
                    continue; // admin: siempre todos
                }
                $rol->syncPermissions($matriz[$rol->id] ?? []);
            }
            $this->limpiarCache();
            $this->bitacora($actor, null, 'Actualizó la matriz de permisos');
        });
    }

    private function hayOtroAdminActivo(User $excepto): bool
    {
        return User::role(PermissionCatalog::superRole())
            ->where('active', true)->where('id', '!=', $excepto->id)->exists();
    }

    private function fallar(string $mensaje): never
    {
        throw ValidationException::withMessages(['regla' => $mensaje]);
    }

    private function limpiarCache(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }

    private function bitacora(User $actor, $objetivo, string $accion, array $props = []): void
    {
        $log = activity('seguridad')->causedBy($actor)->withProperties($props);
        if ($objetivo) {
            $log->performedOn($objetivo);
        }
        $log->log($accion);
    }
}
