<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StorePromocionRequest extends FormRequest
{
    /**
     * Determinar si el usuario puede hacer esta petición.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Reglas de validación para crear una promoción.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'titulo' => ['required', 'string', 'max:120'],
            'descripcion' => ['nullable', 'string'],
            'imagen' => ['nullable', 'string', 'max:255'],
            'tipo' => ['sometimes', 'in:banner,oferta_del_dia'],
            'fecha_inicio' => ['nullable', 'date'],
            'fecha_fin' => [
                'nullable', 'date',
                Rule::when($this->filled('fecha_inicio'), ['after_or_equal:fecha_inicio']),
            ],
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
            'titulo.required' => 'El título de la promoción es obligatorio.',
            'titulo.max' => 'El título no puede tener más de 120 caracteres.',
            'tipo.in' => 'El tipo debe ser "banner" o "oferta_del_dia".',
            'fecha_inicio.date' => 'La fecha de inicio no es una fecha válida.',
            'fecha_fin.date' => 'La fecha de fin no es una fecha válida.',
            'fecha_fin.after_or_equal' => 'La fecha de fin no puede ser anterior a la fecha de inicio.',
            'activo.boolean' => 'El campo activo debe ser verdadero o falso.',
        ];
    }
}