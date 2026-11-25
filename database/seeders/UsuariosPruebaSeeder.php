<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\PermissionRegistrar;

/** Un usuario de prueba por rol (contraseña: password). */
class UsuariosPruebaSeeder extends Seeder
{
    public function run(): void
    {
        $usuarios = [
            ['Administrador Casino', 'admin@casino.test', ['admin'], true],
            ['Supervisor de Sala', 'supervisor@casino.test', ['supervisor'], true],
            ['Cajero Principal', 'cajero@casino.test', ['cajero'], true],
            ['Crupier Mesa 1', 'crupier@casino.test', ['crupier'], true],
            ['Cajero y Crupier (varios roles)', 'multi@casino.test', ['cajero', 'crupier'], true],
            ['Crupier Inactivo', 'inactivo@casino.test', ['crupier'], false],
        ];

        foreach ($usuarios as [$nombre, $email, $roles, $activo]) {
            $u = User::firstOrNew(['email' => $email]);
            $u->forceFill([
                'name' => $nombre,
                'password' => Hash::make('password'),
                'active' => $activo,
                'email_verified_at' => now(),
            ])->save();
            $u->syncRoles($roles);
        }

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }
}
