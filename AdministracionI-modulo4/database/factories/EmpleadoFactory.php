<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class EmpleadoFactory extends Factory
{
    public function definition(): array
    {
        return [
            'nombre' => $this->faker->name,
            'cargo' => $this->faker->jobTitle,
            'correo' => $this->faker->unique()->safeEmail,
            'telefono' => $this->faker->numerify('09########'),
        ];
    }
}