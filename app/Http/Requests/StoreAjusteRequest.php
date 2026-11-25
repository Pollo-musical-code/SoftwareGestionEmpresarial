<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreAjusteRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'stock_contado' => ['required', 'integer', 'min:0'],
            // Regla: todo ajuste debe explicar por qué se hace (auditoría)
            'motivo' => ['required', 'string', 'min:10', 'max:255'],
        ];
    }

    // Mensajes en español: el paquete de idioma de Laravel no está instalado
    public function messages(): array
    {
        return [
            'stock_contado.required' => 'Digite la cantidad contada en caja.',
            'stock_contado.integer' => 'La cantidad contada debe ser un número entero.',
            'stock_contado.min' => 'La cantidad contada no puede ser negativa.',
            'motivo.required' => 'El motivo del ajuste es obligatorio.',
            'motivo.min' => 'El motivo del ajuste debe tener al menos 10 caracteres.',
            'motivo.max' => 'El motivo del ajuste no puede superar los 255 caracteres.',
        ];
    }
}
