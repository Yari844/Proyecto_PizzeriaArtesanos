<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    /**
     * Contraseña actual que usa la fábrica.
     */
    protected static ?string $password;

    /**
     * Definir el estado por defecto del modelo.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => static::$password ??= Hash::make('password'),
            'rol' => 'empleado',
            'telefono' => fake()->numerify('##########'),
            'activo' => true,
            'remember_token' => Str::random(10),
        ];
    }

    /**
     * Usuario con rol de administrador.
     */
    public function administrador(): static
    {
        return $this->state(fn (array $attributes) => [
            'rol' => 'administrador',
        ]);
    }

    /**
     * Usuario desactivado.
     */
    public function inactivo(): static
    {
        return $this->state(fn (array $attributes) => [
            'activo' => false,
        ]);
    }

    /**
     * Indicar que el correo no está verificado.
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }
}