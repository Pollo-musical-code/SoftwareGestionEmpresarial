<?php

namespace App\Observers;

use App\Events\StockBajoDetectado;
use App\Models\Ficha;

class FichaObserver
{
    /** Dispara el evento solo cuando el stock CRUZA el mínimo (evita alertas repetidas). */
    public function updated(Ficha $ficha): void
    {
        if (! $ficha->wasChanged('stock')) {
            return;
        }

        $minimo = (int) config('erp.stock_minimo');

        if ((int) $ficha->stock <= $minimo && (int) $ficha->getOriginal('stock') > $minimo) {
            StockBajoDetectado::dispatch($ficha);
        }
    }
}
