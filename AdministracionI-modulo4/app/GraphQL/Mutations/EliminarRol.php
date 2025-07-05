<?php

namespace App\GraphQL\Mutations;

use App\Models\Rol;
use App\Events\RolEliminado;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Event;

class EliminarRol
{
    public function __invoke($root, array $args)
    {
        try {
            return DB::transaction(function () use ($args) {
                Log::info('Eliminando rol via GraphQL', ['rol_id' => $args['id_rol']]);
                
                $rol = Rol::where('id_rol', $args['id_rol'])->firstOrFail();
                $rolId = $rol->id_rol;
                
                // Disparar evento antes de eliminar
                Event::dispatch(new RolEliminado($rol));
                
                $rol->delete();
                
                Log::info('Rol eliminado exitosamente', ['rol_id' => $rolId]);
                
                return [
                    'success' => true,
                    'message' => 'Rol eliminado exitosamente',
                    'deleted_id' => (string) $rolId
                ];
            });
        } catch (\Exception $e) {
            Log::error('Error al eliminar rol via GraphQL', [
                'error' => $e->getMessage(),
                'rol_id' => $args['id_rol']
            ]);
            throw $e;
        }
    }
}
