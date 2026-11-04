<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrdenDetalleResource extends JsonResource
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
            'orden_id' => $this->orden_id,
            'producto_id' => $this->producto_id,
            'producto' => $this->producto_nombre,
            'tamano' => $this->tamano_nombre,
            'orilla_queso' => (bool) $this->orilla_queso,
            'precio_unitario' => (float) $this->precio_unitario,
            'precio_orilla' => (float) $this->precio_orilla,
            'cantidad' => $this->cantidad,
            'importe' => (float) $this->importe,
            'notas' => $this->notas,
        ];
    }
}