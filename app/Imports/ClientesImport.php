<?php

namespace App\Imports;

use App\Models\Cliente;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use PhpOffice\PhpSpreadsheet\Shared\Date;

/**
 * Importa jugadores. Cada fila se valida; las inválidas se omiten y se reportan.
 * IMPORTANTE: el saldo de fichas NUNCA se importa (siempre inicia en 0): el saldo solo cambia por ventas/kardex.
 */
class ClientesImport implements SkipsOnFailure, ToModel, WithHeadingRow, WithValidation
{
    use Importable, SkipsFailures;

    public int $creados = 0;

    public function prepareForValidation($data, $index)
    {
        if (isset($data['documento'])) {
            $data['documento'] = trim((string) $data['documento']);
        }
        if (isset($data['fecha_nacimiento']) && is_numeric($data['fecha_nacimiento'])) {
            $data['fecha_nacimiento'] = Date::excelToDateTimeObject($data['fecha_nacimiento'])->format('Y-m-d');
        }

        return $data;
    }

    public function model(array $row): Model|array|null
    {
        $this->creados++;

        $cliente = new Cliente;
        $cliente->forceFill([
            'documento' => $row['documento'],
            'nombre' => $row['nombre'],
            'fecha_nacimiento' => $row['fecha_nacimiento'],
            'nivel_vip' => $row['nivel_vip'],
            'saldo_fichas' => 0,
        ]);

        return $cliente;
    }

    public function rules(): array
    {
        return [
            'documento' => ['required', 'max:30', Rule::unique('clientes', 'documento')->whereNull('deleted_at')],
            'nombre' => ['required', 'string', 'max:150'],
            'fecha_nacimiento' => ['required', 'date', 'before_or_equal:-18 years'],
            'nivel_vip' => ['required', 'string', 'max:30'],
        ];
    }

    public function customValidationMessages(): array
    {
        return [
            'documento.unique' => 'Ya existe un cliente con ese documento.',
            'fecha_nacimiento.before_or_equal' => 'El cliente debe ser mayor de 18 años.',
        ];
    }
}
