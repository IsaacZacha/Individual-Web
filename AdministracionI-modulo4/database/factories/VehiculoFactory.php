<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class VehiculoFactory extends Factory
{
    public function definition(): array
    {
        return [
            'placa' => strtoupper($this->faker->unique()->bothify('???###')),
            'marca' => $this->faker->company,
            'modelo' => $this->faker->word,
            'anio' => $this->faker->numberBetween(2010, 2024),
            'tipo_id' => 'Sedan', // ajusta según tu modelo
            'estado' => 'Disponible',
        ];
    }
}