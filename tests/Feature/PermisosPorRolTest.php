<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\Concerns\ConRoles;
use Tests\TestCase;

class PermisosPorRolTest extends TestCase
{
    use ConRoles, RefreshDatabase;

    public static function matriz(): array
    {
        return [
            'admin ve auditoría' => ['admin', '/auditoria', 200],
            'admin ve usuarios' => ['admin', '/configuracion/usuarios', 200],
            'supervisor ve auditoría' => ['supervisor', '/auditoria', 200],
            'supervisor no gestiona usuarios' => ['supervisor', '/configuracion/usuarios', 403],
            'cajero ve clientes' => ['cajero', '/clientes', 200],
            'cajero no ve auditoría' => ['cajero', '/auditoria', 403],
            'crupier ve mesas' => ['crupier', '/mesas', 200],
            'crupier no ve clientes' => ['crupier', '/clientes', 403],
        ];
    }

    #[DataProvider('matriz')]
    public function test_matriz_de_permisos_por_rol(string $rol, string $ruta, int $esperado): void
    {
        $this->prepararRoles();

        $this->actingAs($this->usuarioCon($rol))->get($ruta)->assertStatus($esperado);
    }
}
