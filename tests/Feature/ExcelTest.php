<?php

namespace Tests\Feature;

use App\Models\Cliente;
use Database\Factories\ClienteFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Maatwebsite\Excel\Facades\Excel;
use Tests\Concerns\ConRoles;
use Tests\TestCase;

class ExcelTest extends TestCase
{
    use ConRoles, RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->prepararRoles();
    }

    public function test_exportar_clientes_descarga_un_excel(): void
    {
        Excel::fake();

        $this->actingAs($this->usuarioCon('supervisor'))->get(route('excel.clientes.exportar'));

        Excel::assertDownloaded('clientes.xlsx');
    }

    public function test_cajero_no_puede_exportar_ni_importar(): void
    {
        $cajero = $this->usuarioCon('cajero');

        $this->actingAs($cajero)->get(route('excel.clientes.exportar'))->assertForbidden();
        $this->actingAs($cajero)->get(route('excel.clientes.importar'))->assertForbidden();
    }

    public function test_importar_valida_cada_fila_y_el_saldo_inicia_en_cero(): void
    {
        $nivel = ClienteFactory::nivelVip();
        $nacidoMenor = now()->subYears(16)->format('Y-m-d');
        $csv = "documento,nombre,fecha_nacimiento,nivel_vip\n"
            ."111111,Jugador Valido,1985-03-10,$nivel\n"
            ."222222,Menor de Edad,$nacidoMenor,$nivel\n"
            .",Sin Documento,1980-01-01,$nivel\n";

        $respuesta = $this->actingAs($this->usuarioCon('admin'))->post(route('excel.clientes.importar.guardar'), [
            'archivo' => UploadedFile::fake()->createWithContent('clientes.csv', $csv),
        ]);

        $respuesta->assertOk()->assertViewHas('creados', 1);
        $this->assertDatabaseHas('clientes', ['documento' => '111111', 'saldo_fichas' => 0]);
        $this->assertDatabaseMissing('clientes', ['documento' => '222222']);
        $this->assertSame(1, Cliente::count());
    }
}
