<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductoPrecioResource extends JsonResource
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
            'producto_id' => $this->producto_id,
            'producto' => $this->whenLoaded('producto', fn () => $this->producto?->nombre),
            'tamano_id' => $this->tamano_id,
            'tamano' => $this->whenLoaded('tamano', fn () => $this->tamano?->nombre),
            'precio' => (float) $this->precio,
        ];
    }
}