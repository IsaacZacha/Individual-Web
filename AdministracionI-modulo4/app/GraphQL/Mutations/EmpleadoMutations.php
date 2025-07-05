<?php

namespace App\GraphQL\Mutations;

use App\Models\Empleado;
use App\Events\EmpleadoCreado;
use App\Events\EmpleadoActualizado;
use App\Events\EmpleadoEliminado;
use Illuminate\Support\Facades\Validator;
use GraphQL\Type\Definition\ResolveInfo;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;

class EmpleadoMutations
{
    /**
     * Crear un nuevo empleado (método original)
     */
    public function crear($rootValue, array $args, GraphQLContext $context, ResolveInfo $resolveInfo)
    {
        $empleado = Empleado::create([
            'nombre' => $args['nombre'],
            'cargo' => $args['cargo'],
            'correo' => $args['correo'],
            'telefono' => $args['telefono'] ?? null,
        ]);

        // Disparar evento para WebSocket
        broadcast(new EmpleadoCreado($empleado));

        return $empleado;
    }

    /**
     * Crear empleado (para schema simplificado)
     */
    public function crearEmpleado($rootValue, array $args)
    {
        $input = $args['input'];
        
        $empleado = Empleado::create([
            'nombre' => $input['nombre'],
            'cargo' => $input['cargo'],
            'correo' => $input['correo'],
            'telefono' => $input['telefono'] ?? null,
        ]);

        // Disparar evento para WebSocket
        event(new EmpleadoCreado($empleado));

        return $empleado;
    }

    /**
     * Actualizar un empleado existente (método original)
     */
    public function actualizar($rootValue, array $args, GraphQLContext $context, ResolveInfo $resolveInfo)
    {
        $empleado = Empleado::findOrFail($args['id_empleado']);
        
        $empleado->update([
            'nombre' => $args['nombre'],
            'cargo' => $args['cargo'],
            'correo' => $args['correo'],
            'telefono' => $args['telefono'] ?? null,
        ]);

        // Disparar evento para WebSocket
        broadcast(new EmpleadoActualizado($empleado));

        return $empleado;
    }

    /**
     * Actualizar empleado (para schema simplificado)
     */
    public function actualizarEmpleado($rootValue, array $args)
    {
        $id = $args['id_empleado'];
        $input = $args['input'];
        
        $empleado = Empleado::findOrFail($id);
        
        $empleado->update($input);
        
        // Disparar evento para WebSocket
        event(new EmpleadoActualizado($empleado));
        
        return $empleado;
    }

    /**
     * Eliminar un empleado (método original)
     */
    public function eliminar($rootValue, array $args, GraphQLContext $context, ResolveInfo $resolveInfo)
    {
        $empleado = Empleado::findOrFail($args['id_empleado']);
        
        // Disparar evento para WebSocket antes de eliminar
        broadcast(new EmpleadoEliminado($empleado));
        
        $empleado->delete();
        
        return true;
    }

    /**
     * Eliminar empleado (para schema simplificado)
     */
    public function eliminarEmpleado($rootValue, array $args)
    {
        $id = $args['id_empleado'];
        
        $empleado = Empleado::findOrFail($id);
        
        // Disparar evento para WebSocket antes de eliminar
        event(new EmpleadoEliminado($empleado));
        
        $deleted = $empleado->delete();
        
        return [
            'success' => $deleted,
            'message' => $deleted ? 'Empleado eliminado correctamente' : 'Error al eliminar empleado',
            'deleted_id' => $id
        ];
    }
}
