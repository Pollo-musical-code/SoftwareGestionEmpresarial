<?php

namespace Database\Factories;

use App\Models\Juego;
use App\Models\Mesa;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Mesa> */
class MesaFactory extends Factory
{
    protected $model = Mesa::class;

    public function definition(): array
    {
        return [
            'juego_id' => Juego::factory(),
            'estado' => fake()->randomElement(['abierta', 'cerrada']),
        ];
    }
}
