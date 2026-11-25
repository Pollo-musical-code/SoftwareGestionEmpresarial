<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\ConRoles;
use Tests\TestCase;

class RolActivoTest extends TestCase
{
    use ConRoles, RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->prepararRoles();
    }

    public function test_con_varios_roles_debe_elegir_el_rol_activo(): void
    {
        $u = $this->usuarioCon(['admin', 'cajero']);

        $this->actingAs($u)->get('/dashboard')->assertRedirect(route('rol-activo.elegir'));
    }

    public function test_con_rol_cajero_no_accede_a_configuracion_aunque_sea_admin(): void
    {
        $u = $this->usuarioCon(['admin', 'cajero']);

        $this->actingAs($u)->post(route('rol-activo.guardar'), ['rol' => 'cajero'])
            ->assertSessionHas('active_role', 'cajero');

        $this->actingAs($u)->withSession(['active_role' => 'cajero'])
            ->get('/configuracion/usuarios')->assertForbidden();
    }

    public function test_con_rol_admin_si_accede_a_configuracion(): void
    {
        $u = $this->usuarioCon(['admin', 'cajero']);

        $this->actingAs($u)->withSession(['active_role' => 'admin'])
            ->get('/configuracion/usuarios')->assertOk();
    }

    public function test_no_se_puede_elegir_un_rol_que_no_se_tiene(): void
    {
        $u = $this->usuarioCon(['cajero', 'crupier']);

        $this->actingAs($u)->post(route('rol-activo.guardar'), ['rol' => 'admin'])
            ->assertSessionHasErrors('rol');
    }

    public function test_permiso_directo_se_suma_al_rol_activo(): void
    {
        $u = $this->usuarioCon('crupier');
        $u->givePermissionTo('ver-clientes');

        $this->actingAs($u)->get('/clientes')->assertOk();
    }
}
