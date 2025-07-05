<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreVehiculoSucursalRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'vehiculo_id' => 'required|integer|exists:vehiculo,id_vehiculo',
            'sucursal_id' => 'required|integer|exists:sucursal,id_sucursal',
            'fecha_asignacion' => 'nullable|date',
        ];
    }
    
    public function messages(): array
    {
        return [
            'vehiculo_id.required' => 'El ID del vehículo es obligatorio.',
            'vehiculo_id.exists' => 'El vehículo seleccionado no existe.',
            'sucursal_id.required' => 'El ID de la sucursal es obligatorio.',
            'sucursal_id.exists' => 'La sucursal seleccionada no existe.',
            'fecha_asignacion.date' => 'La fecha de asignación debe ser una fecha válida.',
        ];
    }
}