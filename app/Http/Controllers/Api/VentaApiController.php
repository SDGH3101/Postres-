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
                   u.nombre AS empleado, p.descripcion AS producto, v.total
            FROM venta v
            JOIN empleado em ON v.id_empleado = em.id_usuario
            JOIN usuario  u  ON em.id_usuario  = u.id_usuario
            JOIN producto p  ON v.id_producto  = p.id_producto
            ORDER BY v.fecha DESC
        ");
        return response()->json(['data' => $ventas, 'total' => array_sum(array_column($ventas, 'total'))]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'id_producto' => 'required|exists:producto,id_producto',
            'id_empleado' => 'required|exists:empleado,id_usuario',
            'fecha'       => 'required|date',
        ]);
        $producto = DB::table('producto')->where('id_producto', $request->id_producto)->first();
        if ($producto->stock < 1) {
            return response()->json(['error' => 'Sin stock disponible'], 422);
        }
        $id = DB::table('venta')->insertGetId([
            'fecha'       => $request->fecha,
            'total'       => $producto->precio,
            'id_empleado' => $request->id_empleado,
            'id_producto' => $request->id_producto,
        ]);
        return response()->json(['message' => 'Venta registrada', 'id' => $id], 201);
    }
}
