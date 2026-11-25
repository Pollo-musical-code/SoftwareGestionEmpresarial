<?php

namespace App\Notifications;

use App\Models\Compra;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class CompraRecibidaNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public Compra $compra) {}

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('📦 Compra #'.$this->compra->id.' recibida')
            ->greeting('Hola '.$notifiable->name)
            ->line('Se recibió la compra #'.$this->compra->id.($this->compra->proveedor?->nombre ? ' del proveedor '.$this->compra->proveedor->nombre : '').'. El inventario de fichas ya fue actualizado.')
            ->action('Ver compra', url('/compras/'.$this->compra->id));
    }

    public function toArray(object $notifiable): array
    {
        return [
            'tipo' => 'compra-recibida',
            'titulo' => 'Compra recibida',
            'mensaje' => 'La compra #'.$this->compra->id.' fue recibida y el inventario se actualizó.',
            'url' => url('/compras/'.$this->compra->id),
        ];
    }
}
