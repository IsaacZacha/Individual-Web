<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreSucursalRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nombre' => 'required|string|max:255',
            'direccion' => 'required|string|max:255',
            'ciudad' => 'required|string|max:255',
            'telefono' => 'required|string|max:20',
        ];
    }
    public function messages(): array
    {
        return [
            'nombre.required' => 'El nombre de la sucursal es obligatorio.',
            'direccion.required' => 'La dirección de la sucursal es obligatoria.',
            'ciudad.required' => 'La ciudad de la sucursal es obligatoria.',
            'telefono.required' => 'El teléfono de la sucursal es obligatorio.',
            'telefono.max' => 'El teléfono no puede exceder los 20 caracteres.',
        ];
    }
}