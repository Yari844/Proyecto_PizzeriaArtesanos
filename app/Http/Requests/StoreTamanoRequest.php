<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreTamanoRequest extends FormRequest
{
    /**
     * Determinar si el usuario puede hacer esta petición.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Reglas de validación para crear un tamaño.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'nombre' => ['required', 'string', 'max:30', 'unique:tamanos,nombre'],
            'precio_orilla_queso' => ['required', 'numeric', 'min:0', 'max:9999'],
            'orden' => ['sometimes', 'integer', 'min:0', 'max:255'],
        ];
    }

    /**
     * Mensajes de error en español.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'nombre.required' => 'El nombre del tamaño es obligatorio.',
            'nombre.max' => 'El nombre no puede tener más de 30 caracteres.',
            'nombre.unique' => 'Ya existe un tamaño con ese nombre.',
            'precio_orilla_queso.required' => 'El precio de la orilla de queso es obligatorio.',
            'precio_orilla_queso.numeric' => 'El precio de la orilla de queso debe ser un número.',
            'precio_orilla_queso.min' => 'El precio de la orilla de queso no puede ser negativo.',
            'orden.integer' => 'El orden debe ser un número entero.',
        ];
    }
}