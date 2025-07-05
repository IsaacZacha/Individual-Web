<?php

namespace App\GraphQL\Queries;

use App\Models\User as UserModel;

class Users
{
    public function __invoke($root, array $args)
    {
        return UserModel::all();
    }
}
