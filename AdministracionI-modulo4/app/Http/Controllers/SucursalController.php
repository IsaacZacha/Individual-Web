<?php

namespace App\Http\Controllers;

use App\Models\Sucursal;
use App\Http\Requests\StoreSucursalRequest;
use App\Http\Requests\UpdateSucursalRequest;

class SucursalController extends Controller
{
    /**
     * Listar todas las sucursales.
     *
     * @group Sucursales
     * @authenticated
     * @response 200 scenario="Listado de sucursales" [
     *   {
     *     "id_sucursal": 1,
     *     "nombre": "Sucursal Norte",
     *     "direccion": "Av. Principal 123",
     *     "ciudad": "Quito",
     *     "telefono": "022345678"
     *   }
     * ]
     */
    public function index()
    {
        return response()->json(Sucursal::all());
    }

    /**
     * Crear una nueva sucursal.
     *
     * @group Sucursales
     * @authenticated
     * @bodyParam nombre string required Nombre de la sucursal. Example: Sucursal Norte
     * @bodyParam direccion string required Dirección de la sucursal. Example: Av. Principal 123
     * @bodyParam ciudad string required Ciudad de la sucursal. Example: Quito
     * @bodyParam telefono string required Teléfono de la sucursal. Example: 022345678
     * @response 201 scenario="Sucursal creada" {
     *   "id_sucursal": 2,
     *   "nombre": "Sucursal Sur",
     *   "direccion": "Av. Secundaria 456",
     *   "ciudad": "Guayaquil",
     *   "telefono": "022345679"
     * }
     * @response 422 scenario="Campos inválidos o faltantes" {
     *   "message": "Campos inválidos o faltantes",
     *   "errors": {
     *     "nombre": {"El campo nombre es obligatorio."}
     *   }
     * }
     */
    public function store(StoreSucursalRequest $request)
    {
        $sucursal = Sucursal::create($request->validated());
        return response()->json($sucursal, 201);
    }

    /**
     * Mostrar una sucursal específica.
     *
     * @group Sucursales
     * @authenticated
     * @urlParam id_sucursal int required El ID de la sucursal.
     * @response 200 scenario="Sucursal encontrada" {
     *   "id_sucursal": 1,
     *   "nombre": "Sucursal Norte",
     *   "direccion": "Av. Principal 123",
     *   "ciudad": "Quito",
     *   "telefono": "022345678"
     * }
     * @response 404 scenario="Sucursal no encontrada" {
     *   "message": "Sucursal no encontrada"
     * }
     */
    public function show($id)
    {
        try {
            $sucursal = Sucursal::findOrFail($id);
            return response()->json($sucursal);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Sucursal no encontrada'
            ], 404);
        }
    }

    /**
     * Actualizar una sucursal.
     *
     * @group Sucursales
     * @authenticated
     * @urlParam id_sucursal int required El ID de la sucursal.
     * @bodyParam nombre string Nombre de la sucursal. Example: Sucursal Norte
     * @bodyParam direccion string Dirección de la sucursal. Example: Av. Principal 123
     * @bodyParam ciudad string Ciudad de la sucursal. Example: Quito
     * @bodyParam telefono string Teléfono de la sucursal. Example: 022345678
     * @response 200 scenario="Sucursal actualizada" {
     *   "id_sucursal": 1,
     *   "nombre": "Sucursal Norte",
     *   "direccion": "Av. Principal 123",
     *   "ciudad": "Quito",
     *   "telefono": "022345678"
     * }
     * @response 422 scenario="Campos inválidos o faltantes" {
     *   "message": "Campos inválidos o faltantes",
     *   "errors": {
     *     "nombre": {"El campo nombre es obligatorio."}
     *   }
     * }
     */
    public function update(UpdateSucursalRequest $request, $id)
    {
        $sucursal = Sucursal::findOrFail($id);
        $sucursal->update($request->validated());
        return response()->json([
            'message' => 'Sucursal actualizada',
            'sucursal' => $sucursal
        ], 200);
    }

    /**
     * Eliminar una sucursal.
     *
     * @group Sucursales
     * @authenticated
     * @urlParam id_sucursal int required El ID de la sucursal.
     * @response 204 scenario="Sucursal eliminada" {}
     * @response 404 scenario="Sucursal no encontrada" {
     *   "message": "Sucursal no encontrada"
     * }
     */
    public function destroy($id)
    {
        $sucursal = Sucursal::findOrFail($id);
        $sucursal->delete();
        return response()->json([
            'message' => 'Sucursal eliminada'
        ], 200);
    }
}