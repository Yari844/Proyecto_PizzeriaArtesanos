<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UsuarioResource extends JsonResource
{
    /**
     * Transformar el recurso en un arreglo.
     * La contraseña nunca se incluye en la respuesta.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'rol' => $this->rol,
            'telefono' => $this->telefono,
            'activo' => (bool) $this->activo,
            'creado' => $this->created_at?->toDateTimeString(),
        ];
    }
}