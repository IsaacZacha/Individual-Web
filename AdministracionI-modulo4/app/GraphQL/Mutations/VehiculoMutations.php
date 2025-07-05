<?php

namespace App\GraphQL\Mutations;

use App\Models\Vehiculo;
use App\Events\VehiculoCreado;
use App\Events\VehiculoActualizado;
use App\Events\VehiculoEstadoCambiado;
use App\Events\VehiculoEliminado;
use Illuminate\Support\Facades\Validator;
use GraphQL\Type\Definition\ResolveInfo;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;

class VehiculoMutations
{
    /**
     * Crear un nuevo vehículo (método original)
     */
    public function crear($rootValue, array $args, GraphQLContext $context, ResolveInfo $resolveInfo)
    {
        $vehiculo = Vehiculo::create([
            'placa' => $args['placa'],
            'marca' => $args['marca'],
            'modelo' => $args['modelo'],
            'anio' => $args['anio'],
            'tipo_id' => $args['tipo_id'],
            'estado' => $args['estado'],
        ]);

        // Disparar evento para WebSocket
        broadcast(new VehiculoCreado($vehiculo));

        return $vehiculo;
    }

    /**
     * Crear un nuevo vehículo (para GraphQL schema)
     */
    public function crearVehiculo($rootValue, array $args)
    {
        // Los argumentos vienen directamente debido a @spread
        $input = $args;
        
        // Validar entrada
        $validator = Validator::make($input, [
            'placa' => 'required|string|max:20|unique:vehiculo',
            'marca' => 'required|string|max:100',
            'modelo' => 'required|string|max:100',
            'anio' => 'required|integer|min:1900|max:' . (date('Y') + 1),
            'tipo_id' => 'required|string|max:50',
            'estado' => 'required|string|in:disponible,ocupado,mantenimiento'
        ]);

        if ($validator->fails()) {
            throw new \Exception('Validación fallida: ' . implode(', ', $validator->errors()->all()));
        }

        // Crear vehículo
        $vehiculo = Vehiculo::create($input);

        // Disparar evento para WebSocket
        broadcast(new VehiculoCreado($vehiculo));

        return $vehiculo;
    }

    /**
     * Actualizar un vehículo existente (método original)
     */
    public function actualizar($rootValue, array $args, GraphQLContext $context, ResolveInfo $resolveInfo)
    {
        $vehiculo = Vehiculo::findOrFail($args['id_vehiculo']);
        
        $vehiculo->update([
            'placa' => $args['placa'],
            'marca' => $args['marca'],
            'modelo' => $args['modelo'],
            'anio' => $args['anio'],
            'tipo_id' => $args['tipo_id'],
            'estado' => $args['estado'],
        ]);

        // Disparar evento para WebSocket
        broadcast(new VehiculoActualizado($vehiculo));

        return $vehiculo;
    }

    /**
     * Actualizar vehículo existente (para GraphQL schema)
     */
    public function actualizarVehiculo($rootValue, array $args)
    {
        $id = $args['id_vehiculo'];
        // Los argumentos vienen directamente debido a @spread, excepto el ID
        $input = $args;
        unset($input['id_vehiculo']); // Remover el ID de los datos de actualización
        
        $vehiculo = Vehiculo::findOrFail($id);
        
        // Validar entrada
        $validator = Validator::make($input, [
            'placa' => 'sometimes|required|string|max:20|unique:vehiculo,placa,' . $id . ',id_vehiculo',
            'marca' => 'sometimes|required|string|max:100',
            'modelo' => 'sometimes|required|string|max:100',
            'anio' => 'sometimes|required|integer|min:1900|max:' . (date('Y') + 1),
            'tipo_id' => 'sometimes|required|string|max:50',
            'estado' => 'sometimes|required|string|in:disponible,ocupado,mantenimiento'
        ]);

        if ($validator->fails()) {
            throw new \Exception('Validación fallida: ' . implode(', ', $validator->errors()->all()));
        }

        // Actualizar campos
        $vehiculo->update($input);
        
        // Disparar evento para WebSocket
        broadcast(new VehiculoActualizado($vehiculo));
        
        return $vehiculo;
    }

    /**
     * Cambiar estado del vehículo
     */
    public function cambiarEstado($rootValue, array $args, GraphQLContext $context, ResolveInfo $resolveInfo)
    {
        $vehiculo = Vehiculo::findOrFail($args['id_vehiculo']);
        $estadoAnterior = $vehiculo->estado;
        
        $vehiculo->update([
            'estado' => $args['estado']
        ]);

        // Disparar evento para WebSocket
        broadcast(new VehiculoEstadoCambiado($vehiculo, $estadoAnterior));

        return $vehiculo;
    }

    /**
     * Cambiar estado del vehículo (para GraphQL schema)
     */
    public function cambiarEstadoVehiculo($rootValue, array $args)
    {
        $id = $args['id_vehiculo'];
        $estado = $args['estado'];
        
        // Validar estado
        if (!in_array($estado, ['disponible', 'ocupado', 'mantenimiento'])) {
            throw new \Exception('Estado no válido. Debe ser: disponible, ocupado o mantenimiento');
        }
        
        $vehiculo = Vehiculo::findOrFail($id);
        $estadoAnterior = $vehiculo->estado;
        
        $vehiculo->estado = $estado;
        $vehiculo->save();
        
        // Disparar evento para WebSocket
        broadcast(new VehiculoEstadoCambiado($vehiculo, $estadoAnterior));
        
        return $vehiculo;
    }

    /**
     * Eliminar un vehículo (método original)
     */
    public function eliminar($rootValue, array $args, GraphQLContext $context, ResolveInfo $resolveInfo)
    {
        $vehiculo = Vehiculo::findOrFail($args['id_vehiculo']);
        
        // Disparar evento para WebSocket antes de eliminar
        broadcast(new VehiculoEliminado($vehiculo));
        
        $vehiculo->delete();
        
        return true;
    }

    /**
     * Eliminar vehículo (para GraphQL schema)
     */
    public function eliminarVehiculo($rootValue, array $args)
    {
        $id = $args['id_vehiculo'];
        
        $vehiculo = Vehiculo::findOrFail($id);
        
        // Disparar evento para WebSocket antes de eliminar
        broadcast(new VehiculoEliminado($vehiculo));
        
        $deleted = $vehiculo->delete();
        
        return [
            'success' => $deleted,
            'message' => $deleted ? 'Vehículo eliminado correctamente' : 'Error al eliminar vehículo'
        ];
    }
}
