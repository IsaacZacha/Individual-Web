<?php

namespace App\GraphQL\Mutations;

use App\Models\Sucursal;
use App\Events\SucursalEliminada;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Event;

class EliminarSucursal
{
    public function __invoke($root, array $args)
    {
        try {
            return DB::transaction(function () use ($args) {
                Log::info('Eliminando sucursal via GraphQL', ['sucursal_id' => $args['id_sucursal']]);
                
                $sucursal = Sucursal::where('id_sucursal', $args['id_sucursal'])->firstOrFail();
                $sucursalId = $sucursal->id_sucursal;
                
                // Disparar evento antes de eliminar
                Event::dispatch(new SucursalEliminada($sucursal));
                
                $sucursal->delete();
                
                Log::info('Sucursal eliminada exitosamente', ['sucursal_id' => $sucursalId]);
                
                return [
                    'success' => true,
                    'message' => 'Sucursal eliminada exitosamente',
                    'deleted_id' => (string) $sucursalId
                ];
            });
        } catch (\Exception $e) {
            Log::error('Error al eliminar sucursal via GraphQL', [
                'error' => $e->getMessage(),
                'sucursal_id' => $args['id_sucursal']
            ]);
            throw $e;
        }
    }
}
