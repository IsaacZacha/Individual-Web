<?php
namespace Database\Factories;

use App\Models\Vehiculo;
use App\Models\Sucursal;
use Illuminate\Database\Eloquent\Factories\Factory;

class VehiculoSucursalFactory extends Factory
{
    public function definition(): array
    {
        return [
            'id_vehiculo' => Vehiculo::factory(),
            'id_sucursal' => Sucursal::factory(),
            'fecha_asignacion' => $this->faker->dateTimeBetween('-2 years', 'now'),
        ];
    }
}