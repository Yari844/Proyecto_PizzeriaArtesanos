<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProductoRequest extends FormRequest
{
    /**
     * Determinar si el usuario puede hacer esta petición.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Reglas de validación para actualizar un producto.
     * Con "sometimes" solo se validan los campos que lleguen en la petición.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'tipo' => ['sometimes', 'required', 'in:pizza,extra'],
            'nombre' => ['sometimes', 'required', 'string', 'max:100'],
            'descripcion' => ['sometimes', 'nullable', 'string'],
            'presentacion' => ['sometimes', 'nullable', 'string', 'max:50'],
            'imagen' => ['sometimes', 'nullable', 'string', 'max:255'],
            'activo' => ['sometimes', 'boolean'],
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
            'tipo.required' => 'El tipo de producto es obligatorio.',
            'tipo.in' => 'El tipo debe ser "pizza" o "extra".',
            'nombre.required' => 'El nombre del producto no puede quedar vacío.',
            'nombre.max' => 'El nombre no puede tener más de 100 caracteres.',
            'presentacion.max' => 'La presentación no puede tener más de 50 caracteres.',
            'activo.boolean' => 'El campo activo debe ser verdadero o falso.',
        ];
    }
}
