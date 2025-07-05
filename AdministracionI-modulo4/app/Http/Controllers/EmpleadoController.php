<?php

namespace App\Http\Controllers;

use App\Models\Empleado;
use App\Http\Requests\StoreEmpleadoRequest;
use App\Http\Requests\UpdateEmpleadoRequest;
use App\Services\NotificationService;

class EmpleadoController extends Controller
{
    private NotificationService $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }
    /**
     * Obtener todos los empleados.
     *
     * @group Empleados
     * @authenticated
     * @response 200 scenario="Listado de empleados" [
     *   {
     *     "id_empleado": 1,
     *     "nombre": "Juan Pérez",
     *     "cargo": "Gerente",
     *     "correo": "juan@empresa.com",
     *     "telefono": "0999999999"
     *   }
     * ]
     */
    public function index()
    {
        return response()->json(Empleado::all());
    }

    /**
     * Crear un nuevo empleado.
     *
     * @group Empleados
     * @authenticated
     * @bodyParam nombre string required Nombre del empleado.
     * @bodyParam cargo string required Cargo del empleado.
     * @bodyParam correo string required Correo electrónico.
     * @bodyParam telefono string required Teléfono.
     * @response 201 scenario="Empleado creado" {
     *   "id_empleado": 2,
     *   "nombre": "Ana López",
     *   "cargo": "Asistente",
     *   "correo": "ana@empresa.com",
     *   "telefono": "0988888888"
     * }
     * @response 422 scenario="Campos inválidos o faltantes" {
     *   "message": "Campos inválidos o faltantes",
     *   "errors": {
     *     "nombre": {"El campo nombre es obligatorio."}
     *   }
     * }
     */
    public function store(StoreEmpleadoRequest $request)
    {
        $empleado = Empleado::create($request->validated());
        
        // Enviar notificación
        $this->notificationService->empleadoCreado($empleado->toArray());
        
        return response()->json($empleado, 201);
    }

    /**
     * Mostrar un empleado específico.
     *
     * @group Empleados
     * @authenticated
     * @urlParam id_empleado int required El ID del empleado.
     * @response 200 scenario="Empleado encontrado" {
     *   "id_empleado": 1,
     *   "nombre": "Juan Pérez",
     *   "cargo": "Gerente",
     *   "correo": "juan@empresa.com",
     *   "telefono": "0999999999"
     * }
     * @response 404 scenario="No encontrado" {
     *   "message": "Empleado no encontrado"
     * }
     */
    public function show($id)
    {
        try {
            $empleado = Empleado::findOrFail($id);
            return response()->json($empleado);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Empleado no encontrado'
            ], 404);
        }
    }

    /**
     * Actualizar un empleado.
     *
     * @group Empleados
     * @authenticated
     * @urlParam id_empleado int required El ID del empleado.
     * @bodyParam nombre string Nombre del empleado.
     * @bodyParam cargo string Cargo del empleado.
     * @bodyParam correo string Correo electrónico.
     * @bodyParam telefono string Teléfono.
     * @response 200 scenario="Empleado actualizado" {
     *   "id_empleado": 1,
     *   "nombre": "Juan Pérez",
     *   "cargo": "Director",
     *   "correo": "juan@empresa.com",
     *   "telefono": "0999999999"
     * }
     * @response 422 scenario="Campos inválidos o faltantes" {
     *   "message": "Campos inválidos o faltantes",
     *   "errors": {
     *     "nombre": {"El campo nombre es obligatorio."}
     *   }
     * }
     */
    public function update(UpdateEmpleadoRequest $request, Empleado $empleado)
    {
        $datosOriginales = $empleado->toArray();
        $empleado->update($request->validated());
        
        // Calcular cambios
        $cambios = array_diff_assoc($request->validated(), $datosOriginales);
        
        // Enviar notificación
        $this->notificationService->empleadoActualizado($empleado->toArray(), $cambios);
        
        return response()->json([
            'message' => 'Empleado actualizado',
            'empleado' => $empleado
        ], 200);
    }

    /**
     * Eliminar un empleado.
     *
     * @group Empleados
     * @authenticated
     * @urlParam id_empleado int required El ID del empleado.
     * @response 204 scenario="Empleado eliminado" {}
     * @response 404 scenario="No encontrado" {
     *   "message": "Empleado no encontrado"
     * }
     */
    public function destroy(Empleado $empleado)
    {
        $empleadoId = $empleado->id_empleado;
        $empleadoNombre = $empleado->nombre;
        
        $empleado->delete();
        
        // Enviar notificación
        $this->notificationService->empleadoEliminado($empleadoId, $empleadoNombre);
        
        return response()->json([
            'message' => 'Empleado eliminado'
        ], 200);
    }
}