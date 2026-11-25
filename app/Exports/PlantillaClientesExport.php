<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;

class PlantillaClientesExport implements FromArray, ShouldAutoSize, WithHeadings
{
    public function array(): array
    {
        return [['1020304050', 'Juan Pérez', '1990-05-20', 'Regular']];
    }

    public function headings(): array
    {
        return ['documento', 'nombre', 'fecha_nacimiento', 'nivel_vip'];
    }
}
