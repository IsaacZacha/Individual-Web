<?php

namespace App\Http\Requests;

class UpdateUserRequest extends StoreUserRequest
{
    public function rules(): array
    {
        $rules = parent::rules();

        // Cambia 'required' por 'sometimes|required' en todas las reglas
        foreach ($rules as $key => &$rule) {
            $rule = str_replace('required', 'sometimes|required', $rule);
        }

        // Ajusta la regla unique para username (ignora el actual)
        $rules['username'] = 'sometimes|required|string|unique:users,username,' . $this->user . ',id_usuario|max:255';

        return $rules;
    }
}