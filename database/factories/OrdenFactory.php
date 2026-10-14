<?php

namespace Database\Factories;

use App\Models\Orden;
use App\Models\OrdenDetalle;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Orden>
 */
class OrdenFactory extends Factory
{
    /**
     * Definir el estado por defecto del modelo.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'folio' => fake()->unique()->numerify('ORD-######'),
            'user_id' => User::factory(),
            'tipo_servicio' => fake()->randomElement(['comer_aqui', 'para_llevar', 'telefono']),
            'estado' => 'abierta',
            'cliente_nombre' => fake()->name(),
            'cliente_telefono' => fake()->numerify('##########'),
            'total' => 0,
            'metodo_pago' => null,
            'monto_recibido' => null,
            'cambio' => 0,
            'pagada_at' => null,
        ];
    }

    /**
     * Orden ya cobrada (Cerrada/Pagada).
     */
    public function pagada(): static
    {
        return $this->state(fn (array $attributes) => [
            'estado' => 'pagada',
            'metodo_pago' => fake()->randomElement(['efectivo', 'tarjeta', 'transferencia']),
            'pagada_at' => now(),
        ]);
    }

    /**
     * Orden cancelada.
     */
    public function cancelada(): static
    {
        return $this->state(fn (array $attributes) => [
            'estado' => 'cancelada',
        ]);
    }

    /**
     * Crear la orden con renglones y calcular el total a partir de ellos.
     */
    public function conDetalles(int $cantidad = 2): static
    {
        return $this->afterCreating(function (Orden $orden) use ($cantidad) {
            OrdenDetalle::factory()->count($cantidad)->create(['orden_id' => $orden->id]);

            $total = $orden->detalles()->sum('importe');

            $orden->update([
                'total' => $total,
                'monto_recibido' => $orden->estado === 'pagada' ? $total : null,
            ]);
        });
    }
}