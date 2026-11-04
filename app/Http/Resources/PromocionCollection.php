<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class PromocionCollection extends ResourceCollection
{
    /**
     * Recurso que se usa para cada elemento de la colección.
     */
    public $collects = PromocionResource::class;

    /**
     * Transformar la colección en un arreglo.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'data' => $this->collection,
            'total' => $this->collection->count(),
        ];
    }
}