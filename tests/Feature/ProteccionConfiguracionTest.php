<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\Concerns\ConRoles;
use Tests\TestCase;

class ProteccionConfiguracionTest extends TestCase
{
    use ConRoles, RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->prepararRoles();
    }

    public function test_no_puedes_desactivarte_a_ti_mismo(): void
    {
        $admin = $this->usuarioCon('admin');
        $this->usuarioCon('admin'); // otro admin

        $this->actingAs($admin)->patch(route('settings.users.toggle', $admin))->assertSessionHasErrors('regla');

        $this->assertTrue((bool) $admin->fresh()->active);
    }

    public function test_no_se_puede_desactivar_al_ultimo_admin_activo(): void
    {
        $unico = $this->usuarioCon('admin');
        $gestor = $this->usuarioCon('supervisor');
        $gestor->givePermissionTo('gestionar-usuarios'); // permiso directo

        $this->actingAs($gestor)->patch(route('settings.users.toggle', $unico))->assertSessionHasErrors('regla');

        $this->assertTrue((bool) $unico->fresh()->active);
    }

    public function test_admin_puede_desactivar_a_un_cajero(): void
    {
        $admin = $this->usuarioCon('admin');
        $cajero = $this->usuarioCon('cajero');

        $this->actingAs($admin)->patch(route('settings.users.toggle', $cajero));

        $this->assertFalse((bool) $cajero->fresh()->active);
    }

    public function test_no_se_elimina_un_rol_con_usuarios_ni_el_admin(): void
    {
        $admin = $this->usuarioCon('admin');
        $this->usuarioCon('cajero');

        $this->actingAs($admin)->delete(route('settings.roles.destroy', Role::findByName('cajero')))
            ->assertSessionHasErrors('regla');
        $this->actingAs($admin)->delete(route('settings.roles.destroy', Role::findByName('admin')))
            ->assertSessionHasErrors('regla');

        $this->assertDatabaseHas('roles', ['name' => 'cajero']);
        $this->assertDatabaseHas('roles', ['name' => 'admin']);
    }

    public function test_la_matriz_actualiza_un_rol_y_el_admin_conserva_todo(): void
    {
        $admin = $this->usuarioCon('admin');
        $cajero = Role::findByName('cajero');

        $this->actingAs($admin)->put(route('settings.permissions.update'), [
            'matriz' => [$cajero->id => ['ver-clientes']],
        ])->assertSessionHasNoErrors();

        $this->assertSame(['ver-clientes'], $cajero->fresh()->permissions->pluck('name')->all());
        $this->assertSame(Permission::count(), Role::findByName('admin')->permissions->count());
    }
}
