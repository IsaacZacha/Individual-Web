<?php

namespace App\GraphQL\Queries;

use App\Models\Empleado;
use App\Models\Vehiculo;
use App\Models\Sucursal;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class EstadisticasQuery
{
    /**
     * Resolver para estadísticas generales del sistema
     */
    public function resolve($rootValue, array $args, $context, $resolveInfo)
    {
        return [
            'totalEmpleados' => Empleado::count(),
            'totalVehiculos' => Vehiculo::count(),
            'totalSucursales' => Sucursal::count(),
            'totalUsuarios' => User::count(),
            'vehiculosPorEstado' => $this->getVehiculosPorEstado()
        ];
    }

    /**
     * Obtiene la distribución de vehículos por estado
     */
    private function getVehiculosPorEstado()
    {
        return Vehiculo::select('estado', DB::raw('count(*) as count'))
            ->groupBy('estado')
            ->get()
            ->map(function ($item) {
                return [
                    'estado' => $item->estado,
                    'count' => $item->count
                ];
            })
            ->toArray();
    }
}
