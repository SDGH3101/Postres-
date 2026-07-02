<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $usuario = session('usuario');

        // KPIs generales — procedimientos almacenados usados en reportes
        $totalProductos = DB::table('producto')->count();
        $totalUsuarios  = DB::table('usuario')->count();

        // Ventas e ingresos
        $ventas         = DB::table('venta')
            ->join('producto', 'venta.id_producto', '=', 'producto.id_producto')
            ->join('empleado', 'venta.id_empleado', '=', 'empleado.id_usuario')
            ->join('usuario',  'empleado.id_usuario', '=', 'usuario.id_usuario')
            ->select('venta.*', 'producto.descripcion as producto', 'usuario.nombre as empleado')
            ->orderByDesc('venta.fecha')->limit(5)->get();

        $totalIngresos  = DB::table('ingreso')->sum('monto');
        $totalGastos    = DB::table('gasto')->sum('monto')
                        + DB::table('compras_menores')->sum('monto');

        // Ventas por mes para la gráfica de barras
        $ventasMes = DB::select("
            SELECT DATE_FORMAT(fecha,'%m-%Y') AS mes,
                   SUM(total) AS total
            FROM venta
            GROUP BY DATE_FORMAT(fecha,'%m-%Y')
            ORDER BY mes
        ");

        // Productos más vendidos — llama lógica del sp_productos_mas_vendidos
        $masVendidos = DB::select("
            SELECT p.descripcion, p.precio, COUNT(v.id_venta) AS veces, SUM(v.total) AS ingresos
            FROM producto p
            JOIN venta v ON v.id_producto = p.id_producto
            GROUP BY p.id_producto, p.descripcion, p.precio
            ORDER BY veces DESC LIMIT 5
        ");

        return view('dashboard.index', compact(
            'usuario', 'totalProductos', 'totalUsuarios',
            'ventas', 'totalIngresos', 'totalGastos',
            'ventasMes', 'masVendidos'
        ));
    }
}
