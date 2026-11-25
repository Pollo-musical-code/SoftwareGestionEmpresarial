<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\Concerns\ConRoles;
use Tests\TestCase;

class ApiTest extends TestCase
{
    use ConRoles, RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->prepararRoles();
    }

    public function test_rutas_protegidas_exigen_token(): void
    {
        $this->getJson('/api/v1/clientes')->assertUnauthorized();
    }

    public function test_login_devuelve_token_con_el_rol_activo(): void
    {
        $u = $this->usuarioCon('cajero');

        $this->postJson('/api/v1/login', ['email' => $u->email, 'password' => 'password'])
            ->assertOk()->assertJsonStructure(['token', 'rol_activo'])->assertJsonPath('rol_activo', 'cajero');

        $this->assertSame(['rol:cajero'], $u->tokens()->first()->abilities);
    }

    public function test_login_con_varios_roles_exige_elegir_rol(): void
    {
        $u = $this->usuarioCon(['cajero', 'crupier']);

        $this->postJson('/api/v1/login', ['email' => $u->email, 'password' => 'password'])->assertStatus(422);
        $this->postJson('/api/v1/login', ['email' => $u->email, 'password' => 'password', 'rol' => 'crupier'])->assertOk();
    }

    public function test_login_rechaza_usuario_inactivo(): void
    {
        $u = $this->usuarioCon('cajero', ['active' => false]);

        $this->postJson('/api/v1/login', ['email' => $u->email, 'password' => 'password'])->assertForbidden();
    }

    public function test_el_rol_del_token_define_los_permisos(): void
    {
        $u = $this->usuarioCon(['cajero', 'crupier']);

        Sanctum::actingAs($u, ['rol:crupier']);
        $this->getJson('/api/v1/clientes')->assertForbidden();

        Sanctum::actingAs($u, ['rol:cajero']);
        $this->getJson('/api/v1/clientes')->assertOk();
    }

    public function test_cajero_crea_cliente_y_se_rechaza_menor_de_edad(): void
    {
        $u = $this->usuarioCon('cajero');
        Sanctum::actingAs($u, ['rol:cajero']);

        $this->postJson('/api/v1/clientes', [
            'documento' => '900100200', 'nombre' => 'Ana Jugadora',
            'fecha_nacimiento' => '1990-01-15', 'nivel_vip' => 'Regular',
        ])->assertCreated();

        $this->postJson('/api/v1/clientes', [
            'documento' => '900100201', 'nombre' => 'Menor',
            'fecha_nacimiento' => now()->subYears(15)->format('Y-m-d'), 'nivel_vip' => 'Regular',
        ])->assertStatus(422)->assertJsonValidationErrors('fecha_nacimiento');
    }
}
