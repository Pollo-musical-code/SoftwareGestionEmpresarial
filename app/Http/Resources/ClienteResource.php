<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ClienteResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'documento' => $this->documento,
            'nombre' => $this->nombre,
            'fecha_nacimiento' => $this->fecha_nacimiento ? (string) $this->fecha_nacimiento : null,
            'nivel_vip' => $this->nivel_vip,
            'saldo_fichas' => $this->saldo_fichas,
        ];
    }
}
