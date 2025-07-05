<?php

namespace App\GraphQL\Mutations;

use App\Models\Rol;
use App\Events\RolCreado;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Event;

class CrearRol
{
    public function __invoke($root, array $args)
    {
        try {
            return DB::transaction(function () use ($args) {
                Log::info('Creando rol via GraphQL', ['input' => $args['input']]);
                
                $rol = Rol::create($args['input']);
                
                Log::info('Rol creado exitosamente', [
                    'rol_id' => $rol->id_rol,
                    'nombre' => $rol->nombre
                ]);
                
                // Disparar evento
                Event::dispatch(new RolCreado($rol));
                
                return $rol;
            });
        } catch (\Exception $e) {
            Log::error('Error al crear rol via GraphQL', [
                'error' => $e->getMessage(),
                'input' => $args['input']
            ]);
            throw $e;
        }
    }
}
