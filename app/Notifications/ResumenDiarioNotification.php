<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ResumenDiarioNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public array $datos) {}

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('📊 Resumen diario — Casino Fortuna')
            ->greeting('Hola '.$notifiable->name)
            ->line('Ventas del día: '.$this->datos['ventas'].' por $'.number_format($this->datos['total'], 0, ',', '.'))
            ->line('Compras recibidas hoy: '.$this->datos['compras'])
            ->line('Fichas con stock bajo: '.$this->datos['stock_bajo'])
            ->action('Ir al panel', url('/dashboard'));
    }

    public function toArray(object $notifiable): array
    {
        return [
            'tipo' => 'resumen-diario',
            'titulo' => 'Resumen diario',
            'mensaje' => $this->datos['ventas'].' ventas por $'.number_format($this->datos['total'], 0, ',', '.').'; '.$this->datos['stock_bajo'].' fichas con stock bajo.',
            'url' => url('/dashboard'),
        ];
    }
}
