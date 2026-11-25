<?php

namespace Database\Factories;

use App\Models\Cliente;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\DB;

/** @extends Factory<Cliente> */
class ClienteFactory extends Factory
{
    protected $model = Cliente::class;

    public function definition(): array
    {
        return [
            'documento' => fake()->unique()->numerify('##########'),
            'nombre' => fake()->name(),
            'fecha_nacimiento' => fake()->dateTimeBetween('-70 years', '-19 years')->format('Y-m-d'),
            'nivel_vip' => self::nivelVip(),
            'saldo_fichas' => 0,
        ];
    }

    /** Si la columna es ENUM en MySQL se usa un valor permitido; si no, 'Regular'. */
    public static function nivelVip(): string
    {
        try {
            $col = DB::selectOne("SHOW COLUMNS FROM clientes LIKE 'nivel_vip'");
            if ($col && preg_match_all("/'([^']+)'/", $col->Type, $m) && str_starts_with($col->Type, 'enum')) {
                return fake()->randomElement($m[1]);
            }
        } catch (\Throwable) {
            // SQLite u otro motor: sin restricción.
        }

        return 'Regular';
    }
}
