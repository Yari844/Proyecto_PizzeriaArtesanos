<?php

namespace Database\Factories;

use App\Models\Promocion;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Promocion>
 */
class PromocionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'titulo' => fake()->sentence(3),
            'descripcion' => fake()->sentence(),
            'imagen' => null,
            'tipo' => fake()->randomElement(['banner', 'oferta_del_dia']),
            'fecha_inicio' => now()->toDateString(),
            'fecha_fin' => now()->addDays(7)->toDateString(),
            'activo' => true,
        ];
    }
    //Promoción desactivada
    public function inactiva(): static
    {
        return $this->state(fn (array $attributes) => [
            'activo' => false,
        ]);
    }
}
