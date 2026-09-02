<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class VentaController extends Controller
{
    // Listado de ventas con sus líneas de producto agrupadas
    public function index()
    {
        $ventas = DB::select("
            SELECT v.id_venta, DATE_FORMAT(v.fecha,'%d-%m-%Y') AS fecha,
                   u.nombre AS empleado, em.cargo, v.total, v.medio_pago,
                   GROUP_CONCAT(CONCAT(p.descripcion,' x',d.cantidad) SEPARATOR ', ') AS productos
            FROM venta v
            JOIN empleado em ON v.id_empleado = em.id_usuario
            JOIN usuario  u  ON em.id_usuario  = u.id_usuario
            JOIN detalle_venta d ON d.id_venta = v.id_venta
            JOIN producto p  ON d.id_producto  = p.id_producto
            GROUP BY v.id_venta, v.fecha, u.nombre, em.cargo, v.total, v.medio_pago
            ORDER BY v.fecha DESC
        ");
        $productos = DB::table('producto')->where('stock', '>', 0)
            ->where(function ($q) {
                $q->whereNull('fecha_caducidad')->orWhere('fecha_caducidad', '>=', now()->toDateString());
            })
            ->orderBy('descripcion')->get();
        $empleados = DB::table('empleado')
            ->join('usuario','empleado.id_usuario','=','usuario.id_usuario')
            ->select('empleado.id_usuario','usuario.nombre','empleado.cargo')
            ->get();
        return view('compras.ventas', compact('ventas', 'productos', 'empleados'));
    }

    // Registrar una venta con uno o varios productos (carrito)
    public function store(Request $request)
    {
        $request->validate([
            'id_empleado'            => 'required|exists:empleado,id_usuario',
            'fecha'                  => 'required|date',
            'medio_pago'             => 'required|in:efectivo,transferencia_nequi,transferencia_daviplata,transferencia_bancaria,tarjeta_credito,tarjeta_debito',
            'items'                  => 'required|array|min:1',
            'items.*.id_producto'    => 'required|exists:producto,id_producto',
            'items.*.cantidad'       => 'required|integer|min:1',
        ], [
            'items.required' => 'Agrega al menos un producto al carrito.',
        ]);

        // Validar stock disponible para cada línea antes de insertar nada
        $lineas = [];
        $total  = 0;
        foreach ($request->items as $item) {
            $producto = DB::table('producto')->where('id_producto', $item['id_producto'])->first();
            if (!$producto) {
                return back()->withErrors(['items' => 'Producto no encontrado.'])->withInput();
            }
            if ($producto->stock < $item['cantidad']) {
                return back()->withErrors([
                    'items' => "Stock insuficiente para {$producto->descripcion} (disponible: {$producto->stock})."
                ])->withInput();
            }
            if ($producto->fecha_caducidad && $producto->fecha_caducidad < now()->toDateString()) {
                return back()->withErrors([
                    'items' => "{$producto->descripcion} está vencido desde {$producto->fecha_caducidad} y no se puede vender."
                ])->withInput();
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

        DB::transaction(function () use ($request, $lineas, $total) {
            $idVenta = DB::table('venta')->insertGetId([
                'fecha'       => $request->fecha,
                'total'       => $total,
                'medio_pago'  => $request->medio_pago,
                'id_empleado' => $request->id_empleado,
            ]);

            foreach ($lineas as $linea) {
                // Los triggers trg_descontar_stock y trg_ingreso_por_venta
                // se disparan automáticamente por cada línea insertada aquí.
                DB::table('detalle_venta')->insert(array_merge(['id_venta' => $idVenta], $linea));
            }
        });

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
