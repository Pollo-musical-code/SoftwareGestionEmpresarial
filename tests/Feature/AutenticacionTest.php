<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\ConRoles;
use Tests\TestCase;

class AutenticacionTest extends TestCase
{
    use ConRoles, RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->prepararRoles();
    }

    public function test_invitado_es_redirigido_al_login(): void
    {
        $this->get('/clientes')->assertRedirect('/login');
    }

    public function test_usuario_activo_inicia_sesion(): void
    {
        $u = $this->usuarioCon('cajero');

        $this->post('/login', ['email' => $u->email, 'password' => 'password']);

        $this->assertAuthenticatedAs($u);
    }

    public function test_usuario_inactivo_no_puede_entrar(): void
    {
        $u = $this->usuarioCon('cajero', ['active' => false]);

        $this->post('/login', ['email' => $u->email, 'password' => 'password']);
        $this->get('/dashboard')->assertRedirect(route('login'));

        $this->assertGuest();
    }
}
