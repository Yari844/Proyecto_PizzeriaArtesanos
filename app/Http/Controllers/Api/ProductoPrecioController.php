<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProductoPrecioRequest;
use App\Http\Requests\UpdateProductoPrecioRequest;
use App\Http\Resources\ProductoPrecioCollection;
use App\Http\Resources\ProductoPrecioResource;
use App\Models\ProductoPrecio;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProductoPrecioController extends Controller
{
    /**
     * Listar los precios. Se puede filtrar con ?producto_id=1
     */
    public function index(Request $request): ProductoPrecioCollection
    {
        $precios = ProductoPrecio::with(['producto', 'tamano'])
            ->when(
                $request->query('producto_id'),
                fn ($consulta, $productoId) => $consulta->where('producto_id', $productoId)
            )
            ->get();

        return new ProductoPrecioCollection($precios);
    }

    /**
     * Crear un precio.
     */
    public function store(StoreProductoPrecioRequest $request): JsonResponse
    {
        $precio = ProductoPrecio::create($request->validated());

        return response()->json([
            'mensaje' => 'Precio creado correctamente',
            'data' => new ProductoPrecioResource($precio->load(['producto', 'tamano'])),
        ], 201);
    }

    /**
     * Mostrar un precio.
     */
    public function show(ProductoPrecio $productoPrecio): ProductoPrecioResource
    {
        return new ProductoPrecioResource($productoPrecio->load(['producto', 'tamano']));
    }

    /**
     * Actualizar el monto de un precio.
     */
    public function update(UpdateProductoPrecioRequest $request, ProductoPrecio $productoPrecio): JsonResponse
    {
        $productoPrecio->update($request->validated());

        return response()->json([
            'mensaje' => 'Precio actualizado correctamente',
            'data' => new ProductoPrecioResource($productoPrecio->load(['producto', 'tamano'])),
        ]);
    }

    /**
     * Eliminar un precio.
     */
    public function destroy(ProductoPrecio $productoPrecio): JsonResponse
    {
        $productoPrecio->delete();

        return response()->json([
            'mensaje' => 'Precio eliminado correctamente',
        ]);
    }
}