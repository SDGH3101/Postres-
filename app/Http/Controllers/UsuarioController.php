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
            'nombre'           => 'required|max:100',
            'correo'           => 'required|email|unique:usuario,correo',
            'fecha_nacimiento' => 'required|date|before_or_equal:' . now()->subYears(16)->toDateString() . '|after:' . now()->subYears(99)->toDateString(),
            'pass'             => 'required|min:6',
            'rol'              => 'required|in:cliente,empleado,emprendedor',
        ], [
            'fecha_nacimiento.required'        => 'La fecha de nacimiento es obligatoria.',
            'fecha_nacimiento.before_or_equal' => 'El usuario debe tener al menos 16 años.',
            'fecha_nacimiento.after'           => 'Ingresa una fecha de nacimiento válida.',
        ]);

        $edad = \Carbon\Carbon::parse($request->fecha_nacimiento)->age;

        $id = DB::table('usuario')->insertGetId([
            'nombre'           => $request->nombre,
            'correo'           => $request->correo,
            'edad'             => $edad,
            'fecha_nacimiento' => $request->fecha_nacimiento,
            'contrasena'       => Hash::make($request->pass),
            'registro'         => now()->toDateString(),
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
            'nombre'           => 'required|max:100',
            'fecha_nacimiento' => 'required|date|before_or_equal:' . now()->subYears(16)->toDateString() . '|after:' . now()->subYears(99)->toDateString(),
        ], [
            'fecha_nacimiento.required'        => 'La fecha de nacimiento es obligatoria.',
            'fecha_nacimiento.before_or_equal' => 'El usuario debe tener al menos 16 años.',
            'fecha_nacimiento.after'           => 'Ingresa una fecha de nacimiento válida.',
        ]);
        $data = [
            'nombre'           => $request->nombre,
            'edad'             => \Carbon\Carbon::parse($request->fecha_nacimiento)->age,
            'fecha_nacimiento' => $request->fecha_nacimiento,
        ];
        if ($request->filled('pass')) {
            $data['contrasena'] = Hash::make($request->pass);
        }
        DB::table('usuario')->where('id_usuario', $id)->update($data);
        return redirect()->route('usuarios.index')->with('success', 'Usuario actualizado.');
    }

    public function destroy(Request $request, $id)
    {
        // Regla de negocio: nadie puede borrar la cuenta de otro usuario,
        // ni siquiera el emprendedor/administrador. Solo el dueño de la
        // cuenta puede eliminarla.
        $sesion = $request->session()->get('usuario');
        if (!$sesion || (int) $sesion['id'] !== (int) $id) {
            return redirect()->route('usuarios.index')
                ->withErrors(['delete' => 'Solo el propio usuario puede eliminar su cuenta. El administrador no tiene permiso para borrar cuentas ajenas.']);
        }

        DB::table('usuario')->where('id_usuario', $id)->delete();

        // Si el usuario se borró a sí mismo, cerrar su sesión también.
        if ((int) $sesion['id'] === (int) $id) {
            $request->session()->forget('usuario');
            return redirect()->route('login')->with('success', 'Tu cuenta fue eliminada correctamente.');
        }

        return redirect()->route('usuarios.index')->with('success', 'Usuario eliminado.');
    }
}
