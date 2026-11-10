<?php

namespace App\Console\Commands;

use App\Models\Ficha;
use App\Services\InventarioService;
use Illuminate\Console\Command;

class InicializarKardex extends Command
{
    protected $signature = 'kardex:inicializar';

    protected $description = 'Registra el stock actual como «inventario inicial» para las fichas que aún no tienen movimientos';

    public function handle(InventarioService $inventario): int
    {
        $contador = 0;

        Ficha::withTrashed()->whereDoesntHave('movimientos')->where('stock', '>', 0)
            ->each(function (Ficha $ficha) use ($inventario, &$contador) {
                if ($inventario->apertura($ficha)) {
                    $contador++;
                }
            });

        $this->info("Fichas inicializadas en el kardex: {$contador}");

        return self::SUCCESS;
    }
}