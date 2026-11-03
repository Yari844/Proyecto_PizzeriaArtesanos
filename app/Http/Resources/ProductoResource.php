<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductoResource extends JsonResource
{
    /**
     * Transformar el recurso en un arreglo.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'tipo' => $this->tipo,
            'nombre' => $this->nombre,
            'descripcion' => $this->descripcion,
            'presentacion' => $this->presentacion,
            'imagen' => $this->imagen,
            'activo' => $this->activo,
            'precios' => $this->whenLoaded('precios', fn () => $this->precios->map(fn ($precio) => [
                'tamano' => $precio->tamano?->nombre,
                'precio' => (float) $precio->precio,
            ])),
        ];
    }
}