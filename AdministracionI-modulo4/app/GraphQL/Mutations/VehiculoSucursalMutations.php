<?php

namespace App\GraphQL\Mutations;

use App\Models\VehiculoSucursal;
use App\Events\VehiculoAsignado;
use App\Events\VehiculoDesasignado;
use GraphQL\Type\Definition\ResolveInfo;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;

class VehiculoSucursalMutations
{
    /**
     * Asignar un vehículo a una sucursal
     */
    public function asignar($rootValue, array $args, GraphQLContext $context, ResolveInfo $resolveInfo)
    {
        $asignacion = VehiculoSucursal::create([
            'id_vehiculo' => $args['id_vehiculo'],
            'id_sucursal' => $args['id_sucursal'],
            'fecha_asignacion' => $args['fecha_asignacion'],
        ]);

        // Cargar las relaciones para tener los datos completos
        $asignacion->load(['vehiculo', 'sucursal']);

        // Disparar evento para WebSocket
        broadcast(new VehiculoAsignado($asignacion));

        return $asignacion;
    }

    /**
     * Asignar vehículo a sucursal (para GraphQL schema)
     */
    public function asignarVehiculoSucursal($rootValue, array $args)
    {
        $input = $args['input'];
        
        $asignacion = VehiculoSucursal::create([
            'id_vehiculo' => $input['id_vehiculo'],
            'id_sucursal' => $input['id_sucursal'],
            'fecha_asignacion' => $input['fecha_asignacion'],
        ]);

        // Cargar las relaciones para tener los datos completos
        $asignacion->load(['vehiculo', 'sucursal']);

        // Disparar evento para WebSocket
        broadcast(new VehiculoAsignado($asignacion));

        return $asignacion;
    }

    /**
     * Actualizar asignación vehículo-sucursal
     */
    public function actualizar($rootValue, array $args, GraphQLContext $context, ResolveInfo $resolveInfo)
    {
        $asignacion = VehiculoSucursal::findOrFail($args['id']);
        
        $asignacion->update([
            'id_vehiculo' => $args['id_vehiculo'] ?? $asignacion->id_vehiculo,
            'id_sucursal' => $args['id_sucursal'] ?? $asignacion->id_sucursal,
            'fecha_asignacion' => $args['fecha_asignacion'] ?? $asignacion->fecha_asignacion,
        ]);

        // Cargar las relaciones para tener los datos completos
        $asignacion->load(['vehiculo', 'sucursal']);

        // Disparar evento para WebSocket
        broadcast(new VehiculoAsignado($asignacion));

        return $asignacion;
    }

    /**
     * Actualizar asignación vehículo-sucursal (para GraphQL schema)
     */
    public function actualizarVehiculoSucursal($rootValue, array $args)
    {
        $id = $args['id'];
        $input = $args['input'];
        
        $asignacion = VehiculoSucursal::findOrFail($id);
        
        $asignacion->update([
            'id_vehiculo' => $input['id_vehiculo'] ?? $asignacion->id_vehiculo,
            'id_sucursal' => $input['id_sucursal'] ?? $asignacion->id_sucursal,
            'fecha_asignacion' => $input['fecha_asignacion'] ?? $asignacion->fecha_asignacion,
        ]);

        // Cargar las relaciones para tener los datos completos
        $asignacion->load(['vehiculo', 'sucursal']);

        // Disparar evento para WebSocket
        broadcast(new VehiculoAsignado($asignacion));

        return $asignacion;
    }

    /**
     * Desasignar un vehículo de una sucursal
     */
    public function desasignar($rootValue, array $args, GraphQLContext $context, ResolveInfo $resolveInfo)
    {
        $asignacion = VehiculoSucursal::findOrFail($args['id']);
        $asignacionId = $asignacion->id;
        
        $asignacion->delete();

        // Disparar evento para WebSocket
        broadcast(new VehiculoDesasignado($asignacionId));

        return true;
    }

    /**
     * Desasignar vehículo de sucursal (para GraphQL schema)
     */
    public function desasignarVehiculoSucursal($rootValue, array $args)
    {
        $id = $args['id'];
        
        $asignacion = VehiculoSucursal::findOrFail($id);
        
        // Disparar evento para WebSocket antes de eliminar
        broadcast(new VehiculoDesasignado($asignacion->id));
        
        $deleted = $asignacion->delete();
        
        return [
            'success' => $deleted,
            'message' => $deleted ? 'Vehículo desasignado correctamente' : 'Error al desasignar vehículo'
        ];
    }
}
