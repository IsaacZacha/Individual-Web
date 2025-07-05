<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreEmpleadoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nombre' => 'required|string|max:255',
            'cargo' => 'required|string|max:255',
            'correo' => 'required|email|unique:empleado,correo',
            'telefono' => 'required|string|max:20',
        ];
    }
    public function messages(): array
    {
        return [
            'nombre.required' => 'El nombre es obligatorio.',
            'cargo.required' => 'El cargo es obligatorio.',
            'correo.required' => 'El correo es obligatorio.',
            'correo.email' => 'El correo debe ser un email válido.',
            'correo.unique' => 'El correo ya está registrado.',
            'telefono.required' => 'El teléfono es obligatorio.',
        ];
    }
}