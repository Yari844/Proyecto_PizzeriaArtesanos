<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProductoRequest;
use App\Http\Requests\UpdateProductoRequest;
use App\Http\Resources\ProductoCollection;
use App\Http\Resources\ProductoResource;
use App\Models\Producto;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;

class ProductoController extends Controller
{
    /**
     * Listar todos los productos con sus precios.
     */
    public function index(): ProductoCollection
    {
        return new ProductoCollection(Producto::with('precios.tamano')->get());
    }

    /**
     * Crear un producto.
     */
    public function store(StoreProductoRequest $request): JsonResponse
    {
        $producto = Producto::create($request->validated());

        return response()->json([
            'mensaje' => 'Producto creado correctamente',
            'data' => new ProductoResource($producto),
        ], 201);
    }

    /**
     * Mostrar un producto.
     */
    public function show(Producto $producto): ProductoResource
    {
        return new ProductoResource($producto->load('precios.tamano'));
    }

    /**
     * Actualizar un producto.
     */
    public function update(UpdateProductoRequest $request, Producto $producto): JsonResponse
    {
        $producto->update($request->validated());

        return response()->json([
            'mensaje' => 'Producto actualizado correctamente',
            'data' => new ProductoResource($producto),
        ]);
    }

    /**
     * Eliminar un producto.
     */
    public function destroy(Producto $producto): JsonResponse
    {
        try {
            $producto->delete();
        } catch (QueryException $e) {
            return response()->json([
                'mensaje' => 'No se puede eliminar el producto porque tiene ventas registradas. Desactívalo en su lugar.',
            ], 409);
        }

        return response()->json([
            'mensaje' => 'Producto eliminado correctamente',
        ]);
    }
}