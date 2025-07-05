<?php declare(strict_types=1);

namespace App\GraphQL\Queries;

use App\Models\Empleado as EmpleadoModel;

final readonly class Empleados
{
    /** @param  array{}  $args */
    public function __invoke(null $_, array $args)
    {
        return EmpleadoModel::all();
    }
}
