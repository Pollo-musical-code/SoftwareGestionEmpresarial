<?php

namespace Database\Factories;

use App\Models\Juego;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Juego> */
class JuegoFactory extends Factory
{
    protected $model = Juego::class;

    public function definition(): array
    {
        $juegos = ['Ruleta Europea', 'Ruleta Americana', 'Blackjack', 'Póker Texas Hold\'em', 'Baccarat', 'Craps', 'Tragamonedas', 'Video Póker', 'Keno', 'Sic Bo'];

        return [
            'nombre' => fake()->randomElement($juegos).' '.fake()->numberBetween(1, 9999),
            'apuesta_min' => 1000,
            'apuesta_max' => fake()->randomElement([50000, 100000, 500000]),
            'activo' => true,
        ];
    }
}
