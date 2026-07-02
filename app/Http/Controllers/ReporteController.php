<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

class ReporteController extends Controller
{
    public function index()
    {
        $ventasMes = DB::select("
            SELECT DATE_FORMAT(v.fecha,'%m-%Y') AS mes,
                   COUNT(v.id_venta) AS total_ventas,
                   SUM(v.total) AS ingresos_mes,
                   AVG(v.total) AS ticket_promedio
            FROM venta v
            GROUP BY DATE_FORMAT(v.fecha,'%m-%Y')
            ORDER BY mes
        ");

        $gastosCat = DB::select("
            SELECT g.categoria, COUNT(*) AS cantidad,
                   SUM(g.monto) AS total, AVG(g.monto) AS promedio
            FROM gasto g GROUP BY g.categoria ORDER BY total DESC
        ");

        $masVendidos = DB::select("
            SELECT p.descripcion, p.precio, p.stock,
                   COUNT(v.id_venta) AS veces, SUM(v.total) AS ingresos
            FROM producto p
            JOIN venta v ON v.id_producto = p.id_producto
            GROUP BY p.id_producto, p.descripcion, p.precio, p.stock ORDER BY veces DESC
        ");

        $balance = DB::select("
            SELECT ventas.mes,
                   ventas.total_ventas AS ingresos,
                   COALESCE(gastos.total_gastos,0) AS gastos,
                   ventas.total_ventas - COALESCE(gastos.total_gastos,0) AS balance
            FROM (
                SELECT DATE_FORMAT(fecha,'%m-%Y') AS mes, SUM(total) AS total_ventas
                FROM venta GROUP BY DATE_FORMAT(fecha,'%m-%Y')
            ) ventas
            LEFT JOIN (
                SELECT DATE_FORMAT(fecha,'%m-%Y') AS mes, SUM(monto) AS total_gastos
                FROM gasto GROUP BY DATE_FORMAT(fecha,'%m-%Y')
            ) gastos ON ventas.mes = gastos.mes
            ORDER BY ventas.mes
        ");

        return view('reportes.index', compact('ventasMes','gastosCat','masVendidos','balance'));
    }

    public function ventasPdf()
    {
        $ventas = DB::select("
            SELECT v.id_venta, DATE_FORMAT(v.fecha,'%d-%m-%Y') AS fecha,
                   u.nombre AS empleado, p.descripcion AS producto, v.total
            FROM venta v
            JOIN empleado em ON v.id_empleado = em.id_usuario
            JOIN usuario  u  ON em.id_usuario = u.id_usuario
            JOIN producto p  ON v.id_producto = p.id_producto
            ORDER BY v.fecha
        ");
        $totalVentas = array_sum(array_column($ventas, 'total'));
        $pdf = Pdf::loadView('reportes.ventas_pdf', compact('ventas','totalVentas'))
                  ->setPaper('a4','landscape');
        return $pdf->download('reporte_ventas_postres_laura.pdf');
    }

    public function gastosPdf()
    {
        $gastos = DB::select("
            SELECT DATE_FORMAT(g.fecha,'%d-%m-%Y') AS fecha,
                   g.descripcion, g.categoria, g.monto, g.presupuesto
            FROM gasto g ORDER BY g.fecha
        ");
        $totalGastos = array_sum(array_column($gastos, 'monto'));
        $pdf = Pdf::loadView('reportes.gastos_pdf', compact('gastos','totalGastos'))
                  ->setPaper('a4','portrait');
        return $pdf->download('reporte_gastos_postres_laura.pdf');
    }

    // CSV nativo — compatible con PHP 8.5 sin dependencias externas
    public function excel()
    {
        $ventas = DB::select("
            SELECT v.id_venta AS ID,
                   DATE_FORMAT(v.fecha,'%d-%m-%Y') AS Fecha,
                   u.nombre AS Empleado,
                   p.descripcion AS Producto,
                   v.total AS Total
            FROM venta v
            JOIN empleado em ON v.id_empleado = em.id_usuario
            JOIN usuario  u  ON em.id_usuario = u.id_usuario
            JOIN producto p  ON v.id_producto = p.id_producto
            ORDER BY v.fecha
        ");

        $headers = [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="ventas_postres_laura.csv"',
        ];

        $callback = function () use ($ventas) {
            $handle = fopen('php://output', 'w');
            fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF)); // BOM UTF-8 para Excel
            fputcsv($handle, ['ID','Fecha','Empleado','Producto','Total ($)'], ';');
            foreach ($ventas as $v) {
                fputcsv($handle, [
                    $v->ID, $v->Fecha, $v->Empleado, $v->Producto, $v->Total
                ], ';');
            }
            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }
}
