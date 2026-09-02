<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class EmpleadoController extends Controller
{
    // Reglas de validación compartidas para fecha_nacimiento (16-99 años)
    private function reglasFechaNacimiento(): array
    {
        return [
            'required',
            'date',
            'before_or_equal:' . now()->subYears(16)->toDateString(),
            'after:' . now()->subYears(99)->toDateString(),
        ];
    }

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

    public function create()
    {
        return view('empleados.create');
    }

    // Crea la cuenta (usuario) + el registro de empleado en una sola operación
    public function store(Request $request)
    {
        $request->validate([
            'nombre'           => 'required|max:100',
            'correo'           => 'required|email|unique:usuario,correo',
            'fecha_nacimiento' => $this->reglasFechaNacimiento(),
            'pass'             => 'required|min:6',
            'cargo'            => 'required|max:80',
            'salario'          => 'required|numeric|min:0',
            'horas_trabajadas' => 'required|integer|min:0',
        ], [
            'fecha_nacimiento.required'        => 'La fecha de nacimiento es obligatoria.',
            'fecha_nacimiento.before_or_equal' => 'El empleado debe tener al menos 16 años.',
            'fecha_nacimiento.after'           => 'Ingresa una fecha de nacimiento válida.',
        ]);

        $edad = \Carbon\Carbon::parse($request->fecha_nacimiento)->age;

        DB::transaction(function () use ($request, $edad) {
            $id = DB::table('usuario')->insertGetId([
                'nombre'           => $request->nombre,
                'correo'           => $request->correo,
                'edad'             => $edad,
                'fecha_nacimiento' => $request->fecha_nacimiento,
                'contrasena'       => Hash::make($request->pass),
                'registro'         => now()->toDateString(),
            ]);

            DB::table('empleado')->insert([
                'id_usuario'       => $id,
                'cargo'            => $request->cargo,
                'salario'          => $request->salario,
                'horas_trabajadas' => $request->horas_trabajadas,
            ]);
        });

        return redirect()->route('empleados.index')->with('success', 'Empleado agregado correctamente.');
    }

    public function edit($id)
    {
        $empleado = DB::table('empleado')
            ->join('usuario', 'empleado.id_usuario', '=', 'usuario.id_usuario')
            ->where('empleado.id_usuario', $id)
            ->select('usuario.id_usuario', 'usuario.nombre', 'usuario.correo', 'usuario.fecha_nacimiento',
                     'empleado.cargo', 'empleado.salario', 'empleado.horas_trabajadas')
            ->first();
        if (!$empleado) abort(404);
        return view('empleados.edit', compact('empleado'));
    }

    public function update(Request $request, $id)
    {
        $existe = DB::table('empleado')->where('id_usuario', $id)->exists();
        if (!$existe) abort(404);

        $request->validate([
            'nombre'           => 'required|max:100',
            'correo'           => 'required|email|unique:usuario,correo,' . $id . ',id_usuario',
            'fecha_nacimiento' => $this->reglasFechaNacimiento(),
            'pass'             => 'nullable|min:6',
            'cargo'            => 'required|max:80',
            'salario'          => 'required|numeric|min:0',
            'horas_trabajadas' => 'required|integer|min:0',
        ], [
            'fecha_nacimiento.required'        => 'La fecha de nacimiento es obligatoria.',
            'fecha_nacimiento.before_or_equal' => 'El empleado debe tener al menos 16 años.',
            'fecha_nacimiento.after'           => 'Ingresa una fecha de nacimiento válida.',
        ]);

        DB::transaction(function () use ($request, $id) {
            $datosUsuario = [
                'nombre'           => $request->nombre,
                'correo'           => $request->correo,
                'edad'             => \Carbon\Carbon::parse($request->fecha_nacimiento)->age,
                'fecha_nacimiento' => $request->fecha_nacimiento,
            ];
            if ($request->filled('pass')) {
                $datosUsuario['contrasena'] = Hash::make($request->pass);
            }
            DB::table('usuario')->where('id_usuario', $id)->update($datosUsuario);

            DB::table('empleado')->where('id_usuario', $id)->update([
                'cargo'            => $request->cargo,
                'salario'          => $request->salario,
                'horas_trabajadas' => $request->horas_trabajadas,
            ]);
        });

        return redirect()->route('empleados.index')->with('success', 'Empleado actualizado.');
    }

    // Quita el rol de empleado (no borra la cuenta de usuario). Protegido
    // si tiene ventas registradas, para no perder el historial de ventas
    // (venta.id_empleado -> empleado.id_usuario es FK RESTRICT).
    public function destroy($id)
    {
        $existe = DB::table('empleado')->where('id_usuario', $id)->exists();
        if (!$existe) abort(404);

        $totalVentas = DB::table('venta')->where('id_empleado', $id)->count();
        if ($totalVentas > 0) {
            return redirect()->route('empleados.index')->withErrors([
                'delete' => "No se puede eliminar: tiene {$totalVentas} venta(s) registrada(s). Se protege para conservar el historial de ventas.",
            ]);
        }

        DB::table('empleado')->where('id_usuario', $id)->delete();
        return redirect()->route('empleados.index')->with('success', 'Empleado eliminado. La cuenta de usuario se conserva sin rol asignado.');
    }
}
