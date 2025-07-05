<?php

namespace App\GraphQL\Mutations;

use App\Models\Empleado;
use App\Events\EmpleadoEliminado;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\DB;

class EliminarEmpleado
{
    public function __invoke($root, array $args)
    {
        Log::info('EliminarEmpleado resolver ejecutándose', $args);
        
        return DB::transaction(function () use ($args) {
            $empleado = Empleado::where('id_empleado', $args['id_empleado'])->firstOrFail();
            
            // Guardar datos del empleado antes de eliminar
            $empleadoData = $empleado->toArray();
            
            Log::info('Empleado encontrado, disparando evento');
            
            // Crear y disparar el evento antes de eliminar
            $event = new EmpleadoEliminado($empleado);
            Event::dispatch($event);
            
            // Eliminar empleado
            $empleado->delete();
            
            Log::info('Evento EmpleadoEliminado disparado');
            
            return [
                'success' => true,
                'message' => 'Empleado eliminado exitosamente',
                'deleted_id' => (string) $empleadoData['id_empleado']
            ];
        });
    }
}
