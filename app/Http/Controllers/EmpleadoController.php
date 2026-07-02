<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;

class EmpleadoController extends Controller
{
    // sp_listar_empleados() + sp_rendimiento_empleado()
    public function index()
    {
        $empleados = DB::select("
            SELECT u.id_usuario, u.nombre, u.correo,
                   em.cargo, em.salario, em.horas_trabajadas,
                   DATE_FORMAT(u.registro,'%d-%m-%Y') AS fecha_ingreso,
                   DATEDIFF(CURDATE(), u.registro) AS dias_empresa,
                   COUNT(v.id_venta)  AS total_ventas,
                   COALESCE(SUM(v.total), 0) AS ingresos_generados
            FROM empleado em
            JOIN usuario u  ON em.id_usuario = u.id_usuario
            LEFT JOIN venta v ON v.id_empleado = em.id_usuario
            GROUP BY em.id_usuario, u.id_usuario, u.nombre, u.correo, em.cargo, em.salario, em.horas_trabajadas, u.registro
            ORDER BY em.salario DESC
        ");
        return view('empleados.index', compact('empleados'));
    }

    public function show($id)
    {
        // sp_rendimiento_empleado(id)
        $rendimiento = DB::select("
            SELECT u.nombre, em.cargo, em.salario,
                   COUNT(v.id_venta) AS total_ventas,
                   COALESCE(SUM(v.total),0) AS ingresos_generados,
                   COALESCE(AVG(v.total),0) AS promedio_venta,
                   ROUND(COALESCE(SUM(v.total),0) / em.salario, 2) AS ratio
            FROM empleado em
            JOIN usuario u ON em.id_usuario = u.id_usuario
            LEFT JOIN venta v ON v.id_empleado = em.id_usuario
            WHERE em.id_usuario = ?
            GROUP BY em.id_usuario, u.nombre, em.cargo, em.salario
        ", [$id]);
        if (empty($rendimiento)) abort(404);
        return view('empleados.show', ['emp' => $rendimiento[0]]);
    }

    // Stubs requeridos por resource
    public function create()  { return view('empleados.index'); }
    public function store()   { return redirect()->route('empleados.index'); }
    public function edit($id) { return view('empleados.index'); }
    public function update(\Illuminate\Http\Request $r, $id) { return redirect()->route('empleados.index'); }
    public function destroy($id) { return redirect()->route('empleados.index'); }
}
