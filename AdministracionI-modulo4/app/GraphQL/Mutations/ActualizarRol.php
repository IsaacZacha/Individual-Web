<?php

namespace App\GraphQL\Mutations;

use App\Models\Rol;
use App\Events\RolActualizado;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Event;

class ActualizarRol
{
    public function __invoke($root, array $args)
    {
        try {
            return DB::transaction(function () use ($args) {
                Log::info('Actualizando rol via GraphQL', [
                    'rol_id' => $args['id_rol'],
                    'input' => $args['input']
                ]);
                
                $rol = Rol::where('id_rol', $args['id_rol'])->firstOrFail();
                $rol->update($args['input']);
                
                Log::info('Rol actualizado exitosamente', [
                    'rol_id' => $rol->id_rol,
                    'nombre' => $rol->nombre
                ]);
                
                // Disparar evento
                Event::dispatch(new RolActualizado($rol));
                
                return $rol;
            });
        } catch (\Exception $e) {
            Log::error('Error al actualizar rol via GraphQL', [
                'error' => $e->getMessage(),
                'rol_id' => $args['id_rol'],
                'input' => $args['input']
            ]);
            throw $e;
        }
    }
}
