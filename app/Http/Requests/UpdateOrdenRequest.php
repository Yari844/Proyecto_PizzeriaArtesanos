<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateOrdenRequest extends FormRequest
{
    /**
     * Determinar si el usuario puede hacer esta petición.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Reglas de validación para actualizar una orden (datos del cliente,
     * cancelarla o cobrarla). Los renglones se manejan en orden-detalles.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'tipo_servicio' => ['sometimes', 'required', 'in:comer_aqui,para_llevar,telefono'],
            'estado' => ['sometimes', 'required', 'in:pagada,cancelada'],
            'cliente_nombre' => ['sometimes', 'nullable', 'string', 'max:100'],
            'cliente_telefono' => ['sometimes', 'nullable', 'string', 'max:20'],
            'metodo_pago' => [
                Rule::requiredIf(fn () => $this->input('estado') === 'pagada'),
                'nullable',
                'in:efectivo,tarjeta,transferencia',
            ],
            'monto_recibido' => [
                Rule::requiredIf(fn () => $this->input('estado') === 'pagada'
                    && $this->input('metodo_pago') === 'efectivo'),
                'nullable',
                'numeric',
                'min:0',
            ],
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
            'tipo_servicio.in' => 'El tipo de servicio debe ser "comer_aqui", "para_llevar" o "telefono".',
            'estado.in' => 'El estado debe ser "pagada" o "cancelada".',
            'cliente_nombre.max' => 'El nombre del cliente no puede tener más de 100 caracteres.',
            'cliente_telefono.max' => 'El teléfono no puede tener más de 20 caracteres.',
            'metodo_pago.required' => 'El método de pago es obligatorio para cobrar la orden.',
            'metodo_pago.in' => 'El método de pago debe ser "efectivo", "tarjeta" o "transferencia".',
            'monto_recibido.required' => 'El monto recibido es obligatorio cuando se paga en efectivo.',
            'monto_recibido.numeric' => 'El monto recibido debe ser un número.',
            'monto_recibido.min' => 'El monto recibido no puede ser negativo.',
        ];
    }
}
