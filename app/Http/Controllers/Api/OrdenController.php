<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreOrdenRequest;
use App\Http\Requests\UpdateOrdenRequest;
use App\Http\Resources\OrdenCollection;
use App\Http\Resources\OrdenResource;
use App\Models\Orden;
use App\Services\DetalleOrdenService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class OrdenController extends Controller
{
    /**
     * Listar las órdenes. Se puede filtrar con ?estado=abierta
     */
    public function index(Request $request): OrdenCollection
    {
        $ordenes = Orden::with('user')
            ->when(
                $request->query('estado'),
                fn ($consulta, $estado) => $consulta->where('estado', $estado)
            )
            ->latest()
            ->get();

        return new OrdenCollection($ordenes);
    }

    /**
     * Crear una orden con sus renglones. Los precios se toman del menú
     * y el total se calcula en el servidor.
     */
    public function store(StoreOrdenRequest $request, DetalleOrdenService $servicio): JsonResponse
    {
        $orden = DB::transaction(function () use ($request, $servicio) {
            $orden = Orden::create($request->safe()->except('detalles') + [
                'folio' => 'TEMP-' . Str::random(10),
                'estado' => 'abierta',
                'total' => 0,
            ]);

            $orden->update(['folio' => sprintf('ORD-%06d', $orden->id)]);

            foreach ($request->validated('detalles') as $indice => $detalle) {
                $orden->detalles()->create($servicio->construir(
                    (int) $detalle['producto_id'],
                    isset($detalle['tamano_id']) ? (int) $detalle['tamano_id'] : null,
                    (bool) ($detalle['orilla_queso'] ?? false),
                    (int) $detalle['cantidad'],
                    $detalle['notas'] ?? null,
                    "detalles.$indice.producto_id"
                ));
            }

            $servicio->recalcularTotal($orden);

            return $orden;
        });

        return response()->json([
            'mensaje' => 'Orden creada correctamente',
            'data' => new OrdenResource($orden->load(['user', 'detalles'])),
        ], 201);
    }

    /**
     * Mostrar una orden con sus renglones.
     */
    public function show(Orden $orden): OrdenResource
    {
        return new OrdenResource($orden->load(['user', 'detalles']));
    }

    /**
     * Actualizar una orden: datos del cliente, cancelarla o cobrarla.
     */
    public function update(UpdateOrdenRequest $request, Orden $orden): JsonResponse
    {
        if ($orden->estado !== 'abierta') {
            return response()->json([
                'mensaje' => 'Solo se pueden modificar órdenes abiertas.',
            ], 409);
        }

        $datos = $request->validated();

        if (($datos['estado'] ?? null) === 'pagada') {
            if ($orden->detalles()->count() === 0) {
                throw ValidationException::withMessages([
                    'estado' => 'No se puede cobrar una orden sin productos.',
                ]);
            }

            $total = round((float) $orden->total, 2);
            $recibido = $datos['metodo_pago'] === 'efectivo'
                ? round((float) $datos['monto_recibido'], 2)
                : $total;

            if ($recibido < $total) {
                throw ValidationException::withMessages([
                    'monto_recibido' => 'El monto recibido es menor al total de la orden ($' . number_format($total, 2) . ').',
                ]);
            }

            $datos['monto_recibido'] = $recibido;
            $datos['cambio'] = $recibido - $total;
            $datos['pagada_at'] = now();
        } else {
            $datos = Arr::except($datos, ['metodo_pago', 'monto_recibido']);
        }

        $orden->update($datos);

        return response()->json([
            'mensaje' => 'Orden actualizada correctamente',
            'data' => new OrdenResource($orden->load(['user', 'detalles'])),
        ]);
    }

    /**
     * Eliminar una orden (las pagadas no se pueden eliminar).
     */
    public function destroy(Orden $orden): JsonResponse
    {
        if ($orden->estado === 'pagada') {
            return response()->json([
                'mensaje' => 'Las órdenes pagadas no se pueden eliminar.',
            ], 409);
        }

        $orden->delete();

        return response()->json([
            'mensaje' => 'Orden eliminada correctamente',
        ]);
    }
}