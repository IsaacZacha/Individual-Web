<?php

namespace App\GraphQL\Mutations;

use App\Models\VehiculoSucursal;
use App\Events\VehiculoAsignado;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Event;

class CrearVehiculoSucursal
{
    public function __invoke($root, array $args)
    {
        try {
            return DB::transaction(function () use ($args) {
                Log::info('Creando vehiculo-sucursal via GraphQL', ['input' => $args['input']]);
                
                $vehiculoSucursal = VehiculoSucursal::create($args['input']);
                
                Log::info('VehiculoSucursal creado exitosamente', [
                    'vehiculo_sucursal_id' => $vehiculoSucursal->id,
                    'id_vehiculo' => $vehiculoSucursal->id_vehiculo,
                    'id_sucursal' => $vehiculoSucursal->id_sucursal
                ]);
                
                // Disparar evento
                Event::dispatch(new VehiculoAsignado($vehiculoSucursal));
                
                return $vehiculoSucursal;
            });
        } catch (\Exception $e) {
            Log::error('Error al crear vehiculo-sucursal via GraphQL', [
                'error' => $e->getMessage(),
                'input' => $args['input']
            ]);
            throw $e;
        }
    }
}
