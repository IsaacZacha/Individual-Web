<?php

namespace Database\Factories;

use App\Models\Rol;
use App\Models\Empleado;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;

class UserFactory extends Factory
{
    public function definition(): array
    {
        return [
            'empleado_id' => Empleado::factory(),
            'username' => $this->faker->unique()->userName(),
            'password' => Hash::make('password'),
            'rol_id' => Rol::factory(),
        ];
    }

    /**
     * Indicate that the user should have a specific username.
     */
    public function withUsername(string $username): static
    {
        return $this->state(fn (array $attributes) => [
            'username' => $username,
        ]);
    }
}