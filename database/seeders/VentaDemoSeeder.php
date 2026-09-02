<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

// Genera 10 ventas de prueba por cada uno de los últimos 30 días
// (hoy incluido), además de las ventas ya existentes en el sistema.
// Usa el mismo flujo que VentaController::store (insert en venta +
// detalle_venta) para que los triggers trg_descontar_stock y
// trg_ingreso_por_venta se disparen normalmente.
class VentaDemoSeeder extends Seeder
{
    public function run(): void
    {
        $idEmpleado = DB::table('empleado')->value('id_usuario');
        if (!$idEmpleado) {
            $this->command->error('No hay empleados registrados. Aborta.');
            return;
        }

        $productos = DB::table('producto')->get();
        if ($productos->isEmpty()) {
            $this->command->error('No hay productos registrados. Aborta.');
            return;
        }

        $mediosPago = [
            'efectivo', 'efectivo', 'efectivo',
            'transferencia_nequi', 'transferencia_nequi',
            'transferencia_daviplata',
            'transferencia_bancaria',
            'tarjeta_debito', 'tarjeta_credito',
        ];

        $totalVentas = 0;
        for ($diasAtras = 29; $diasAtras >= 0; $diasAtras--) {
            $fecha = Carbon::now()->subDays($diasAtras)->toDateString();

            for ($i = 0; $i < 10; $i++) {
                $numItems = rand(1, 3);
                $seleccion = $productos->random(min($numItems, $productos->count()));
                if (!$seleccion instanceof \Illuminate\Support\Collection) {
                    $seleccion = collect([$seleccion]);
                }

                $lineas = [];
                $total  = 0;
                foreach ($seleccion as $producto) {
                    $cantidad = rand(1, 4);
                    $subtotal = $producto->precio * $cantidad;
                    $total   += $subtotal;
                    $lineas[] = [
                        'id_producto'     => $producto->id_producto,
                        'cantidad'        => $cantidad,
                        'precio_unitario' => $producto->precio,
                        'subtotal'        => $subtotal,
                    ];
                }

                DB::transaction(function () use ($fecha, $idEmpleado, $mediosPago, $lineas, $total) {
                    $idVenta = DB::table('venta')->insertGetId([
                        'fecha'       => $fecha,
                        'total'       => $total,
                        'medio_pago'  => $mediosPago[array_rand($mediosPago)],
                        'id_empleado' => $idEmpleado,
                    ]);
                    foreach ($lineas as $linea) {
                        DB::table('detalle_venta')->insert(array_merge(['id_venta' => $idVenta], $linea));
                    }
                });

                $totalVentas++;
            }
        }

        $this->command->info("Se generaron {$totalVentas} ventas de prueba (10/día × 30 días).");
    }
}
