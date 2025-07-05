<?php

namespace App\Http\Requests;

class UpdateEmpleadoRequest extends StoreEmpleadoRequest
{
    public function rules(): array
    {
        $rules = parent::rules();

        foreach ($rules as $key => &$rule) {
            $rule = str_replace('required', 'sometimes|required', $rule);
        }

        // Ajusta la regla unique para el correo (ignora el actual)
        $empleadoId = $this->route('empleado');
        if (is_object($empleadoId)) {
            $empleadoId = $empleadoId->id_empleado ?? $empleadoId->getKey();
        }
        $rules['correo'] = 'sometimes|required|email|unique:empleado,correo,' . $empleadoId . ',id_empleado';

        return $rules;
    }
}