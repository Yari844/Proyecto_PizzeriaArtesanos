<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Producto>
 */
class ProductoFactory extends Factory
{
    /**
     * Definir el estado por defecto del modelo.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'tipo' => 'pizza',
            'nombre' => fake()->unique()->words(2, true),
            'descripcion' => fake()->sentence(),
            'presentacion' => null,
            'imagen' => null,
            'activo' => true,
        ];
    }

    /**
     * Producto de tipo pizza.
     */
    public function pizza(): static
    {
        return $this->state(fn (array $attributes) => [
            'tipo' => 'pizza',
            'presentacion' => null,
        ]);
    }

    /**
     * Producto de tipo extra (boneless, papas, alitas).
     */
    public function extra(): static
    {
        return $this->state(fn (array $attributes) => [
            'tipo' => 'extra',
            'presentacion' => fake()->randomElement(['200 gramos', '350 gramos', '8 piezas']),
        ]);
    }

    /**
     * Producto desactivado.
     */
    public function inactivo(): static
    {
        return $this->state(fn (array $attributes) => [
            'activo' => false,
        ]);
    }
}