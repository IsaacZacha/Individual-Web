<?php

namespace App\GraphQL\Queries;

use App\Models\Rol as RolModel;

class Roles
{
    public function __invoke($root, array $args)
    {
        return RolModel::all();
    }
}
