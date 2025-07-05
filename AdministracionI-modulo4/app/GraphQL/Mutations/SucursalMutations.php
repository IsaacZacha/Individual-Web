<?php

namespace App\GraphQL\Mutations;

use App\Models\Sucursal;
use App\Events\SucursalCreada;
use App\Events\SucursalActualizada;
use App\Events\SucursalEliminada;
use GraphQL\Type\Definition\ResolveInfo;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;

class SucursalMutations
{
    /**
     * Crear una nueva sucursal
     */
    public function crear($rootValue, array $args, GraphQLContext $context, ResolveInfo $resolveInfo)
    {
        $sucursal = Sucursal::create([
            'nombre' => $args['nombre'],
            'direccion' => $args['direccion'],
            'ciudad' => $args['ciudad'],
            'telefono' => $args['telefono'] ?? null,
        ]);

        // Disparar evento para WebSocket
        broadcast(new SucursalCreada($sucursal));

        return $sucursal;
    }

    /**
     * Actualizar una sucursal existente
     */
    public function actualizar($rootValue, array $args, GraphQLContext $context, ResolveInfo $resolveInfo)
    {
        $sucursal = Sucursal::findOrFail($args['id_sucursal']);
        
        $sucursal->update([
            'nombre' => $args['nombre'],
            'direccion' => $args['direccion'],
            'ciudad' => $args['ciudad'],
            'telefono' => $args['telefono'] ?? null,
        ]);

        // Disparar evento para WebSocket
        broadcast(new SucursalActualizada($sucursal));

        return $sucursal;
    }

    /**
     * Crear sucursal (para GraphQL schema)
     */
    public function crearSucursal($rootValue, array $args)
    {
        // Los argumentos vienen directamente debido a @spread
        $input = $args;
        
        $sucursal = Sucursal::create([
            'nombre' => $input['nombre'],
            'direccion' => $input['direccion'],
            'ciudad' => $input['ciudad'],
            'telefono' => $input['telefono'] ?? null,
        ]);

        // Disparar evento para WebSocket
        broadcast(new SucursalCreada($sucursal));

        return $sucursal;
    }

    /**
     * Actualizar sucursal (para GraphQL schema)
     */
    public function actualizarSucursal($rootValue, array $args)
    {
        $id = $args['id_sucursal'];
        // Los argumentos vienen directamente debido a @spread, excepto el ID
        $input = $args;
        unset($input['id_sucursal']); // Remover el ID de los datos de actualización
        
        $sucursal = Sucursal::findOrFail($id);
        
        $sucursal->update([
            'nombre' => $input['nombre'] ?? $sucursal->nombre,
            'direccion' => $input['direccion'] ?? $sucursal->direccion,
            'ciudad' => $input['ciudad'] ?? $sucursal->ciudad,
            'telefono' => $input['telefono'] ?? $sucursal->telefono,
        ]);

        // Disparar evento para WebSocket
        broadcast(new SucursalActualizada($sucursal));

        return $sucursal;
    }

    /**
     * Eliminar una sucursal
     */
    public function eliminar($rootValue, array $args, GraphQLContext $context, ResolveInfo $resolveInfo)
    {
        $sucursal = Sucursal::findOrFail($args['id_sucursal']);
        $sucursalId = $sucursal->id_sucursal;
        
        $sucursal->delete();

        // Disparar evento para WebSocket
        broadcast(new SucursalEliminada($sucursalId));

        return true;
    }

    /**
     * Eliminar sucursal (para GraphQL schema)
     */
    public function eliminarSucursal($rootValue, array $args)
    {
        $id = $args['id_sucursal'];
        
        $sucursal = Sucursal::findOrFail($id);
        
        // Disparar evento para WebSocket antes de eliminar
        broadcast(new SucursalEliminada($sucursal->id_sucursal));
        
        $deleted = $sucursal->delete();
        
        return [
            'success' => $deleted,
            'message' => $deleted ? 'Sucursal eliminada correctamente' : 'Error al eliminar sucursal'
        ];
    }
}
