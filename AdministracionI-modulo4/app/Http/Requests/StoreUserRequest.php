<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'empleado_id' => 'required|integer|exists:empleado,id_empleado',
            'username' => 'required|string|unique:users,username|max:255',
            'password' => 'required|string|min:6',
            'rol_id' => 'required|integer|exists:rol,id_rol',
        ];
    }
    public function messages(): array
    {
        return [
            'empleado_id.required' => 'El ID del empleado es obligatorio.',
            'empleado_id.exists' => 'El empleado seleccionado no existe.',
            'username.required' => 'El nombre de usuario es obligatorio.',
            'username.unique' => 'El nombre de usuario ya está en uso.',
            'username.max' => 'El nombre de usuario no puede exceder los 255 caracteres.',
            'password.required' => 'La contraseña es obligatoria.',
            'password.min' => 'La contraseña debe tener al menos 6 caracteres.',
            'rol_id.required' => 'El ID del rol es obligatorio.',
            'rol_id.exists' => 'El rol seleccionado no existe.',
        ];
    }
}