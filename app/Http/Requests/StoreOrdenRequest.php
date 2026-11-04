<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreOrdenRequest extends FormRequest
{
    /**
     * Determinar si el usuario puede hacer esta petición.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Reglas de validación para crear una orden con sus renglones.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'user_id' => ['required', 'exists:users,id'],
            'tipo_servicio' => ['required', 'in:comer_aqui,para_llevar,telefono'],
            'cliente_nombre' => ['nullable', 'string', 'max:100'],
            'cliente_telefono' => ['nullable', 'string', 'max:20'],
            'detalles' => ['required', 'array', 'min:1'],
            'detalles.*.producto_id' => ['required', 'exists:productos,id'],
            'detalles.*.tamano_id' => ['nullable', 'exists:tamanos,id'],
            'detalles.*.orilla_queso' => ['sometimes', 'boolean'],
            'detalles.*.cantidad' => ['required', 'integer', 'min:1', 'max:50'],
            'detalles.*.notas' => ['nullable', 'string', 'max:255'],
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
            'user_id.required' => 'El empleado que toma la orden es obligatorio.',
            'user_id.exists' => 'El empleado indicado no existe.',
            'tipo_servicio.required' => 'El tipo de servicio es obligatorio.',
            'tipo_servicio.in' => 'El tipo de servicio debe ser "comer_aqui", "para_llevar" o "telefono".',
            'cliente_nombre.max' => 'El nombre del cliente no puede tener más de 100 caracteres.',
            'cliente_telefono.max' => 'El teléfono no puede tener más de 20 caracteres.',
            'detalles.required' => 'La orden debe tener al menos un producto.',
            'detalles.min' => 'La orden debe tener al menos un producto.',
            'detalles.*.producto_id.required' => 'Cada renglón necesita un producto.',
            'detalles.*.producto_id.exists' => 'Uno de los productos indicados no existe.',
            'detalles.*.tamano_id.exists' => 'Uno de los tamaños indicados no existe.',
            'detalles.*.orilla_queso.boolean' => 'La orilla de queso debe ser verdadero o falso.',
            'detalles.*.cantidad.required' => 'Cada renglón necesita una cantidad.',
            'detalles.*.cantidad.integer' => 'La cantidad debe ser un número entero.',
            'detalles.*.cantidad.min' => 'La cantidad mínima es 1.',
            'detalles.*.cantidad.max' => 'La cantidad máxima por renglón es 50.',
        ];
    }
}