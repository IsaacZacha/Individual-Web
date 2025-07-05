<?php

namespace App\GraphQL\Mutations;

use App\Models\User;
use App\Models\Rol;
use App\Events\UserCreado;
use App\Events\UserActualizado;
use App\Events\UserEliminado;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UserMutations
{
    /**
     * Crear un nuevo usuario
     */
    public function crearUsuario($rootValue, array $args)
    {
        // Los argumentos vienen directamente debido a @spread
        $input = $args;
        
        // Validar entrada
        $validator = Validator::make($input, [
            'empleado_id' => 'required|exists:empleado,id_empleado',
            'username' => 'required|string|max:255|unique:users,username',
            'password' => 'required|string|min:8',
            'rol_id' => 'required|exists:rol,id_rol'
        ]);

        if ($validator->fails()) {
            throw new \Exception('Validación fallida: ' . implode(', ', $validator->errors()->all()));
        }

        // Crear usuario
        $user = User::create([
            'empleado_id' => $input['empleado_id'],
            'username' => $input['username'],
            'password' => Hash::make($input['password']),
            'rol_id' => $input['rol_id']
        ]);

        // Disparar evento para WebSocket
        broadcast(new UserCreado($user));

        return $user->load(['rol', 'empleado']);
    }

    /**
     * Actualizar usuario existente
     */
    public function actualizarUsuario($rootValue, array $args)
    {
        $id = $args['id_usuario'];
        // Obtener input del resto de argumentos debido a @spread
        $input = array_diff_key($args, ['id_usuario' => '']);
        
        $user = User::where('id_usuario', $id)->firstOrFail();
        
        // Validar entrada
        $validator = Validator::make($input, [
            'username' => 'sometimes|required|string|max:255|unique:users,username,' . $id . ',id_usuario',
            'password' => 'sometimes|string|min:8',
            'rol_id' => 'sometimes|required|exists:rol,id_rol',
            'empleado_id' => 'sometimes|required|exists:empleado,id_empleado'
        ]);

        if ($validator->fails()) {
            throw new \Exception('Validación fallida: ' . implode(', ', $validator->errors()->all()));
        }

        // Actualizar campos usando update para mejor rendimiento
        $user->update(array_filter([
            'username' => $input['username'] ?? $user->username,
            'password' => isset($input['password']) ? Hash::make($input['password']) : $user->password,
            'rol_id' => $input['rol_id'] ?? $user->rol_id,
            'empleado_id' => $input['empleado_id'] ?? $user->empleado_id
        ]));
        
        // Disparar evento para WebSocket
        broadcast(new UserActualizado($user));
        
        return $user->load(['rol', 'empleado']);
    }

    /**
     * Eliminar usuario
     */
    public function eliminarUsuario($rootValue, array $args)
    {
        $id = $args['id_usuario'];
        
        $user = User::where('id_usuario', $id)->firstOrFail();
        
        // Disparar evento antes de eliminar para tener los datos
        broadcast(new UserEliminado($user));
        
        $deleted = $user->delete();
        
        return [
            'success' => $deleted,
            'message' => $deleted ? 'Usuario eliminado correctamente' : 'Error al eliminar usuario'
        ];
    }

    /**
     * Cambiar contraseña
     */
    public function cambiarContrasena($rootValue, array $args)
    {
        $id = $args['id_usuario'];
        $nuevaContrasena = $args['nueva_contrasena'];
        
        $user = User::where('id_usuario', $id)->firstOrFail();
        $user->password = Hash::make($nuevaContrasena);
        $user->save();
        
        return $user;
    }

    /**
     * Actualizar perfil de usuario
     */
    public function actualizarPerfil($rootValue, array $args)
    {
        $id = $args['id_usuario'];
        
        $user = User::where('id_usuario', $id)->firstOrFail();
        
        if (isset($args['username'])) {
            $user->username = $args['username'];
        }
        
        if (isset($args['password'])) {
            $user->password = Hash::make($args['password']);
        }
        
        $user->save();
        
        return $user;
    }
}
