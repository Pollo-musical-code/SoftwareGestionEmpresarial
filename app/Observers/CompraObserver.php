<?php

namespace App\Observers;

use App\Events\CompraRecibida;
use App\Models\Compra;

class CompraObserver
{
    /** Cuando algún campo cambia a un valor "recibida/recibido" la compra fue recibida. */
    public function updated(Compra $compra): void
    {
        $recibida = collect($compra->getChanges())
            ->contains(fn ($v) => is_string($v) && stripos($v, 'recib') !== false);

        if ($recibida) {
            CompraRecibida::dispatch($compra);
        }
    }
}
