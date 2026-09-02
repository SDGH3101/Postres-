<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FlujoCajaController extends Controller
{
    private function periodoSql(string $periodo): array
    {
        return match ($periodo) {
            'diario'  => ["DATE_FORMAT(fecha,'%d-%m-%Y')", "fecha", "fecha >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)"],
            'semanal' => ["CONCAT('Sem ', WEEK(fecha,3), ' - ', YEAR(fecha))", "YEARWEEK(fecha,3)", "fecha >= DATE_SUB(CURDATE(), INTERVAL 16 WEEK)"],
            'anual'   => ["YEAR(fecha)", "YEAR(fecha)", "1=1"],
            default   => ["DATE_FORMAT(fecha,'%m-%Y')", "DATE_FORMAT(fecha,'%Y-%m')", "fecha >= DATE_SUB(CURDATE(), INTERVAL 12 MONTH)"],
        };
    }

    public function index(Request $request)
    {
        $periodo = in_array($request->get('periodo'), ['diario','semanal','mensual','anual'])
            ? $request->get('periodo') : 'mensual';
        [$g, $o, $lim] = $this->periodoSql($periodo);

        $flujo = DB::select("
            SELECT periodo,
                   SUM(entradas) AS entradas,
                   SUM(salidas)  AS salidas,
                   SUM(entradas) - SUM(salidas) AS neto
            FROM (
                SELECT $g AS periodo, monto AS entradas, 0 AS salidas, fecha, $o AS ord
                FROM ingreso WHERE $lim
                UNION ALL
                SELECT $g AS periodo, 0 AS entradas, monto AS salidas, fecha, $o AS ord
                FROM gasto WHERE $lim
                UNION ALL
                SELECT $g AS periodo, 0 AS entradas, monto AS salidas, fecha, $o AS ord
                FROM compras_menores WHERE $lim
            ) movimientos
            GROUP BY periodo, ord
            ORDER BY ord
        ");

        $saldoAcumulado = 0;
        foreach ($flujo as $f) {
            $saldoAcumulado += $f->neto;
            $f->saldo_acumulado = $saldoAcumulado;
        }

        // Saldo real de caja (histórico completo, sin límite de periodo)
        $totalIngresos = DB::table('ingreso')->sum('monto');
        $totalGastos   = DB::table('gasto')->sum('monto') + DB::table('compras_menores')->sum('monto');
        $saldoCaja     = $totalIngresos - $totalGastos;

        return view('flujo_caja.index', compact('flujo', 'periodo', 'saldoCaja', 'totalIngresos', 'totalGastos'));
    }
}
