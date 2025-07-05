<?php

namespace App\GraphQL\Queries;

use App\Models\Empleado;

class EmpleadosConUsuariosQuery
{
    /**
     * Resolver para obtener empleados con sus usuarios asociados
     */
    public function resolve($rootValue, array $args, $context, $resolveInfo)
    {
        return Empleado::with(['user.rol'])
            ->get();
    }
}
