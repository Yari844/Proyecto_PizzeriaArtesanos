<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreOrdenDetalleRequest;
use App\Http\Requests\UpdateOrdenDetalleRequest;
use App\Http\Resources\OrdenDetalleCollection;
use App\Http\Resources\OrdenDetalleResource;
use App\Models\Orden;
use App\Models\OrdenDetalle;
use App\Services\DetalleOrdenService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class OrdenDetalleController extends Controller
{
    /**
     * Listar los renglones. Se puede filtrar con ?orden_id=1
     */
    public function index(Request $request): OrdenDetalleCollection
    {
        $detalles = OrdenDetalle::query()
            ->when(
                $request->query('orden_id'),
                fn ($consulta, $ordenId) => $consulta->where('orden_id', $ordenId)
            )
            ->get();

        return new OrdenDetalleCollection($detalles);
    }

    /**
     * Agregar un renglón a una orden abierta y recalcular su total.
     */
    public function store(StoreOrdenDetalleRequest $request, DetalleOrdenService $servicio): JsonResponse
    {
        $orden = Orden::findOrFail($request->validated('orden_id'));

        if ($orden->estado !== 'abierta') {
            return response()->json([
                'mensaje' => 'Solo se pueden agregar productos a órdenes abiertas.',
            ], 409);
        }

        $detalle = DB::transaction(function () use ($request, $orden, $servicio) {
            $detalle = $orden->detalles()->create($servicio->construir(
                (int) $request->validated('producto_id'),
                $request->filled('tamano_id') ? (int) $request->validated('tamano_id') : null,
                $request->boolean('orilla_queso'),
                (int) $request->validated('cantidad'),
                $request->validated('notas')
            ));

            $servicio->recalcularTotal($orden);

            return $detalle;
        });

        return response()->json([
            'mensaje' => 'Producto agregado a la orden correctamente',
            'data' => new OrdenDetalleResource($detalle),
            'total_orden' => (float) $orden->fresh()->total,
        ], 201);
    }

    /**
     * Mostrar un renglón.
     */
    public function show(OrdenDetalle $ordenDetalle): OrdenDetalleResource
    {
        return new OrdenDetalleResource($ordenDetalle);
    }

    /**
     * Modificar cantidad, orilla de queso o notas de un renglón.
     * Se conserva el precio con el que se vendió.
     */
    public function update(
        UpdateOrdenDetalleRequest $request,
        OrdenDetalle $ordenDetalle,
        DetalleOrdenService $servicio
    ): JsonResponse {
        $orden = $ordenDetalle->orden;

        if ($orden->estado !== 'abierta') {
            return response()->json([
                'mensaje' => 'Solo se pueden modificar productos de órdenes abiertas.',
            ], 409);
        }

        $orilla = $request->has('orilla_queso')
            ? $request->boolean('orilla_queso')
            : (bool) $ordenDetalle->orilla_queso;
        $cantidad = (int) $request->input('cantidad', $ordenDetalle->cantidad);

        $precioOrilla = 0;
        if ($orilla) {
            $tamano = $ordenDetalle->tamano;

            if (! $tamano) {
                throw ValidationException::withMessages([
                    'orilla_queso' => 'La orilla de queso solo aplica a pizzas con tamaño.',
                ]);
            }

            $precioOrilla = $tamano->precio_orilla_queso;
        }

        DB::transaction(function () use ($request, $ordenDetalle, $orden, $servicio, $orilla, $cantidad, $precioOrilla) {
            $ordenDetalle->update([
                'orilla_queso' => $orilla,
                'precio_orilla' => $precioOrilla,
                'cantidad' => $cantidad,
                'importe' => ($ordenDetalle->precio_unitario + $precioOrilla) * $cantidad,
                'notas' => $request->has('notas') ? $request->input('notas') : $ordenDetalle->notas,
            ]);

            $servicio->recalcularTotal($orden);
        });

        return response()->json([
            'mensaje' => 'Renglón actualizado correctamente',
            'data' => new OrdenDetalleResource($ordenDetalle->fresh()),
            'total_orden' => (float) $orden->fresh()->total,
        ]);
    }

    /**
     * Quitar un renglón de una orden abierta y recalcular su total.
     */
    public function destroy(OrdenDetalle $ordenDetalle, DetalleOrdenService $servicio): JsonResponse
    {
        $orden = $ordenDetalle->orden;

        if ($orden->estado !== 'abierta') {
            return response()->json([
                'mensaje' => 'Solo se pueden quitar productos de órdenes abiertas.',
            ], 409);
        }

        DB::transaction(function () use ($ordenDetalle, $orden, $servicio) {
            $ordenDetalle->delete();
            $servicio->recalcularTotal($orden);
        });

        return response()->json([
            'mensaje' => 'Producto quitado de la orden correctamente',
            'total_orden' => (float) $orden->fresh()->total,
        ]);
    }
}