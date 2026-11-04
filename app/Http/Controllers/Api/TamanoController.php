<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTamanoRequest;
use App\Http\Requests\UpdateTamanoRequest;
use App\Http\Resources\TamanoCollection;
use App\Http\Resources\TamanoResource;
use App\Models\Tamano;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;

class TamanoController extends Controller
{
    /**
     * Listar todos los tamaños.
     */
    public function index(): TamanoCollection
    {
        return new TamanoCollection(Tamano::orderBy('orden')->get());
    }

    /**
     * Crear un tamaño.
     */
    public function store(StoreTamanoRequest $request): JsonResponse
    {
        $tamano = Tamano::create($request->validated());

        return response()->json([
            'mensaje' => 'Tamaño creado correctamente',
            'data' => new TamanoResource($tamano),
        ], 201);
    }

    /**
     * Mostrar un tamaño.
     */
    public function show(Tamano $tamano): TamanoResource
    {
        return new TamanoResource($tamano);
    }

    /**
     * Actualizar un tamaño.
     */
    public function update(UpdateTamanoRequest $request, Tamano $tamano): JsonResponse
    {
        $tamano->update($request->validated());

        return response()->json([
            'mensaje' => 'Tamaño actualizado correctamente',
            'data' => new TamanoResource($tamano),
        ]);
    }

    /**
     * Eliminar un tamaño.
     */
    public function destroy(Tamano $tamano): JsonResponse
    {
        try {
            $tamano->delete();
        } catch (QueryException $e) {
            return response()->json([
                'mensaje' => 'No se puede eliminar el tamaño porque tiene ventas registradas.',
            ], 409);
        }

        return response()->json([
            'mensaje' => 'Tamaño eliminado correctamente',
        ]);
    }
}