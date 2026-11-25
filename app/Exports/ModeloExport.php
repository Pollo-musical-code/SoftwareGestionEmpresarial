<?php

namespace App\Exports;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Schema;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

/** Exporta todas las columnas de un modelo (clientes, fichas, ventas...). */
class ModeloExport implements FromQuery, ShouldAutoSize, WithHeadings, WithMapping
{
    private array $columnas;

    public function __construct(private string $modelo)
    {
        $this->columnas = Schema::getColumnListing((new $modelo)->getTable());
    }

    public function query(): Builder
    {
        return ($this->modelo)::query();
    }

    public function headings(): array
    {
        return $this->columnas;
    }

    public function map($fila): array
    {
        return array_map(function ($c) use ($fila) {
            $v = $fila->{$c};

            return is_array($v) ? json_encode($v) : (is_object($v) ? (string) $v : $v);
        }, $this->columnas);
    }
}
