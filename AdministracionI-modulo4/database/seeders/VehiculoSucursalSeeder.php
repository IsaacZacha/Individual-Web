<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Vehiculo;
use App\Models\Sucursal;
use App\Models\VehiculoSucursal;

class VehiculoSucursalSeeder extends Seeder
{
    public function run(): void
    {
        // Crear sucursales primero
        $sucursales = [
            [
                'nombre' => 'Sucursal Centro',
                'direccion' => 'Av. Principal 123',
                'ciudad' => 'Ciudad Central',
                'telefono' => '555-0001'
            ],
            [
                'nombre' => 'Sucursal Norte',
                'direccion' => 'Calle Norte 456',
                'ciudad' => 'Zona Norte',
                'telefono' => '555-0002'
            ],
            [
                'nombre' => 'Sucursal Sur',
                'direccion' => 'Av. Sur 789',
                'ciudad' => 'Zona Sur',
                'telefono' => '555-0003'
            ]
        ];

        foreach ($sucursales as $sucursalData) {
            Sucursal::create($sucursalData);
        }

        // Crear más vehículos si es necesario
        $vehiculos = [
            [
                'placa' => 'ABC-123',
                'marca' => 'Toyota',
                'modelo' => 'Corolla',
                'anio' => 2023,
                'tipo_id' => 'sedan',
                'estado' => 'activo'
            ],
            [
                'placa' => 'DEF-456',
                'marca' => 'Honda',
                'modelo' => 'Civic',
                'anio' => 2022,
                'tipo_id' => 'sedan',
                'estado' => 'activo'
            ],
            [
                'placa' => 'GHI-789',
                'marca' => 'Nissan',
                'modelo' => 'Sentra',
                'anio' => 2021,
                'tipo_id' => 'sedan',
                'estado' => 'mantenimiento'
            ],
            [
                'placa' => 'JKL-012',
                'marca' => 'Ford',
                'modelo' => 'Ranger',
                'anio' => 2024,
                'tipo_id' => 'pickup',
                'estado' => 'activo'
            ],
            [
                'placa' => 'MNO-345',
                'marca' => 'Chevrolet',
                'modelo' => 'Aveo',
                'anio' => 2020,
                'tipo_id' => 'hatchback',
                'estado' => 'activo'
            ]
        ];

        foreach ($vehiculos as $vehiculoData) {
            if (!Vehiculo::where('placa', $vehiculoData['placa'])->exists()) {
                Vehiculo::create($vehiculoData);
            }
        }

        // Crear asignaciones vehiculo-sucursal
        $vehiculosIds = Vehiculo::pluck('id_vehiculo')->toArray();
        $sucursalesIds = Sucursal::pluck('id_sucursal')->toArray();

        $asignaciones = [
            [
                'id_vehiculo' => $vehiculosIds[0] ?? 1,
                'id_sucursal' => $sucursalesIds[0] ?? 1,
                'fecha_asignacion' => now()->subDays(10),
            ],
            [
                'id_vehiculo' => $vehiculosIds[1] ?? 1,
                'id_sucursal' => $sucursalesIds[1] ?? 1,
                'fecha_asignacion' => now()->subDays(5),
            ],
            [
                'id_vehiculo' => $vehiculosIds[2] ?? 1,
                'id_sucursal' => $sucursalesIds[2] ?? 1,
                'fecha_asignacion' => now()->subDays(2),
            ],
            [
                'id_vehiculo' => $vehiculosIds[3] ?? 2,
                'id_sucursal' => $sucursalesIds[0] ?? 1,
                'fecha_asignacion' => now()->subDays(1),
            ],
            [
                'id_vehiculo' => $vehiculosIds[4] ?? 3,
                'id_sucursal' => $sucursalesIds[1] ?? 2,
                'fecha_asignacion' => now(),
            ]
        ];

        foreach ($asignaciones as $asignacionData) {
            VehiculoSucursal::updateOrCreate(
                [
                    'id_vehiculo' => $asignacionData['id_vehiculo'],
                    'id_sucursal' => $asignacionData['id_sucursal']
                ],
                $asignacionData
            );
        }

        $this->command->info('✅ Datos de prueba creados para VehiculoSucursal');
    }
}
