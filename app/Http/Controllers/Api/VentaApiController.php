<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class VentaApiController extends Controller
{
    public function index()
    {
        $ventas = DB::select("
            SELECT v.id_venta, DATE_FORMAT(v.fecha,'%d-%m-%Y') AS fecha,
                   u.nombre AS empleado, v.total, v.medio_pago,
                   GROUP_CONCAT(CONCAT(p.descripcion,' x',d.cantidad) SEPARATOR ', ') AS productos
            FROM venta v
            JOIN empleado em ON v.id_empleado = em.id_usuario
            JOIN usuario  u  ON em.id_usuario  = u.id_usuario
            JOIN detalle_venta d ON d.id_venta = v.id_venta
            JOIN producto p  ON d.id_producto  = p.id_producto
            GROUP BY v.id_venta, v.fecha, u.nombre, v.total, v.medio_pago
            ORDER BY v.fecha DESC
        ");
        return response()->json(['data' => $ventas, 'total' => array_sum(array_column($ventas, 'total'))]);
    }

    // Body esperado ahora (cambio de contrato — actualizar app Kotlin):
    // {
    //   "id_empleado": 2,
    //   "fecha": "2026-07-08",
    //   "items": [
    //     { "id_producto": 1, "cantidad": 2 },
    //     { "id_producto": 4, "cantidad": 1 }
    //   ]
    // }
    public function store(Request $request)
    {
        $request->validate([
            'id_empleado'         => 'required|exists:empleado,id_usuario',
            'fecha'               => 'required|date',
            'medio_pago'          => 'nullable|in:efectivo,transferencia_nequi,transferencia_daviplata,transferencia_bancaria,tarjeta_credito,tarjeta_debito',
            'items'               => 'required|array|min:1',
            'items.*.id_producto' => 'required|exists:producto,id_producto',
            'items.*.cantidad'    => 'required|integer|min:1',
        ]);

        $lineas = [];
        $total  = 0;
        foreach ($request->items as $item) {
            $producto = DB::table('producto')->where('id_producto', $item['id_producto'])->first();
            if ($producto->stock < $item['cantidad']) {
                return response()->json([
                    'error' => "Stock insuficiente para {$producto->descripcion}"
                ], 422);
            }
            if ($producto->fecha_caducidad && $producto->fecha_caducidad < now()->toDateString()) {
                return response()->json([
                    'error' => "{$producto->descripcion} está vencido desde {$producto->fecha_caducidad} y no se puede vender."
                ], 422);
            }
            $subtotal = $producto->precio * $item['cantidad'];
            $total   += $subtotal;
            $lineas[] = [
                'id_producto'     => $producto->id_producto,
                'cantidad'        => $item['cantidad'],
                'precio_unitario' => $producto->precio,
                'subtotal'        => $subtotal,
            ];
        }

        $idVenta = DB::transaction(function () use ($request, $lineas, $total) {
            $id = DB::table('venta')->insertGetId([
                'fecha'       => $request->fecha,
                'total'       => $total,
                'medio_pago'  => $request->medio_pago ?? 'efectivo',
                'id_empleado' => $request->id_empleado,
            ]);
            foreach ($lineas as $linea) {
                DB::table('detalle_venta')->insert(array_merge(['id_venta' => $id], $linea));
            }
            return $id;
        });

        return response()->json(['message' => 'Venta registrada', 'id' => $idVenta, 'total' => $total], 201);
    }
}
