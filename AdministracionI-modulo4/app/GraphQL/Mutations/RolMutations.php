<?php

namespace App\GraphQL\Mutations;

use App\Models\Rol;
use App\Events\RolCreado;
use App\Events\RolActualizado;
use App\Events\RolEliminado;
use Illuminate\Support\Facades\Validator;

class RolMutations
{
    /**
     * Crear un nuevo rol
     */
    public function crear($rootValue, array $args)
    {
        $input = $args['input'];
        
        // Validar entrada
        $validator = Validator::make($input, [
            'nombre' => 'required|string|max:50|unique:rol',
            'descripcion' => 'nullable|string|max:200'
        ]);

        if ($validator->fails()) {
            throw new \Exception('Validación fallida: ' . implode(', ', $validator->errors()->all()));
        }

        // Crear rol
        $rol = Rol::create($input);

        return $rol;
    }

    /**
     * Actualizar rol existente
     */
    public function actualizar($rootValue, array $args)
    {
        $id = $args['id_rol'];
        $input = $args['input'];
        
        $rol = Rol::findOrFail($id);
        
        // Validar entrada
        $validator = Validator::make($input, [
            'nombre' => 'sometimes|required|string|max:50|unique:rol,nombre,' . $id . ',id_rol',
            'descripcion' => 'sometimes|nullable|string|max:200'
        ]);

        if ($validator->fails()) {
            throw new \Exception('Validación fallida: ' . implode(', ', $validator->errors()->all()));
        }

        // Actualizar campos
        $rol->update($input);
        
        return $rol;
    }

    /**
     * Crear rol (para GraphQL schema)
     */
    public function crearRol($rootValue, array $args)
    {
        return $this->crear($rootValue, $args);
    }

    /**
     * Actualizar rol (para GraphQL schema)
     */
    public function actualizarRol($rootValue, array $args)
    {
        return $this->actualizar($rootValue, $args);
    }

    /**
     * Eliminar rol
     */
    public function eliminar($rootValue, array $args)
    {
        $id = $args['id_rol'];
        
        $rol = Rol::findOrFail($id);
        
        // Verificar si hay empleados con este rol
        if ($rol->empleados()->count() > 0) {
            throw new \Exception('No se puede eliminar el rol porque tiene empleados asignados');
        }
        
        $deleted = $rol->delete();
        
        return [
            'success' => $deleted,
            'message' => $deleted ? 'Rol eliminado correctamente' : 'Error al eliminar rol'
        ];
    }

    /**
     * Eliminar rol (para GraphQL schema)
     */
    public function eliminarRol($rootValue, array $args)
    {
        return $this->eliminar($rootValue, $args);
    }
}
