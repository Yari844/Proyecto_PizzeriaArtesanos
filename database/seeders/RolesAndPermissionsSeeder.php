<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Crear los roles y permisos del sistema.
     */
    public function run(): void
    {
        // Limpia el caché de permisos
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        // Permisos: recurso.acción
        $recursos = ['productos', 'tamanos', 'precios', 'promociones', 'usuarios', 'ordenes', 'detalles'];
        $acciones = ['ver', 'crear', 'editar', 'eliminar'];

        foreach ($recursos as $recurso) {
            foreach ($acciones as $accion) {
                Permission::firstOrCreate(['name' => "$recurso.$accion", 'guard_name' => 'web']);
            }
        }

        // Rol desarrollador: el equipo, con todos los permisos
        $desarrollador = Role::firstOrCreate(['name' => 'desarrollador', 'guard_name' => 'web']);
        $desarrollador->syncPermissions(Permission::all());

        // Rol administrador: todos los permisos del negocio
        $administrador = Role::firstOrCreate(['name' => 'administrador', 'guard_name' => 'web']);
        $administrador->syncPermissions(Permission::all());

        // Rol empleado: consulta el menú y toma pedidos, sin modificar precios ni usuarios
        $empleado = Role::firstOrCreate(['name' => 'empleado', 'guard_name' => 'web']);
        $empleado->syncPermissions([
            'productos.ver',
            'tamanos.ver',
            'precios.ver',
            'promociones.ver',
            'ordenes.ver',
            'ordenes.crear',
            'ordenes.editar',
            'detalles.ver',
            'detalles.crear',
            'detalles.editar',
            'detalles.eliminar',
        ]);
    }
}