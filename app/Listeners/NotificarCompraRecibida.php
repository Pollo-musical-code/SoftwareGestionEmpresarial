<?php

namespace App\Listeners;

use App\Events\CompraRecibida;
use App\Notifications\CompraRecibidaNotification;
use App\Support\Destinatarios;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Notification;

class NotificarCompraRecibida implements ShouldQueue
{
    public function handle(CompraRecibida $evento): void
    {
        Notification::send(Destinatarios::administradores(), new CompraRecibidaNotification($evento->compra));
    }
}
