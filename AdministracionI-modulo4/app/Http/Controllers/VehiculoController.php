<?php

namespace App\Http\Controllers;

use App\Models\Vehiculo;
use App\Http\Requests\StoreVehiculoRequest;
use App\Http\Requests\UpdateVehiculoRequest;
use App\Services\NotificationService;

class VehiculoController extends Controller
{
    private NotificationService $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }
    /**
     * Listar todos los vehículos.
     *
     * @group Vehículos
     * @authenticated
     * @response 200 scenario="Listado de vehículos" [
     *   {
     *     "id_vehiculo": 1,
     *     "placa": "ABC123",
     *     "marca": "Toyota",
     *     "modelo": "Corolla",
     *     "anio": 2022,
     *     "tipo_id": "Sedan",
     *     "estado": "Disponible"
     *   }
     * ]
     */
    public function index()
    {
        return response()->json(Vehiculo::all());
    }

    /**
     * Crear un nuevo vehículo.
     *
     * @group Vehículos
     * @authenticated
     * @bodyParam placa string required Placa del vehículo. Example: ABC123
     * @bodyParam marca string required Marca del vehículo. Example: Toyota
     * @bodyParam modelo string required Modelo del vehículo. Example: Corolla
     * @bodyParam anio integer required Año del vehículo. Example: 2022
     * @bodyParam tipo_id string required Tipo del vehículo. Example: Sedan
     * @bodyParam estado string required Estado del vehículo. Example: Disponible
     * @response 201 scenario="Vehículo creado" {
     *   "id_vehiculo": 2,
     *   "placa": "DEF456",
     *   "marca": "Nissan",
     *   "modelo": "Sentra",
     *   "anio": 2023,
     *   "tipo_id": "Sedan",
     *   "estado": "Disponible"
     * }
     * @response 422 scenario="Campos inválidos o faltantes" {
     *   "message": "Campos inválidos o faltantes",
     *   "errors": {
     *     "placa": {"El campo placa es obligatorio."}
     *   }
     * }
     */
    public function store(StoreVehiculoRequest $request)
    {
        $vehiculo = Vehiculo::create($request->validated());
        
        // Enviar notificación
        $this->notificationService->vehiculoCreado($vehiculo->toArray());
        
        return response()->json($vehiculo, 201);
    }

    /**
     * Mostrar un vehículo específico.
     *
     * @group Vehículos
     * @authenticated
     * @urlParam id_vehiculo int required El ID del vehículo.
     * @response 200 scenario="Vehículo encontrado" {
     *   "id_vehiculo": 1,
     *   "placa": "ABC123",
     *   "marca": "Toyota",
     *   "modelo": "Corolla",
     *   "anio": 2022,
     *   "tipo_id": "Sedan",
     *   "estado": "Disponible"
     * }
     * @response 404 scenario="Vehículo no encontrado" {
     *   "message": "Vehículo no encontrado"
     * }
     */

    public function show($id)
    {
        try {
            $vehiculo = Vehiculo::findOrFail($id);
            return response()->json($vehiculo);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Vehículo no encontrado'
            ], 404);
        }
    }

    /**
     * Actualizar un vehículo.
     *
     * @group Vehículos
     * @authenticated
     * @urlParam id_vehiculo int required El ID del vehículo.
     * @bodyParam placa string Placa del vehículo. Example: ABC123
     * @bodyParam marca string Marca del vehículo. Example: Toyota
     * @bodyParam modelo string Modelo del vehículo. Example: Corolla
     * @bodyParam anio integer Año del vehículo. Example: 2022
     * @bodyParam tipo_id string Tipo del vehículo. Example: Sedan
     * @bodyParam estado string Estado del vehículo. Example: Disponible
     * @response 200 scenario="Vehículo actualizado" {
     *   "id_vehiculo": 1,
     *   "placa": "ABC123",
     *   "marca": "Toyota",
     *   "modelo": "Corolla",
     *   "anio": 2022,
     *   "tipo_id": "Sedan",
     *   "estado": "Disponible"
     * }
     * @response 422 scenario="Campos inválidos o faltantes" {
     *   "message": "Campos inválidos o faltantes",
     *   "errors": {
     *     "placa": {"El campo placa es obligatorio."}
     *   }
     * }
     */
    public function update(UpdateVehiculoRequest $request, Vehiculo $vehiculo)
    {
        $datosOriginales = $vehiculo->toArray();
        $estadoAnterior = $vehiculo->estado;
        
        $vehiculo->update($request->validated());
        
        // Calcular cambios
        $cambios = array_diff_assoc($request->validated(), $datosOriginales);
        
        // Si cambió el estado, enviar notificación específica
        if (isset($cambios['estado'])) {
            $this->notificationService->vehiculoEstadoCambiado(
                $vehiculo->toArray(), 
                $estadoAnterior, 
                $vehiculo->estado
            );
        } else {
            // Enviar notificación general de actualización
            $this->notificationService->vehiculoActualizado($vehiculo->toArray(), $cambios);
        }
        
        return response()->json($vehiculo);
    }

    /**
     * Eliminar un vehículo.
     *
     * @group Vehículos
     * @authenticated
     * @urlParam id_vehiculo int required El ID del vehículo.
     * @response 204 scenario="Vehículo eliminado" {}
     * @response 404 scenario="Vehículo no encontrado" {
     *   "message": "Vehículo no encontrado"
     * }
     */
    public function destroy(Vehiculo $vehiculo)
    {
        $vehiculoId = $vehiculo->id_vehiculo;
        $vehiculoPlaca = $vehiculo->placa;
        
        $vehiculo->delete();
        
        // Enviar notificación
        $this->notificationService->vehiculoEliminado($vehiculoId, $vehiculoPlaca);
        
        return response()->json(null, 204);
    }
}