<?php

namespace Tests\Concerns;

use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Spatie\Permission\PermissionRegistrar;

trait ConRoles
{
    protected function prepararRoles(): void
    {
        $this->withoutVite();
        $this->seed(RolePermissionSeeder::class);
        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }

    protected function usuarioCon(string|array $roles, array $atributos = []): User
    {
        $usuario = User::factory()->create(array_merge(['active' => true], $atributos));
        $usuario->assignRole($roles);

        return $usuario;
    }
}
