<?php

namespace App\Notifications;

use App\Models\Venta;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

/** Alerta de control: venta de fichas de monto alto (posible riesgo de lavado de activos). */
class VentaAltaNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public Venta $venta) {}

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('🚨 Venta de fichas de monto alto #'.$this->venta->id)
            ->greeting('Hola '.$notifiable->name)
            ->line('Se registró una venta por $'.number_format((float) $this->venta->total, 0, ',', '.').', superior al umbral de control.')
            ->action('Revisar venta', url('/ventas/'.$this->venta->id))
            ->line('Verifica la identidad del cliente según el protocolo de prevención de lavado de activos.');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'tipo' => 'venta-alta',
            'titulo' => 'Venta de monto alto',
            'mensaje' => 'Venta #'.$this->venta->id.' por $'.number_format((float) $this->venta->total, 0, ',', '.').'.',
            'url' => url('/ventas/'.$this->venta->id),
        ];
    }
}
