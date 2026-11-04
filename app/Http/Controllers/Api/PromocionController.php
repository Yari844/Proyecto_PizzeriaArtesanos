<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePromocionRequest;
use App\Http\Requests\UpdatePromocionRequest;
use App\Http\Resources\PromocionCollection;
use App\Http\Resources\PromocionResource;
use App\Models\Promocion;
use Illuminate\Http\JsonResponse;

class PromocionController extends Controller
{
    /**
     * Listar todas las promociones.
     */
    public function index(): PromocionCollection
    {
        return new PromocionCollection(Promocion::latest()->get());
    }

    /**
     * Crear una promoción.
     */
    public function store(StorePromocionRequest $request): JsonResponse
    {
        $promocion = Promocion::create($request->validated());

        return response()->json([
            'mensaje' => 'Promoción creada correctamente',
            'data' => new PromocionResource($promocion),
        ], 201);
    }

    /**
     * Mostrar una promoción.
     */
    public function show(Promocion $promocion): PromocionResource
    {
        return new PromocionResource($promocion);
    }

    /**
     * Actualizar una promoción.
     */
    public function update(UpdatePromocionRequest $request, Promocion $promocion): JsonResponse
    {
        $promocion->update($request->validated());

        return response()->json([
            'mensaje' => 'Promoción actualizada correctamente',
            'data' => new PromocionResource($promocion),
        ]);
    }

    /**
     * Eliminar una promoción.
     */
    public function destroy(Promocion $promocion): JsonResponse
    {
        $promocion->delete();

        return response()->json([
            'mensaje' => 'Promoción eliminada correctamente',
        ]);
    }
}