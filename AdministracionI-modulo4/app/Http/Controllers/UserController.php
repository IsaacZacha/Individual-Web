<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    /**
     * Listar todos los usuarios.
     *
     * @group Usuarios
     * @authenticated
     * @response 200 scenario="Listado de usuarios" [
     *   {
     *     "id_usuario": 1,
     *     "empleado_id": 1,
     *     "username": "usuario123",
     *     "rol_id": 2,
     *     "created_at": "2025-07-06T16:40:06.000000Z",
     *     "updated_at": "2025-07-06T16:40:06.000000Z"
     *   }
     * ]
     */
    public function index()
    {
        return response()->json(User::all());
    }

    /**
     * Crear un nuevo usuario.
     *
     * @group Usuarios
     * @authenticated
     * @bodyParam empleado_id int required ID del empleado. Example: 1
     * @bodyParam username string required Nombre de usuario. Example: usuario123
     * @bodyParam password string required Contraseña. Example: secreto123
     * @bodyParam rol_id int required ID del rol. Example: 2
     * @response 201 scenario="Usuario creado" {
     *   "id_usuario": 2,
     *   "empleado_id": 1,
     *   "username": "usuario123",
     *   "rol_id": 2,
     *   "created_at": "2025-07-06T16:40:06.000000Z",
     *   "updated_at": "2025-07-06T16:40:06.000000Z"
     * }
     * @response 422 scenario="Campos inválidos o faltantes" {
     *   "message": "Campos inválidos o faltantes",
     *   "errors": {
     *     "username": {"El campo username es obligatorio."}
     *   }
     * }
     */
    public function store(StoreUserRequest $request)
    {
        $data = $request->validated();
        $data['password'] = Hash::make($data['password']);
        $user = User::create($data);
        return response()->json($user, 201);
    }

    /**
     * Registrar un nuevo usuario.
     *
     * @group Registro
     * @unauthenticated
     * @bodyParam empleado_id int required ID del empleado. Example: 1
     * @bodyParam username string required Nombre de usuario. Example: usuario123
     * @bodyParam password string required Contraseña. Example: secreto123
     * @bodyParam rol_id int required ID del rol. Example: 2
     * @response 201 scenario="Usuario registrado" {
     *   "id_usuario": 2,
     *   "empleado_id": 1,
     *   "username": "usuario123",
     *   "rol_id": 2,
     *   "created_at": "2025-07-06T16:40:06.000000Z",
     *   "updated_at": "2025-07-06T16:40:06.000000Z"
     * }
     * @response 422 scenario="Campos inválidos o faltantes" {
     *   "message": "Campos inválidos o faltantes",
     *   "errors": {
     *     "username": {"El campo username es obligatorio."}
     *   }
     * }
     */
    public function register(StoreUserRequest $request)
    {
        $data = $request->validated();
        $data['password'] = Hash::make($data['password']);
        $user = User::create($data);
        return response()->json($user, 201);
    }

    /**
     * Mostrar un usuario específico.
     *
     * @group Usuarios
     * @authenticated
     * @urlParam id_usuario int required El ID del usuario.
     * @response 200 scenario="Usuario encontrado" {
     *   "id_usuario": 1,
     *   "empleado_id": 1,
     *   "username": "usuario123",
     *   "rol_id": 2,
     *   "created_at": "2025-07-06T16:40:06.000000Z",
     *   "updated_at": "2025-07-06T16:40:06.000000Z"
     * }
     * @response 404 scenario="Usuario no encontrado" {
     *   "message": "Usuario no encontrado"
     * }
     */
    public function show(User $user)
    {
        return response()->json($user);
    }

    /**
     * Actualizar un usuario.
     *
     * @group Usuarios
     * @authenticated
     * @urlParam id_usuario int required El ID del usuario.
     * @bodyParam empleado_id int ID del empleado. Example: 1
     * @bodyParam username string Nombre de usuario. Example: usuario123
     * @bodyParam password string Contraseña. Example: secreto123
     * @bodyParam rol_id int ID del rol. Example: 2
     * @response 200 scenario="Usuario actualizado" {
     *   "id_usuario": 1,
     *   "empleado_id": 1,
     *   "username": "usuario123",
     *   "rol_id": 2,
     *   "created_at": "2025-07-06T16:40:06.000000Z",
     *   "updated_at": "2025-07-06T16:40:06.000000Z"
     * }
     * @response 422 scenario="Campos inválidos o faltantes" {
     *   "message": "Campos inválidos o faltantes",
     *   "errors": {
     *     "username": {"El campo username es obligatorio."}
     *   }
     * }
     */
    public function update(UpdateUserRequest $request, User $user)
    {
        $data = $request->validated();
        if (isset($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        }
        $user->update($data);
        return response()->json($user);
    }

    /**
     * Eliminar un usuario.
     *
     * @group Usuarios
     * @authenticated
     * @urlParam id_usuario int required El ID del usuario.
     * @response 204 scenario="Usuario eliminado" {}
     * @response 404 scenario="Usuario no encontrado" {
     *   "message": "Usuario no encontrado"
     * }
     */
    public function destroy(User $user)
    {
        $user->delete();
        return response()->json(null, 204);
    }
}