<?php

namespace App\GraphQL\Mutations;

use App\Models\VehiculoSucursal;
use App\Events\VehiculoSucursalActualizado;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Event;

class ActualizarVehiculoSucursal
{
    public function __invoke($root, array $args)
    {
        try {
            return DB::transaction(function () use ($args) {
                Log::info('Actualizando vehiculo-sucursal via GraphQL', [
                    'vehiculo_sucursal_id' => $args['id'],
                    'input' => $args['input']
                ]);
                
                $vehiculoSucursal = VehiculoSucursal::findOrFail($args['id']);
                $vehiculoSucursal->update($args['input']);
                
                Log::info('VehiculoSucursal actualizado exitosamente', [
                    'vehiculo_sucursal_id' => $vehiculoSucursal->id,
                    'id_vehiculo' => $vehiculoSucursal->id_vehiculo,
                    'id_sucursal' => $vehiculoSucursal->id_sucursal
                ]);
                
                // Disparar evento
                Event::dispatch(new VehiculoSucursalActualizado($vehiculoSucursal));
                
                return $vehiculoSucursal;
            });
        } catch (\Exception $e) {
            Log::error('Error al actualizar vehiculo-sucursal via GraphQL', [
                'error' => $e->getMessage(),
                'vehiculo_sucursal_id' => $args['id'],
                'input' => $args['input']
            ]);
            throw $e;
        }
    }
}
