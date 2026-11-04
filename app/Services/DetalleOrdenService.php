<?php

namespace App\Services;

use App\Models\Orden;
use App\Models\Producto;
use App\Models\ProductoPrecio;
use App\Models\Tamano;
use Illuminate\Validation\ValidationException;

class DetalleOrdenService
{
    /**
     * Arma los datos de un renglón de la orden a partir del menú:
     * busca el precio real, valida y calcula el importe.
     *
     * @return array<string, mixed>
     */
    public function construir(
        int $productoId,
        ?int $tamanoId,
        bool $orillaQueso,
        int $cantidad,
        ?string $notas = null,
        string $campo = 'producto_id'
    ): array {
        $producto = Producto::findOrFail($productoId);

        if (! $producto->activo) {
            throw ValidationException::withMessages([
                $campo => 'El producto "' . $producto->nombre . '" no está activo.',
            ]);
        }

        $precio = ProductoPrecio::where('producto_id', $productoId)
            ->when(
                $tamanoId,
                fn ($consulta) => $consulta->where('tamano_id', $tamanoId),
                fn ($consulta) => $consulta->whereNull('tamano_id')
            )
            ->first();

        if (! $precio) {
            throw ValidationException::withMessages([
                $campo => 'El producto "' . $producto->nombre . '" no tiene precio para el tamaño indicado.',
            ]);
        }

        $tamano = $tamanoId ? Tamano::find($tamanoId) : null;
        $precioOrilla = 0;

        if ($orillaQueso) {
            if ($producto->tipo !== 'pizza' || ! $tamano) {
                throw ValidationException::withMessages([
                    $campo => 'La orilla de queso solo aplica a pizzas con tamaño.',
                ]);
            }
            $precioOrilla = $tamano->precio_orilla_queso;
        }

        return [
            'producto_id' => $producto->id,
            'tamano_id' => $tamanoId,
            'producto_nombre' => $producto->nombre,
            'tamano_nombre' => $tamano?->nombre,
            'orilla_queso' => $orillaQueso,
            'precio_unitario' => $precio->precio,
            'precio_orilla' => $precioOrilla,
            'cantidad' => $cantidad,
            'importe' => ($precio->precio + $precioOrilla) * $cantidad,
            'notas' => $notas,
        ];
    }

    /**
     * Recalcula el total de la orden sumando sus renglones.
     */
    public function recalcularTotal(Orden $orden): void
    {
        $orden->update(['total' => $orden->detalles()->sum('importe')]);
    }
}