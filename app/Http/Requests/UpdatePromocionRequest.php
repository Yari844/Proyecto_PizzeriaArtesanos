<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdatePromocionRequest extends FormRequest
{
    /**
     * Determinar si el usuario puede hacer esta petición.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Reglas de validación para actualizar una promoción.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'titulo' => ['sometimes', 'required', 'string', 'max:120'],
            'descripcion' => ['sometimes', 'nullable', 'string'],
            'imagen' => ['sometimes', 'nullable', 'string', 'max:255'],
            'tipo' => ['sometimes', 'in:banner,oferta_del_dia'],
            'fecha_inicio' => ['sometimes', 'nullable', 'date'],
            'fecha_fin' => [
                'sometimes', 'nullable', 'date',
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
            'titulo.required' => 'El título de la promoción no puede quedar vacío.',
            'titulo.max' => 'El título no puede tener más de 120 caracteres.',
            'tipo.in' => 'El tipo debe ser "banner" o "oferta_del_dia".',
            'fecha_inicio.date' => 'La fecha de inicio no es una fecha válida.',
            'fecha_fin.date' => 'La fecha de fin no es una fecha válida.',
            'fecha_fin.after_or_equal' => 'La fecha de fin no puede ser anterior a la fecha de inicio.',
            'activo.boolean' => 'El campo activo debe ser verdadero o falso.',
        ];
    }
}