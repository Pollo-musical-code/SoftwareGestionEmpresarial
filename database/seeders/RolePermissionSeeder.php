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
            'ver-proveedores', 'crear-proveedores', 'editar-proveedores', 'eliminar-proveedores',
            'ver-compras', 'crear-compras', 'editar-compras', 'eliminar-compras', 'recibir-compras',
            'ver-reportes',
            // Clase 12: inventario (kardex) y auditoría
            'ver-inventario', 'ajustar-inventario',
            'ver-auditoria',
        ];

        foreach ($permissions as $permission) {
            Permission::findOrCreate($permission);
        }

        Role::findOrCreate('admin')->syncPermissions(Permission::all());

        // Cajero: atiende clientes, vende fichas, recibe fichas y consulta el kardex (no ajusta)
        Role::findOrCreate('cajero')->syncPermissions([
            'ver-clientes', 'crear-clientes', 'editar-clientes',
            'ver-mesas', 'ver-juegos',
            'ver-fichas',
            'ver-ventas', 'crear-ventas',
            'ver-proveedores', 'ver-compras', 'recibir-compras',
            'ver-inventario',
        ]);

        // Crupier: maneja mesas y juegos
        Role::findOrCreate('crupier')->syncPermissions([
            'ver-juegos', 'ver-mesas', 'editar-mesas',
        ]);
    }
}