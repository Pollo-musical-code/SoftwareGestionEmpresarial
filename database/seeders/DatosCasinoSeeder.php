<?php

namespace Database\Seeders;

use App\Models\Cliente;
use App\Models\Juego;
use App\Models\Mesa;
use Illuminate\Database\Seeder;

/** Completa las tablas principales hasta tener al menos 10 registros (datos de prueba). */
class DatosCasinoSeeder extends Seeder
{
    public function run(): void
    {
        Juego::factory()->count(max(0, 10 - Juego::count()))->create();
        Cliente::factory()->count(max(0, 12 - Cliente::count()))->create();

        $faltan = max(0, 10 - Mesa::count());
        for ($i = 0; $i < $faltan; $i++) {
            Mesa::factory()->create(['juego_id' => Juego::inRandomOrder()->value('id')]);
        }
    }
}
