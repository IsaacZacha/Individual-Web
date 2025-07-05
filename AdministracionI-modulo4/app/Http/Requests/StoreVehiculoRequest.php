<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreVehiculoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'placa' => 'required|string|unique:vehiculo,placa|max:20',
            'marca' => 'required|string|max:255',
            'modelo' => 'required|string|max:255',
            'anio' => 'required|integer|min:1900|max:' . (date('Y') + 1),
            'tipo_id' => 'required|string|max:255',
            'estado' => 'required|string|max:255',
        ];
    }
    public function messages(): array
    {
        return [
            'placa.required' => 'La placa es obligatoria.',
            'placa.unique' => 'La placa ya está registrada.',
            'placa.max' => 'La placa no puede exceder los 20 caracteres.',
            'marca.required' => 'La marca es obligatoria.',
            'modelo.required' => 'El modelo es obligatorio.',
            'anio.required' => 'El año es obligatorio.',
            'anio.integer' => 'El año debe ser un número entero.',
            'anio.min' => 'El año debe ser mayor o igual a 1900.',
            'anio.max' => 'El año no puede ser mayor al próximo año.',
            'tipo_id.required' => 'El tipo de vehículo es obligatorio.',
            'estado.required' => 'El estado del vehículo es obligatorio.',
        ];
    }
}