<?php

namespace App\GraphQL\Mutations;

use App\Models\VehiculoSucursal;
use App\Events\VehiculoDesasignado;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Event;

class EliminarVehiculoSucursal
{
    public function __invoke($root, array $args)
    {
        try {
            $result = DB::transaction(function () use ($args) {
                Log::info('Eliminando vehiculo-sucursal via GraphQL', ['vehiculo_sucursal_id' => $args['id']]);
                
                $vehiculoSucursal = VehiculoSucursal::findOrFail($args['id']);
                $vehiculoSucursalId = $vehiculoSucursal->id;
                
                $vehiculoSucursal->delete();
                
                Log::info('VehiculoSucursal eliminado exitosamente', ['vehiculo_sucursal_id' => $vehiculoSucursalId]);
                
                return [
                    'success' => true,
                    'message' => 'VehiculoSucursal eliminado exitosamente',
                    'deleted_id' => (string) $vehiculoSucursalId,
                    'original_id' => $vehiculoSucursalId
                ];
            });
            
            // Disparar evento después de la transacción
            Event::dispatch(new VehiculoDesasignado($result['original_id']));
            
            return $result;
        } catch (\Exception $e) {
            Log::error('Error al eliminar vehiculo-sucursal via GraphQL', [
                'error' => $e->getMessage(),
                'vehiculo_sucursal_id' => $args['id']
            ]);
            throw $e;
        }
    }
}
