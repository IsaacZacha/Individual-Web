<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class SucursalFactory extends Factory
{
    public function definition(): array
    {
        return [
            'nombre' => $this->faker->company,
            'direccion' => $this->faker->address,
            'ciudad' => $this->faker->city,
            'telefono' => $this->faker->phoneNumber,
        ];
    }
}