<?php

use App\Http\Controllers\Api\OrdenController;
use App\Http\Controllers\Api\OrdenDetalleController;
use App\Http\Controllers\Api\ProductoController;
use App\Http\Controllers\Api\ProductoPrecioController;
use App\Http\Controllers\Api\PromocionController;
use App\Http\Controllers\Api\TamanoController;
use App\Http\Controllers\Api\UsuarioController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// Rutas resource de la API (una por cada migración principal)
Route::apiResource('productos', ProductoController::class);
Route::apiResource('tamanos', TamanoController::class)->parameters(['tamanos' => 'tamano']);
Route::apiResource('promociones', PromocionController::class)->parameters(['promociones' => 'promocion']);
Route::apiResource('producto-precios', ProductoPrecioController::class)->parameters(['producto-precios' => 'productoPrecio']);
Route::apiResource('usuarios', UsuarioController::class)->parameters(['usuarios' => 'usuario']);
Route::apiResource('ordenes', OrdenController::class)->parameters(['ordenes' => 'orden']);
Route::apiResource('orden-detalles', OrdenDetalleController::class)->parameters(['orden-detalles' => 'ordenDetalle']);