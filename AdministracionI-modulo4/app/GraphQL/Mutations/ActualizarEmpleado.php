<?php

namespace App\GraphQL\Mutations;

use App\Models\Empleado;
use App\Events\EmpleadoActualizado;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\DB;

class ActualizarEmpleado
{
    public function __invoke($root, array $args)
    {
        Log::info('ActualizarEmpleado resolver ejecutándose', $args);
        
        return DB::transaction(function () use ($args) {
            $empleado = Empleado::where('id_empleado', $args['id_empleado'])->firstOrFail();
            $empleado->update($args['input']);
            
            Log::info('Empleado actualizado, disparando evento');
            
            // Crear y disparar el evento
            $event = new EmpleadoActualizado($empleado);
            Event::dispatch($event);
            
            Log::info('Evento EmpleadoActualizado disparado');
            
            return $empleado;
        });
    }
}
