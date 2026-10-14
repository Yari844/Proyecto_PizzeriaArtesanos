<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Tamano>
 */
class TamanoFactory extends Factory
{
    /**
     * Definir el estado por defecto del modelo.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'nombre' => fake()->unique()->randomElement(['Chica', 'Mediana', 'Grande', 'Familiar']),
            'precio_orilla_queso' => fake()->randomElement([40, 50, 60, 70]),
            'orden' => fake()->numberBetween(1, 4),
        ];
    }
}