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
            'ver-fichas',
            'ver-ventas', 'crear-ventas', 'editar-ventas', 'eliminar-ventas',
            'ver-reportes',
        ];

        foreach ($permissions as $permission) {
            Permission::findOrCreate($permission);
        }

        Role::findOrCreate('admin')->syncPermissions(Permission::all());

        // Cajero: atiende clientes y vende fichas
        Role::findOrCreate('cajero')->syncPermissions([
            'ver-clientes', 'crear-clientes', 'editar-clientes',
            'ver-mesas', 'ver-juegos',
            'ver-fichas',
            'ver-ventas', 'crear-ventas',
        ]);

        // Crupier: maneja mesas y juegos, no ve clientes ni ventas
        Role::findOrCreate('crupier')->syncPermissions([
            'ver-juegos', 'ver-mesas', 'editar-mesas',
        ]);
    }
}