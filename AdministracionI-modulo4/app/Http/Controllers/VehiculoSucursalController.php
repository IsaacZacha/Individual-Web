<?php

namespace App\Http\Controllers;

use App\Models\VehiculoSucursal;
use App\Models\Vehiculo;
use App\Models\Sucursal;
use App\Http\Requests\StoreVehiculoSucursalRequest;
use App\Http\Requests\UpdateVehiculoSucursalRequest;
use App\Services\NotificationService;

class VehiculoSucursalController extends Controller
{
    private NotificationService $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }
    /**
     * Listar todas las relaciones vehículo-sucursal.
     *
     * @group VehiculoSucursal
     * @authenticated
     * @response 200 scenario="Listado de relaciones" [
     *   {
     *     "id": 1,
     *     "vehiculo_id": 1,
     *     "sucursal_id": 2,
     *     "fecha_ingreso": "2024-07-01"
     *   }
     * ]
     */
    public function index()
    {
        return response()->json(VehiculoSucursal::all());
    }

    /**
     * Crear una nueva relación vehículo-sucursal.
     *
     * @group VehiculoSucursal
     * @authenticated
     * @bodyParam vehiculo_id int required ID del vehículo. Example: 1
     * @bodyParam sucursal_id int required ID de la sucursal. Example: 2
     * @bodyParam fecha_ingreso date required Fecha de ingreso. Example: 2024-07-01
     * @response 201 scenario="Relación creada" {
     *   "id": 2,
     *   "vehiculo_id": 1,
     *   "sucursal_id": 2,
     *   "fecha_ingreso": "2024-07-01"
     * }
     * @response 422 scenario="Campos inválidos o faltantes" {
     *   "message": "Campos inválidos o faltantes",
     *   "errors": {
     *     "vehiculo_id": {"El campo vehiculo_id es obligatorio."}
     *   }
     * }
     */
    public function store(StoreVehiculoSucursalRequest $request)
    {
        // Mapear los campos del request a los nombres esperados por el modelo
        $validatedData = $request->validated();
        $modelData = [
            'id_vehiculo' => $validatedData['vehiculo_id'],
            'id_sucursal' => $validatedData['sucursal_id'],
            'fecha_asignacion' => $validatedData['fecha_asignacion'] ?? now()
        ];
        
        $relacion = VehiculoSucursal::create($modelData);
        
        // Obtener datos del vehículo y sucursal para la notificación
        $vehiculo = Vehiculo::find($relacion->id_vehiculo);
        $sucursal = Sucursal::find($relacion->id_sucursal);
        
        // Enviar notificación
        $this->notificationService->vehiculoAsignado(
            $relacion->toArray(),
            $vehiculo ? $vehiculo->toArray() : [],
            $sucursal ? $sucursal->toArray() : []
        );
        
        return response()->json($relacion, 201);
    }

    /**
     * Mostrar una relación vehículo-sucursal específica.
     *
     * @group VehiculoSucursal
     * @authenticated
     * @urlParam id int required El ID de la relación.
     * @response 200 scenario="Relación encontrada" {
     *   "id": 1,
     *   "vehiculo_id": 1,
     *   "sucursal_id": 2,
     *   "fecha_ingreso": "2024-07-01"
     * }
     * @response 404 scenario="Relación no encontrada" {
     *   "message": "Relación no encontrada"
     * }
     */

    public function show($id)
    {
        try {
            $vehiculoSucursal = VehiculoSucursal::findOrFail($id);
            return response()->json($vehiculoSucursal);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Vehiculo con Sucursal no encontrado'
            ], 404);
        }
    }

    /**
     * Actualizar una relación vehículo-sucursal.
     *
     * @group VehiculoSucursal
     * @authenticated
     * @urlParam id int required El ID de la relación.
     * @bodyParam vehiculo_id int ID del vehículo. Example: 1
     * @bodyParam sucursal_id int ID de la sucursal. Example: 2
     * @bodyParam fecha_ingreso date Fecha de ingreso. Example: 2024-07-01
     * @response 200 scenario="Relación actualizada" {
     *   "id": 1,
     *   "vehiculo_id": 1,
     *   "sucursal_id": 2,
     *   "fecha_ingreso": "2024-07-01"
     * }
     * @response 422 scenario="Campos inválidos o faltantes" {
     *   "message": "Campos inválidos o faltantes",
     *   "errors": {
     *     "vehiculo_id": {"El campo vehiculo_id es obligatorio."}
     *   }
     * }
     */
    public function update(UpdateVehiculoSucursalRequest $request, VehiculoSucursal $vehiculoSucursal)
    {
        // Mapear los campos del request a los nombres esperados por el modelo
        $validatedData = $request->validated();
        $modelData = [];
        
        if (isset($validatedData['vehiculo_id'])) {
            $modelData['id_vehiculo'] = $validatedData['vehiculo_id'];
        }
        
        if (isset($validatedData['sucursal_id'])) {
            $modelData['id_sucursal'] = $validatedData['sucursal_id'];
        }
        
        if (isset($validatedData['fecha_asignacion'])) {
            $modelData['fecha_asignacion'] = $validatedData['fecha_asignacion'];
        }
        
        $vehiculoSucursal->update($modelData);
        return response()->json([
            'message' => 'Vehiculo con Sucursal actualizado',
            'vehiculo_sucursal' => $vehiculoSucursal
        ], 200);
    }

    /**
     * Eliminar una relación vehículo-sucursal.
     *
     * @group VehiculoSucursal
     * @authenticated
     * @urlParam id int required El ID de la relación.
     * @response 204 scenario="Relación eliminada" {}
     * @response 404 scenario="Relación no encontrada" {
     *   "message": "Relación no encontrada"
     * }
     */
    public function destroy($id)
    {
        try {
            $vehiculoSucursal = VehiculoSucursal::where('id', $id)->first();
            
            if (!$vehiculoSucursal) {
                return response()->json([
                    'message' => 'Relación no encontrada'
                ], 404);
            }
            
            $asignacionId = $vehiculoSucursal->id;
            
            // Obtener datos del vehículo y sucursal para la notificación ANTES de eliminar
            $vehiculo = Vehiculo::where('id_vehiculo', $vehiculoSucursal->id_vehiculo)->first();
            $sucursal = Sucursal::where('id_sucursal', $vehiculoSucursal->id_sucursal)->first();
            
            $vehiculoSucursal->delete();
            
            // Enviar notificación
            $this->notificationService->vehiculoDesasignado(
                $asignacionId,
                $vehiculo ? $vehiculo->toArray() : [],
                $sucursal ? $sucursal->toArray() : []
            );
            
            return response()->json([
                'message' => 'Vehiculo con Sucursal eliminado'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al eliminar: ' . $e->getMessage()
            ], 500);
        }
    }
}