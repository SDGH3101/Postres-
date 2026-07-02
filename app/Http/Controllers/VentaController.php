<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class VentaController extends Controller
{
    // sp_listar_ventas()
    public function index()
    {
        $ventas = DB::select("
            SELECT v.id_venta, DATE_FORMAT(v.fecha,'%d-%m-%Y') AS fecha,
                   u.nombre AS empleado, em.cargo,
                   p.descripcion AS producto, p.precio, v.total
            FROM venta v
            JOIN empleado em ON v.id_empleado = em.id_usuario
            JOIN usuario  u  ON em.id_usuario  = u.id_usuario
            JOIN producto p  ON v.id_producto  = p.id_producto
            ORDER BY v.fecha DESC
        ");
        $productos = DB::table('producto')->where('stock', '>', 0)->get();
        $empleados = DB::table('empleado')
            ->join('usuario','empleado.id_usuario','=','usuario.id_usuario')
            ->select('empleado.id_usuario','usuario.nombre','empleado.cargo')
            ->get();
        return view('compras.ventas', compact('ventas', 'productos', 'empleados'));
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
            return back()->withErrors(['id_producto' => 'Producto sin stock disponible.']);
        }
        // El trigger trg_descontar_stock y trg_ingreso_por_venta se ejecutan automáticamente
        DB::table('venta')->insert([
            'fecha'       => $request->fecha,
            'total'       => $producto->precio,
            'id_empleado' => $request->id_empleado,
            'id_producto' => $request->id_producto,
        ]);
        return redirect()->route('ventas.index')->with('success', 'Venta registrada. Stock descontado automáticamente.');
    }

    // Stubs resource
    public function create()  { return $this->index(); }
    public function show($id) { abort(404); }
    public function edit($id) { abort(404); }
    public function update(Request $r, $id) { abort(404); }
    public function destroy($id)
    {
        DB::table('venta')->where('id_venta', $id)->delete();
        return redirect()->route('ventas.index')->with('success', 'Venta eliminada.');
    }
}
