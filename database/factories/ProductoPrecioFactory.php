<?php

namespace Database\Factories;

use App\Models\Producto;
use App\Models\Tamano;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ProductoPrecio>
 */
class ProductoPrecioFactory extends Factory
{
    /**
     * Definir el estado por defecto del modelo.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'producto_id' => Producto::factory(),
            'tamano_id' => Tamano::factory(),
            'precio' => fake()->randomElement([120, 170, 220, 300]),
        ];
    }

    /**
     * Precio único sin tamaño (para los extras).
     */
    public function sinTamano(): static
    {
        return $this->state(fn (array $attributes) => [
            'tamano_id' => null,
            'precio' => fake()->randomElement([80, 100, 120]),
        ]);
    }
}