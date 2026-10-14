<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Poblar la base de datos de la aplicación.
     */
    public function run(): void
    {
        $now = now();

        // Usuarios iniciales (cambia las contraseñas)
        User::updateOrCreate(['email' => 'admin@pizzeria.test'], [
            'name' => 'Administrador',
            'password' => Hash::make('cambiar123'),
            'rol' => 'administrador',
        ]);
       foreach ([1, 2, 3] as $numero) {
        User::updateOrCreate(['email' => "caja{$numero}@pizzeria.test"], [
        'name' => "Cajero {$numero}",
        'password' => Hash::make('cambiar123'),
        'rol' => 'empleado',
    ]);
       }

        // Tamaños + precio de la orilla de queso
        $tamanos = ['Chica' => 40, 'Mediana' => 50, 'Grande' => 60, 'Familiar' => 70];
        $tamanoIds = [];
        $orden = 1;
        foreach ($tamanos as $nombre => $orilla) {
            $tamanoIds[$nombre] = DB::table('tamanos')->insertGetId([
                'nombre' => $nombre,
                'precio_orilla_queso' => $orilla,
                'orden' => $orden++,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }

        // Pizzas (mismo precio por tamaño)
        $preciosPizza = ['Chica' => 120, 'Mediana' => 170, 'Grande' => 220, 'Familiar' => 300];
        $pizzas = [
            'Peperoni'     => 'Peperoni y doble queso',
            'Hawaiana'     => 'Jamón y piña',
            'Vegetariana'  => 'Cebolla, elote, piña, champiñón, morrón, aceitunas negras',
            'Carnes Frías' => 'Jamón, peperoni, tocino, salchicha y chorizo',
            'Mexicana'     => 'Chorizo, elote, frijoles, cebolla morada y jalapeño',
            'Italiana'     => 'Peperoni, champiñón, morrón, tocino y aceituna negra',
            'Choriqueso'   => 'Chorizo y doble queso',
            'Pastor'       => 'Carne al pastor, piña y cebolla morada',
        ];
        foreach ($pizzas as $nombre => $descripcion) {
            $id = DB::table('productos')->insertGetId([
                'tipo' => 'pizza',
                'nombre' => $nombre,
                'descripcion' => $descripcion,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
            foreach ($preciosPizza as $tamano => $precio) {
                DB::table('producto_precios')->insert([
                    'producto_id' => $id,
                    'tamano_id' => $tamanoIds[$tamano],
                    'precio' => $precio,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }
        }

        // Extras (precio único, sin tamaño)
        $extras = [
            ['Boneless', '200 gramos', 100, null],
            ['Papas Gajo', '350 gramos', 80, null],
            ['Alitas', '8 piezas + 100 g de papas', 120, 'Salsas: BBQ, Red Hot, Mango Habanero, Tamarindo Habanero'],
        ];
        foreach ($extras as [$nombre, $presentacion, $precio, $descripcion]) {
            $id = DB::table('productos')->insertGetId([
                'tipo' => 'extra',
                'nombre' => $nombre,
                'descripcion' => $descripcion,
                'presentacion' => $presentacion,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
            DB::table('producto_precios')->insert([
                'producto_id' => $id,
                'tamano_id' => null,
                'precio' => $precio,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }
                // Órdenes de ejemplo (para probar el POS y los reportes)
        $cajeros = User::where('rol', 'empleado')->pluck('id')->all();
        $productos = DB::table('productos')->get();
        $tamanosDb = DB::table('tamanos')->get()->keyBy('id');
        $clientes = ['Juan Pérez', 'María López', 'Carlos Ramírez', 'Ana Torres', 'Luis Hernández'];
        $tipos = ['comer_aqui', 'para_llevar', 'telefono'];
        $metodos = ['efectivo', 'tarjeta', 'transferencia'];

        for ($i = 1; $i <= 15; $i++) {
            $fecha = now()->subDays(rand(0, 14))->setTime(rand(12, 21), rand(0, 59));
            $estado = $i === 14 ? 'abierta' : ($i === 15 ? 'cancelada' : 'pagada');

            $ordenId = DB::table('ordenes')->insertGetId([
                'folio' => sprintf('ORD-%06d', $i),
                'user_id' => $cajeros[array_rand($cajeros)],
                'tipo_servicio' => $tipos[array_rand($tipos)],
                'estado' => $estado,
                'cliente_nombre' => $clientes[array_rand($clientes)],
                'cliente_telefono' => '443' . rand(1000000, 9999999),
                'total' => 0,
                'created_at' => $fecha,
                'updated_at' => $fecha,
            ]);

            $total = 0;
            $renglones = rand(1, 3);
            for ($j = 0; $j < $renglones; $j++) {
                $producto = $productos->random();
                $precio = DB::table('producto_precios')
                    ->where('producto_id', $producto->id)
                    ->inRandomOrder()
                    ->first();
                $tamano = $precio->tamano_id ? $tamanosDb[$precio->tamano_id] : null;

                $conOrilla = $producto->tipo === 'pizza' && rand(1, 100) <= 30;
                $precioOrilla = $conOrilla ? $tamano->precio_orilla_queso : 0;
                $cantidad = rand(1, 2);
                $importe = ($precio->precio + $precioOrilla) * $cantidad;

                DB::table('orden_detalles')->insert([
                    'orden_id' => $ordenId,
                    'producto_id' => $producto->id,
                    'tamano_id' => $precio->tamano_id,
                    'producto_nombre' => $producto->nombre,
                    'tamano_nombre' => $tamano?->nombre,
                    'orilla_queso' => $conOrilla,
                    'precio_unitario' => $precio->precio,
                    'precio_orilla' => $precioOrilla,
                    'cantidad' => $cantidad,
                    'importe' => $importe,
                    'created_at' => $fecha,
                    'updated_at' => $fecha,
                ]);

                $total += $importe;
            }

            $datosPago = [];
            if ($estado === 'pagada') {
                $metodo = $metodos[array_rand($metodos)];
                $recibido = $metodo === 'efectivo' ? ceil($total / 100) * 100 : $total;
                $datosPago = [
                    'metodo_pago' => $metodo,
                    'monto_recibido' => $recibido,
                    'cambio' => $recibido - $total,
                    'pagada_at' => $fecha,
                ];
            }

            DB::table('ordenes')->where('id', $ordenId)->update(['total' => $total] + $datosPago);
        }
    }
}