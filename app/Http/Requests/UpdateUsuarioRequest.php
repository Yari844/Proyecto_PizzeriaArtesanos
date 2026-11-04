<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateUsuarioRequest extends FormRequest
{
    /**
     * Determinar si el usuario puede hacer esta petición.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Reglas de validación para actualizar un usuario.
     * La contraseña es opcional: si no se envía, no cambia.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'name' => ['sometimes', 'required', 'string', 'max:255'],
            'email' => [
                'sometimes', 'required', 'email', 'max:255',
                Rule::unique('users', 'email')->ignore($this->route('usuario')),
            ],
            'password' => ['sometimes', 'nullable', 'string', 'min:8'],
            'rol' => ['sometimes', 'required', 'in:administrador,empleado'],
            'telefono' => ['sometimes', 'nullable', 'string', 'max:20'],
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
            'name.required' => 'El nombre no puede quedar vacío.',
            'email.required' => 'El correo no puede quedar vacío.',
            'email.email' => 'El correo no tiene un formato válido.',
            'email.unique' => 'Ya existe un usuario con ese correo.',
            'password.min' => 'La contraseña debe tener al menos 8 caracteres.',
            'rol.in' => 'El rol debe ser "administrador" o "empleado".',
            'telefono.max' => 'El teléfono no puede tener más de 20 caracteres.',
            'activo.boolean' => 'El campo activo debe ser verdadero o falso.',
        ];
    }
}