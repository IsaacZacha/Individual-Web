<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    /**
     * Iniciar sesión y obtener token de acceso.
     *
     * @group Autenticación
     * @unauthenticated
     * @bodyParam username string required Nombre de usuario. Example: naomi
     * @bodyParam password string required Contraseña. Example: naomi12
     * @response 200 scenario="Login exitoso" {
     *   "access_token": "1|abc123...",
     *   "token_type": "Bearer"
     * }
     * @response 401 scenario="Credenciales incorrectas" {
     *   "message": "Credenciales incorrectas"
     * }
     * @response 422 scenario="Campos inválidos o faltantes" {
     *   "message": "Campos inválidos o faltantes",
     *   "errors": {
     *     "username": {"El campo username es obligatorio."}
     *   }
     * }
     */

    
    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        $user = User::where('username', $request->username)->first();

        if (!$user || !Hash::check($request->password, (string) $user->password)) {
            return response()->json(['message' => 'Credenciales incorrectas'], 401);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'access_token' => $token,
            'token_type' => 'Bearer'
        ]);
    }
}