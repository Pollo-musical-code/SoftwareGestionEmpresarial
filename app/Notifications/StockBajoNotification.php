<?php

namespace App\Notifications;

use App\Models\Ficha;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class StockBajoNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public Ficha $ficha) {}

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    private function nombre(): string
    {
        return $this->ficha->nombre ?? 'Ficha #'.$this->ficha->id;
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('⚠️ Stock bajo de fichas: '.$this->nombre())
            ->greeting('Hola '.$notifiable->name)
            ->line('La ficha "'.$this->nombre().'" quedó con '.$this->ficha->stock.' unidades (mínimo '.config('erp.stock_minimo').').')
            ->action('Ver inventario', url('/inventario/kardex'))
            ->line('Programa una compra a proveedores para reponer.');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'tipo' => 'stock-bajo',
            'titulo' => 'Stock bajo de fichas',
            'mensaje' => $this->nombre().' quedó con '.$this->ficha->stock.' unidades.',
            'url' => url('/inventario/kardex'),
        ];
    }
}
