<?php

namespace App\GraphQL\Mutations;

use App\Models\VehiculoSucursal;
use App\Events\VehiculoAsignado;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Event;

class AsignarVehiculoSucursal
{
    public function __invoke($root, array $args)
    {
        try {
            $result = DB::transaction(function () use ($args) {
                Log::info('Asignando vehiculo-sucursal via GraphQL', ['args' => $args]);
                
                $vehiculoSucursal = VehiculoSucursal::create([
                    'id_vehiculo' => $args['id_vehiculo'],
                    'id_sucursal' => $args['id_sucursal'],
                    'fecha_asignacion' => $args['fecha_asignacion']
                ]);
                
                Log::info('VehiculoSucursal asignado exitosamente', [
                    'vehiculo_sucursal_id' => $vehiculoSucursal->id,
                    'id_vehiculo' => $vehiculoSucursal->id_vehiculo,
                    'id_sucursal' => $vehiculoSucursal->id_sucursal
                ]);
                
                return $vehiculoSucursal;
            });
            
            // Disparar evento después de la transacción
            Event::dispatch(new VehiculoAsignado($result));
            
            return $result;
        } catch (\Exception $e) {
            Log::error('Error al asignar vehiculo-sucursal via GraphQL', [
                'error' => $e->getMessage(),
                'args' => $args
            ]);
            throw $e;
        }
    }
}
