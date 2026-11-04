<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreUsuarioRequest;
use App\Http\Requests\UpdateUsuarioRequest;
use App\Http\Resources\UsuarioCollection;
use App\Http\Resources\UsuarioResource;
use App\Models\User;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;

class UsuarioController extends Controller
{
    /**
     * Listar todos los usuarios.
     */
    public function index(): UsuarioCollection
    {
        return new UsuarioCollection(User::orderBy('name')->get());
    }

    /**
     * Crear un usuario (la contraseña se guarda cifrada).
     */
    public function store(StoreUsuarioRequest $request): JsonResponse
    {
        $datos = $request->validated();
        $datos['password'] = Hash::make($datos['password']);

        $usuario = User::create($datos);

        return response()->json([
            'mensaje' => 'Usuario creado correctamente',
            'data' => new UsuarioResource($usuario),
        ], 201);
    }

    /**
     * Mostrar un usuario.
     */
    public function show(User $usuario): UsuarioResource
    {
        return new UsuarioResource($usuario);
    }

    /**
     * Actualizar un usuario. Si no se envía contraseña, se conserva la actual.
     */
    public function update(UpdateUsuarioRequest $request, User $usuario): JsonResponse
    {
        $datos = $request->validated();

        if (! empty($datos['password'])) {
            $datos['password'] = Hash::make($datos['password']);
        } else {
            unset($datos['password']);
        }

        $usuario->update($datos);

        return response()->json([
            'mensaje' => 'Usuario actualizado correctamente',
            'data' => new UsuarioResource($usuario),
        ]);
    }

    /**
     * Eliminar un usuario.
     */
    public function destroy(User $usuario): JsonResponse
    {
        try {
            $usuario->delete();
        } catch (QueryException $e) {
            return response()->json([
                'mensaje' => 'No se puede eliminar el usuario porque tiene órdenes registradas. Desactívalo en su lugar.',
            ], 409);
        }

        return response()->json([
            'mensaje' => 'Usuario eliminado correctamente',
        ]);
    }
}