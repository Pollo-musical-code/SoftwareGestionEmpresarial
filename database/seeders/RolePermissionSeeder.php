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

        $permisos = [];
        foreach (['juegos', 'mesas', 'clientes', 'ventas', 'proveedores', 'compras'] as $modulo) {
            foreach (['ver', 'crear', 'editar', 'eliminar'] as $accion) {
                $permisos[] = "$accion-$modulo";
            }
        }
        $permisos = array_merge($permisos, [
            'ver-fichas', 'recibir-compras', 'ver-reportes',
            'ver-inventario', 'ajustar-inventario', 'ver-auditoria',
            'ver-branding', 'editar-branding',
            // Configuración
            'gestionar-usuarios', 'gestionar-roles',
            // Excel (clase 13)
            'exportar-clientes', 'importar-clientes', 'exportar-fichas', 'exportar-ventas',
        ]);

        foreach ($permisos as $p) {
            Permission::findOrCreate($p);
        }

        // 1) Administrador: todo.
        Role::findOrCreate('admin')->syncPermissions(Permission::all());

        // 2) Cajero: atiende jugadores, vende fichas y recibe compras.
        Role::findOrCreate('cajero')->syncPermissions([
            'ver-clientes', 'crear-clientes', 'editar-clientes',
            'ver-mesas', 'ver-juegos', 'ver-fichas',
            'ver-ventas', 'crear-ventas',
            'ver-proveedores', 'ver-compras', 'recibir-compras',
            'ver-inventario',
        ]);

        // 3) Crupier: opera las mesas; no toca dinero ni clientes.
        Role::findOrCreate('crupier')->syncPermissions([
            'ver-juegos', 'ver-mesas', 'editar-mesas',
        ]);

        // 4) Supervisor de sala: vigila, anula ventas, audita y exporta; no administra usuarios.
        Role::findOrCreate('supervisor')->syncPermissions([
            'ver-juegos', 'ver-mesas', 'editar-mesas',
            'ver-clientes', 'ver-fichas',
            'ver-ventas', 'eliminar-ventas',
            'ver-proveedores', 'ver-compras', 'recibir-compras',
            'ver-reportes', 'ver-inventario', 'ver-auditoria',
            'exportar-clientes', 'exportar-fichas', 'exportar-ventas',
        ]);
    }
}
