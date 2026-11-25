<?php

namespace App\Listeners;

use App\Events\StockBajoDetectado;
use App\Notifications\StockBajoNotification;
use App\Support\Destinatarios;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Notification;

class NotificarStockBajo implements ShouldQueue
{
    public function handle(StockBajoDetectado $evento): void
    {
        Notification::send(Destinatarios::administradores(), new StockBajoNotification($evento->ficha));
    }
}
