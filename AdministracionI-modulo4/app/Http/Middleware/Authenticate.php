<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;

class Authenticate extends Middleware
{
    protected function redirectTo($request)
    {
        // Siempre responde con JSON 401 si no está autenticado
        abort(response()->json(['message' => 'No autenticado.'], 401));
    }
}