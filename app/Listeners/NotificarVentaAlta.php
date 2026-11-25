<?php

namespace App\Listeners;

use App\Events\VentaRegistrada;
use App\Notifications\VentaAltaNotification;
use App\Support\Destinatarios;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Notification;

class NotificarVentaAlta implements ShouldQueue
{
    public function handle(VentaRegistrada $evento): void
    {
        $total = (float) ($evento->venta->total ?? 0);

        if ($total >= (float) config('erp.venta_alta')) {
            Notification::send(Destinatarios::administradores(), new VentaAltaNotification($evento->venta));
        }
    }
}
