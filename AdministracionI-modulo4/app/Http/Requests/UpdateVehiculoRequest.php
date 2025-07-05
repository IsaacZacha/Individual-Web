<?php

namespace App\Http\Requests;

class UpdateVehiculoRequest extends StoreVehiculoRequest
{
    public function rules(): array
    {
        $rules = parent::rules();

        // Cambia 'required' por 'sometimes|required' en todas las reglas
        foreach ($rules as $key => &$rule) {
            $rule = str_replace('required', 'sometimes|required', $rule);
        }

        // Ajusta la regla unique para la placa (ignora el actual)
        $rules['placa'] = 'sometimes|required|string|unique:vehiculo,placa,' . $this->vehiculo . ',id_vehiculo|max:20';

        return $rules;
    }
}