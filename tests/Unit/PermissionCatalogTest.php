<?php

namespace Tests\Unit;

use App\Support\PermissionCatalog;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class PermissionCatalogTest extends TestCase
{
    public static function permisos(): array
    {
        return [
            'ver clientes' => ['ver-clientes', 'ver', 'clientes'],
            'recibir compras' => ['recibir-compras', 'recibir', 'compras'],
            'ajustar inventario' => ['ajustar-inventario', 'ajustar', 'inventario'],
            'exportar fichas' => ['exportar-fichas', 'exportar', 'fichas'],
            'sin guion' => ['administrar', 'administrar', 'general'],
        ];
    }

    #[DataProvider('permisos')]
    public function test_divide_el_permiso_en_accion_y_modulo(string $permiso, string $accion, string $modulo): void
    {
        $this->assertSame([$accion, $modulo], PermissionCatalog::split($permiso));
    }

    public function test_solo_admin_es_super_rol(): void
    {
        $this->assertTrue(PermissionCatalog::isSuperRole('admin'));
        $this->assertFalse(PermissionCatalog::isSuperRole('cajero'));
        $this->assertSame('admin', PermissionCatalog::superRole());
    }
}
