<?php

namespace App\Http\Controllers;

use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UsuarioController extends Controller
{
    // sp_listar_usuarios()
    public function index()
    {
        $usuarios = DB::select("
            SELECT u.id_usuario, u.nombre, u.correo, u.edad,
                   DATE_FORMAT(u.registro,'%d-%m-%Y') AS fecha_registro,
                   CASE
                       WHEN emp.id_usuario IS NOT NULL THEN 'emprendedor'
                       WHEN em.id_usuario  IS NOT NULL THEN 'empleado'
                       WHEN c.id_usuario   IS NOT NULL THEN 'cliente'
                       ELSE 'sin rol'
                   END AS rol,
                   emp.descripcion AS empresa, em.cargo
            FROM usuario u
            LEFT JOIN emprendedor emp ON u.id_usuario = emp.id_usuario
            LEFT JOIN empleado    em  ON u.id_usuario = em.id_usuario
            LEFT JOIN cliente     c   ON u.id_usuario = c.id_usuario
            ORDER BY u.id_usuario
        ");
        return view('usuarios.index', compact('usuarios'));
    }

    public function create()
    {
        return view('usuarios.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|max:100',
            'correo' => 'required|email|unique:usuario,correo',
            'edad'   => 'required|integer|min:16',
            'pass'   => 'required|min:6',
            'rol'    => 'required|in:cliente,empleado,emprendedor',
        ]);

        $id = DB::table('usuario')->insertGetId([
            'nombre'    => $request->nombre,
            'correo'    => $request->correo,
            'edad'      => $request->edad,
            'contrasena'=> Hash::make($request->pass),
            'registro'  => now()->toDateString(),
        ]);

        match ($request->rol) {
            'emprendedor' => DB::table('emprendedor')->insert(['id_usuario' => $id, 'descripcion' => 'Postres Laura']),
            'empleado'    => DB::table('empleado')->insert(['id_usuario' => $id, 'cargo' => 'Empleado', 'salario' => 0, 'horas_trabajadas' => 0]),
            default       => DB::table('cliente')->insert(['id_usuario' => $id]),
        };

        return redirect()->route('usuarios.index')->with('success', 'Usuario registrado correctamente.');
    }

    // sp_buscar_usuario()
    public function show($id)
    {
        $usuario = DB::select("
            SELECT u.*, CASE
                WHEN emp.id_usuario IS NOT NULL THEN 'emprendedor'
                WHEN em.id_usuario  IS NOT NULL THEN 'empleado'
                WHEN c.id_usuario   IS NOT NULL THEN 'cliente'
                ELSE 'sin rol' END AS rol,
                emp.descripcion AS empresa, em.cargo, em.salario
            FROM usuario u
            LEFT JOIN emprendedor emp ON u.id_usuario = emp.id_usuario
            LEFT JOIN empleado    em  ON u.id_usuario = em.id_usuario
            LEFT JOIN cliente     c   ON u.id_usuario = c.id_usuario
            WHERE u.id_usuario = ?
        ", [$id]);

        if (empty($usuario)) abort(404);
        return view('usuarios.show', ['usuario' => $usuario[0]]);
    }

    public function edit($id)
    {
        $usuario = DB::table('usuario')->where('id_usuario', $id)->first();
        if (!$usuario) abort(404);
        return view('usuarios.edit', compact('usuario'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nombre' => 'required|max:100',
            'edad'   => 'required|integer|min:16',
        ]);
        $data = ['nombre' => $request->nombre, 'edad' => $request->edad];
        if ($request->filled('pass')) {
            $data['contrasena'] = Hash::make($request->pass);
        }
        DB::table('usuario')->where('id_usuario', $id)->update($data);
        return redirect()->route('usuarios.index')->with('success', 'Usuario actualizado.');
    }

    public function destroy($id)
    {
        DB::table('usuario')->where('id_usuario', $id)->delete();
        return redirect()->route('usuarios.index')->with('success', 'Usuario eliminado.');
    }
}
