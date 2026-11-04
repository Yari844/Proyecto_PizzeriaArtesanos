<?php

namespace App\Http\Requests;

use App\Models\Producto;
use App\Models\ProductoPrecio;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class StoreProductoPrecioRequest extends FormRequest
{
    /**
     * Determinar si el usuario puede hacer esta petición.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Reglas de validación para crear un precio.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'producto_id' => ['required', 'exists:productos,id'],
            'tamano_id' => ['nullable', 'exists:tamanos,id'],
            'precio' => ['required', 'numeric', 'min:0', 'max:99999'],
        ];
    }

    /**
     * Validaciones adicionales: pizzas con tamaño, extras sin tamaño
     * y que no se repita la pareja producto-tamaño.
     */
    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            if ($validator->errors()->isNotEmpty()) {
                return;
            }

            $producto = Producto::find($this->producto_id);

            if ($producto->tipo === 'pizza' && ! $this->tamano_id) {
                $validator->errors()->add('tamano_id', 'Las pizzas necesitan un tamaño.');
                return;
            }

            if ($producto->tipo === 'extra' && $this->tamano_id) {
                $validator->errors()->add('tamano_id', 'Los extras tienen precio único y no llevan tamaño.');
                return;
            }

            $existe = ProductoPrecio::where('producto_id', $this->producto_id)
                ->when(
                    $this->tamano_id,
                    fn ($consulta) => $consulta->where('tamano_id', $this->tamano_id),
                    fn ($consulta) => $consulta->whereNull('tamano_id')
                )
                ->exists();

            if ($existe) {
                $validator->errors()->add('producto_id', 'Ese producto ya tiene un precio para ese tamaño.');
            }
        });
    }

    /**
     * Mensajes de error en español.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'producto_id.required' => 'El producto es obligatorio.',
            'producto_id.exists' => 'El producto indicado no existe.',
            'tamano_id.exists' => 'El tamaño indicado no existe.',
            'precio.required' => 'El precio es obligatorio.',
            'precio.numeric' => 'El precio debe ser un número.',
            'precio.min' => 'El precio no puede ser negativo.',
        ];
    }
}