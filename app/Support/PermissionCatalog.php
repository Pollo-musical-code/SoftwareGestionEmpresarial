<?php

namespace App\Support;

use Illuminate\Support\Collection;

class PermissionCatalog
{
    public const SUPER_ROLE = 'admin';

    public static function superRole(): string
    {
        return self::SUPER_ROLE;
    }

    public static function isSuperRole(string $role): bool
    {
        return $role === self::SUPER_ROLE;
    }

    /** "ver-clientes" => ['ver', 'clientes'] */
    public static function split(string $permission): array
    {
        $parts = explode('-', $permission, 2);

        return count($parts) === 2 ? [$parts[0], $parts[1]] : [$permission, 'general'];
    }

    public static function moduleLabel(string $module): string
    {
        return config("permisos.modulos.$module", ucfirst($module));
    }

    public static function actionLabel(string $action): string
    {
        return config("permisos.acciones.$action", ucfirst($action));
    }

    /** Agrupa permisos (modelos) por módulo, ordenados. */
    public static function grouped(Collection $permissions): Collection
    {
        return $permissions
            ->groupBy(fn ($p) => self::split($p->name)[1])
            ->sortKeys();
    }
}
