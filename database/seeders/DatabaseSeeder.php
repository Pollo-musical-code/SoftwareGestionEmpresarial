<?php

namespace Database\Seeders;

use App\Console\Commands\InicializarKardex;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Artisan;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            CompanySettingSeeder::class,
            RolePermissionSeeder::class,
            UsuariosPruebaSeeder::class,
            JuegoSeeder::class,
            ClienteSeeder::class,
            ProveedorSeeder::class,
            FichaSeeder::class,
            DatosCasinoSeeder::class,
        ]);

        // Kardex: asiento de apertura con el stock inicial de cada ficha
        Artisan::call((new InicializarKardex)->getName());
    }
}
