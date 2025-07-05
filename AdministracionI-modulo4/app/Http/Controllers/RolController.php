<?php

namespace App\Http\Controllers;

use App\Models\Rol;
use App\Http\Requests\StoreRolRequest;
use App\Http\Requests\UpdateRolRequest;

class RolController extends Controller
{
    /**
     * Listar todos los roles.
     *
     * @group Roles
     * @authenticated
     * @response 200 scenario="Listado de roles" [
     *   {
     *     "id_rol": 1,
     *     "nombre": "Administrador",
     *     "created_at": "2025-07-06T16:40:06.000000Z",
     *     "updated_at": "2025-07-06T16:40:06.000000Z"
     *   }
     * ]
     */
    public function index()
    {
        return response()->json(Rol::all());
    }

    /**
     * Crear un nuevo rol.
     *
     * @group Roles
     * @authenticated
     * @bodyParam nombre string required Nombre del rol. Example: Administrador
     * @response 201 scenario="Rol creado" {
     *   "id_rol": 2,
     *   "nombre": "Supervisor",
     *   "created_at": "2025-07-06T16:40:06.000000Z",
     *   "updated_at": "2025-07-06T16:40:06.000000Z"
     * }
     * @response 422 scenario="Campos inválidos o faltantes" {
     *   "message": "Campos inválidos o faltantes",
     *   "errors": {
     *     "nombre": {"El campo nombre es obligatorio."}
     *   }
     * }
     */
    public function store(StoreRolRequest $request)
    {
        $rol = Rol::create($request->validated());
        return response()->json($rol, 201);
    }

    /**
     * Mostrar un rol específico.
     *
     * @group Roles
     * @authenticated
     * @urlParam id_rol int required El ID del rol.
     * @response 200 scenario="Rol encontrado" {
     *   "id_rol": 1,
     *   "nombre": "Administrador",
     *   "created_at": "2025-07-06T16:40:06.000000Z",
     *   "updated_at": "2025-07-06T16:40:06.000000Z"
     * }
     * @response 404 scenario="Rol no encontrado" {
     *   "message": "Rol no encontrado"
     * }
     */
    public function show($id_rol)
    {
        try {
            $rol = Rol::findOrFail($id_rol);
            return response()->json($rol);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Rol no encontrado'
            ], 404);
        }
    }

    /**
     * Actualizar un rol.
     *
     * @group Roles
     * @authenticated
     * @urlParam id_rol int required El ID del rol.
     * @bodyParam nombre string Nombre del rol. Example: Administrador
     * @response 200 scenario="Rol actualizado" {
     *   "id_rol": 1,
     *   "nombre": "Supervisor",
     *   "created_at": "2025-07-06T16:40:06.000000Z",
     *   "updated_at": "2025-07-06T16:40:06.000000Z"
     * }
     * @response 422 scenario="Campos inválidos o faltantes" {
     *   "message": "Campos inválidos o faltantes",
     *   "errors": {
     *     "nombre": {"El campo nombre es obligatorio."}
     *   }
     * }
     */
    public function update(UpdateRolRequest $request, $id_rol)
    {
        $rol = Rol::findOrFail($id_rol);
        $rol->update($request->validated());
        return response()->json([
            'message' => 'Rol actualizado',
            'rol' => $rol
        ], 200);
    }

    /**
     * Eliminar un rol.
     *
     * @group Roles
     * @authenticated
     * @urlParam id_rol int required El ID del rol.
     * @response 204 scenario="Rol eliminado" {}
     * @response 404 scenario="Rol no encontrado" {
     *   "message": "Rol no encontrado"
     * }
     */
    public function destroy($id_rol)
    {
        try {
            $rol = Rol::where('id_rol', $id_rol)->first();
            
            if (!$rol) {
                return response()->json([
                    'message' => 'Rol no encontrado'
                ], 404);
            }
            
            $rol->delete();
            return response()->json([
                'message' => 'Rol eliminado'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al eliminar el rol: ' . $e->getMessage()
            ], 500);
        }
    }
}