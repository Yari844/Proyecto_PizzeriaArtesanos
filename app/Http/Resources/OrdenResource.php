<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrdenResource extends JsonResource
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
            'folio' => $this->folio,
            'tipo_servicio' => $this->tipo_servicio,
            'estado' => $this->estado,
            'cliente_nombre' => $this->cliente_nombre,
            'cliente_telefono' => $this->cliente_telefono,
            'cajero' => $this->whenLoaded('user', fn () => $this->user?->name),
            'total' => (float) $this->total,
            'metodo_pago' => $this->metodo_pago,
            'monto_recibido' => $this->monto_recibido !== null ? (float) $this->monto_recibido : null,
            'cambio' => (float) $this->cambio,
            'pagada_at' => $this->pagada_at?->toIso8601String(),
            'creada' => $this->created_at?->toDateTimeString(),
            'detalles' => OrdenDetalleResource::collection($this->whenLoaded('detalles')),
        ];
    }
}