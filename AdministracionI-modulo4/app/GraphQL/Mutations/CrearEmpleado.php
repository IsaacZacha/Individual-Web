<?php

namespace App\GraphQL\Mutations;

use App\Models\Empleado;
use App\Events\EmpleadoCreado;

class CrearEmpleado
{
    /**
     * Crear un nuevo empleado
     */
    public function __invoke($rootValue, array $args, $context)
    {
        $input = $args['input'];
        
        $empleado = Empleado::create([
            'nombre' => $input['nombre'],
            'cargo' => $input['cargo'],
            'correo' => $input['correo'],
            'telefono' => $input['telefono'] ?? null,
        ]);

        // Disparar evento para WebSocket
        event(new EmpleadoCreado($empleado));

        return $empleado;
    }
}
