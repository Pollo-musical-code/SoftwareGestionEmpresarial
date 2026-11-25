<?php

namespace App\Observers;

use App\Events\VentaRegistrada;
use App\Models\Venta;

class VentaObserver
{
    public function created(Venta $venta): void
    {
        VentaRegistrada::dispatch($venta);
    }
}
