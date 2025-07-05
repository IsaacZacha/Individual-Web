<?php

namespace App\GraphQL\Queries;

use App\Models\Rol as RolModel;

class Rol
{
    public function __invoke($root, array $args)
    {
        return RolModel::where('id_rol', $args['id_rol'])->first();
    }
}
