<?php

namespace App\Console\Commands;

use App\Models\Compra;
use App\Models\Ficha;
use App\Models\Venta;
use App\Notifications\ResumenDiarioNotification;
use App\Support\Destinatarios;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Notification;

class EnviarResumenDiario extends Command
{
    protected $signature = 'erp:resumen-diario';

    protected $description = 'Envía a los administradores el resumen del día del casino';

    public function handle(): int
    {
        $datos = [
            'ventas' => Venta::whereDate('created_at', today())->count(),
            'total' => (float) Venta::whereDate('created_at', today())->sum('total'),
            'compras' => Compra::whereDate('updated_at', today())->count(),
            'stock_bajo' => Ficha::where('stock', '<=', config('erp.stock_minimo'))->count(),
        ];

        $admins = Destinatarios::administradores();
        Notification::send($admins, new ResumenDiarioNotification($datos));

        $this->info("Resumen enviado a {$admins->count()} administrador(es).");

        return self::SUCCESS;
    }
}
