<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $usuario = session('usuario');

        // El cliente solo ve la bienvenida con la fachada del negocio,
        // sin acceso a ningún dato de movimientos financieros del sistema.
        if ($usuario && ($usuario['rol'] ?? null) === 'cliente') {
            return view('dashboard.cliente', compact('usuario'));
        }

        // KPIs generales — procedimientos almacenados usados en reportes
        $totalProductos = DB::table('producto')->count();
        $totalUsuarios  = DB::table('usuario')->count();

        // Ventas e ingresos (ahora una venta puede tener varios productos)
        $ventas = DB::select("
            SELECT v.id_venta, DATE_FORMAT(v.fecha,'%d-%m-%Y') AS fecha, v.total, u.nombre AS empleado,
                   GROUP_CONCAT(CONCAT(p.descripcion,' x',d.cantidad) SEPARATOR ', ') AS producto
            FROM venta v
            JOIN detalle_venta d ON d.id_venta = v.id_venta
            JOIN producto p ON d.id_producto = p.id_producto
            JOIN empleado em ON v.id_empleado = em.id_usuario
            JOIN usuario  u  ON em.id_usuario = u.id_usuario
            GROUP BY v.id_venta, v.fecha, v.total, u.nombre
            ORDER BY v.fecha DESC LIMIT 5
        ");

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
            SELECT p.descripcion, p.precio, COUNT(d.id_detalle_venta) AS veces, SUM(d.subtotal) AS ingresos
            FROM producto p
            JOIN detalle_venta d ON d.id_producto = p.id_producto
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
