<?php

namespace Tests\Feature;

use App\Events\VentaRegistrada;
use App\Listeners\NotificarVentaAlta;
use App\Models\Venta;
use App\Notifications\ResumenDiarioNotification;
use App\Notifications\VentaAltaNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Notification;
use Tests\Concerns\ConRoles;
use Tests\TestCase;

class NotificacionesTest extends TestCase
{
    use ConRoles, RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->prepararRoles();
    }

    private function ventaConTotal(float $total): Venta
    {
        $venta = new Venta;
        $venta->forceFill(['id' => 1, 'total' => $total]);

        return $venta;
    }

    public function test_venta_alta_notifica_a_los_administradores(): void
    {
        Notification::fake();
        $admin = $this->usuarioCon('admin');
        $cajero = $this->usuarioCon('cajero');

        (new NotificarVentaAlta)->handle(new VentaRegistrada($this->ventaConTotal(9_000_000)));

        Notification::assertSentTo($admin, VentaAltaNotification::class);
        Notification::assertNotSentTo($cajero, VentaAltaNotification::class);
    }

    public function test_venta_normal_no_genera_alerta(): void
    {
        Notification::fake();
        $this->usuarioCon('admin');

        (new NotificarVentaAlta)->handle(new VentaRegistrada($this->ventaConTotal(50_000)));

        Notification::assertNothingSent();
    }

    public function test_el_listener_esta_registrado_para_el_evento(): void
    {
        Event::fake();

        Event::assertListening(VentaRegistrada::class, NotificarVentaAlta::class);
    }

    public function test_comando_resumen_diario_notifica_al_admin(): void
    {
        Notification::fake();
        $admin = $this->usuarioCon('admin');

        $this->artisan('erp:resumen-diario')->assertSuccessful();

        Notification::assertSentTo($admin, ResumenDiarioNotification::class);
    }
}
