<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $permissions = [
            'ver-juegos', 'crear-juegos', 'editar-juegos', 'eliminar-juegos',
            'ver-mesas', 'crear-mesas', 'editar-mesas', 'eliminar-mesas',
            'ver-clientes', 'crear-clientes', 'editar-clientes', 'eliminar-clientes',
            'ver-reportes',
        ];

        foreach ($permissions as $permission) {
            Permission::findOrCreate($permission);
        }

        // Admin: todos los permisos
        Role::findOrCreate('admin')->syncPermissions(Permission::all());

        // Cajero: atiende clientes, no toca juegos ni mesas
        Role::findOrCreate('cajero')->syncPermissions([
            'ver-clientes', 'crear-clientes', 'editar-clientes',
            'ver-mesas', 'ver-juegos',
        ]);

        // Crupier: maneja mesas y juegos, no administra clientes
        Role::findOrCreate('crupier')->syncPermissions([
            'ver-juegos', 'ver-mesas', 'editar-mesas',
        ]);
    }
}