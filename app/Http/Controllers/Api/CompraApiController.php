<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CompraApiController extends Controller
{
    public function index()
    {
        $gastos = DB::table('gasto')->orderByDesc('fecha')->get();
        return response()->json(['data' => $gastos, 'total' => $gastos->sum('monto')]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'descripcion' => 'required', 'fecha' => 'required|date', 'monto' => 'required|numeric|min:1',
        ]);
        $empId = DB::table('emprendedor')->value('id_usuario');
        $id = DB::table('gasto')->insertGetId([
            'descripcion'    => $request->descripcion,
            'categoria'      => $request->categoria ?? 'Otros',
            'fecha'          => $request->fecha,
            'monto'          => $request->monto,
            'presupuesto'    => $request->presupuesto ?? $request->monto,
            'id_emprendedor' => $empId,
        ]);
        return response()->json(['message' => 'Gasto registrado', 'id' => $id], 201);
    }

    public function destroy($id)
    {
        $rows = DB::table('gasto')->where('id_gasto', $id)->delete();
        if (!$rows) return response()->json(['error' => 'No encontrado'], 404);
        return response()->json(['message' => 'Eliminado']);
    }
}
