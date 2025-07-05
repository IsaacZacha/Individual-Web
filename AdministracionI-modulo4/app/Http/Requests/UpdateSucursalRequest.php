<?php

namespace App\Http\Requests;

class UpdateSucursalRequest extends StoreSucursalRequest
{
    public function rules(): array
    {
        $rules = parent::rules();

        // Cambia 'required' por 'sometimes|required' en todas las reglas
        foreach ($rules as $key => &$rule) {
            $rule = str_replace('required', 'sometimes|required', $rule);
        }

        return $rules;
    }
}