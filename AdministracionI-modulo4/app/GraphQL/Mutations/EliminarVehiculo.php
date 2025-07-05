<?php

namespace App\GraphQL\Mutations;

use App\Models\Vehiculo;
use App\Events\VehiculoEliminado;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Event;

class EliminarVehiculo
{
    public function __invoke($root, array $args)
    {
        try {
            return DB::transaction(function () use ($args) {
                Log::info('Eliminando vehiculo via GraphQL', ['vehiculo_id' => $args['id_vehiculo']]);
                
                $vehiculo = Vehiculo::where('id_vehiculo', $args['id_vehiculo'])->firstOrFail();
                $vehiculoId = $vehiculo->id_vehiculo;
                
                // Disparar evento antes de eliminar
                Event::dispatch(new VehiculoEliminado($vehiculo));
                
                $vehiculo->delete();
                
                Log::info('Vehiculo eliminado exitosamente', ['vehiculo_id' => $vehiculoId]);
                
                return [
                    'success' => true,
                    'message' => 'Vehículo eliminado exitosamente',
                    'deleted_id' => (string) $vehiculoId
                ];
            });
        } catch (\Exception $e) {
            Log::error('Error al eliminar vehiculo via GraphQL', [
                'error' => $e->getMessage(),
                'vehiculo_id' => $args['id_vehiculo']
            ]);
            throw $e;
        }
    }
}
