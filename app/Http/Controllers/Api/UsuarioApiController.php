<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UsuarioApiController extends Controller
{
    public function index()
    {
        $usuarios = DB::select("
            SELECT u.id_usuario, u.nombre, u.correo, u.edad,
                   DATE_FORMAT(u.registro,'%d-%m-%Y') AS registro,
                   CASE WHEN emp.id_usuario IS NOT NULL THEN 'emprendedor'
                        WHEN em.id_usuario  IS NOT NULL THEN 'empleado'
                        WHEN c.id_usuario   IS NOT NULL THEN 'cliente'
                        ELSE 'sin rol' END AS rol
            FROM usuario u
            LEFT JOIN emprendedor emp ON u.id_usuario = emp.id_usuario
            LEFT JOIN empleado    em  ON u.id_usuario = em.id_usuario
            LEFT JOIN cliente     c   ON u.id_usuario = c.id_usuario
            ORDER BY u.id_usuario
        ");
        return response()->json(['data' => $usuarios, 'total' => count($usuarios)]);
    }

    public function show($id)
    {
        $u = DB::table('usuario')->where('id_usuario', $id)->first();
        if (!$u) return response()->json(['error' => 'Usuario no encontrado'], 404);
        return response()->json(['data' => $u]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required', 'correo' => 'required|email|unique:usuario,correo',
            'pass'   => 'required|min:6', 'rol' => 'required|in:cliente,empleado,emprendedor',
        ]);
        $id = DB::table('usuario')->insertGetId([
            'nombre'    => $request->nombre, 'correo' => $request->correo,
            'contrasena'=> Hash::make($request->pass),
            'edad'      => $request->edad ?? null,
            'registro'  => now()->toDateString(),
        ]);
        match($request->rol) {
            'emprendedor' => DB::table('emprendedor')->insert(['id_usuario'=>$id,'descripcion'=>'']),
            'empleado'    => DB::table('empleado')->insert(['id_usuario'=>$id,'cargo'=>'','salario'=>0,'horas_trabajadas'=>0]),
            default       => DB::table('cliente')->insert(['id_usuario'=>$id]),
        };
        return response()->json(['message' => 'Usuario creado', 'id' => $id], 201);
    }

    public function update(Request $request, $id)
    {
        $u = DB::table('usuario')->where('id_usuario', $id)->first();
        if (!$u) return response()->json(['error' => 'No encontrado'], 404);
        $data = array_filter(['nombre' => $request->nombre, 'edad' => $request->edad]);
        if ($request->filled('pass')) $data['contrasena'] = Hash::make($request->pass);
        DB::table('usuario')->where('id_usuario', $id)->update($data);
        return response()->json(['message' => 'Actualizado']);
    }

    public function destroy($id)
    {
        $rows = DB::table('usuario')->where('id_usuario', $id)->delete();
        if (!$rows) return response()->json(['error' => 'No encontrado'], 404);
        return response()->json(['message' => 'Eliminado']);
    }
}
