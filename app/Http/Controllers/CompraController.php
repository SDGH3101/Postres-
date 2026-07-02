<?php

namespace App\Http\Controllers;

use App\Models\Gasto;
use App\Models\ComprasMenores;
use App\Models\Categoria;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CompraController extends Controller
{
    public function index()
    {
        // sp_listar_gastos() + sp_listar_compras_menores()
        $gastos = DB::select("
            SELECT g.id_gasto, DATE_FORMAT(g.fecha,'%d-%m-%Y') AS fecha,
                   g.descripcion, g.categoria, g.monto, g.presupuesto,
                   g.monto - g.presupuesto AS diferencia, u.nombre AS emprendedor
            FROM gasto g
            JOIN emprendedor emp ON g.id_emprendedor = emp.id_usuario
            JOIN usuario u ON emp.id_usuario = u.id_usuario
            ORDER BY g.fecha DESC
        ");
        $compras = DB::select("
            SELECT cm.id_compra, DATE_FORMAT(cm.fecha,'%d-%m-%Y') AS fecha,
                   cm.descripcion, cm.monto, cat.nombre_categoria AS categoria
            FROM compras_menores cm
            JOIN categoria cat ON cm.id_categoria = cat.id_categoria
            ORDER BY cm.fecha DESC
        ");
        $categorias = Categoria::all();
        $totalGastos = DB::table('gasto')->sum('monto')
                     + DB::table('compras_menores')->sum('monto');
        return view('compras.index', compact('gastos', 'compras', 'categorias', 'totalGastos'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'tipo'        => 'required|in:gasto,compra',
            'descripcion' => 'required|max:200',
            'fecha'       => 'required|date',
            'monto'       => 'required|numeric|min:1',
        ]);

        $emprendedorId = DB::table('emprendedor')->value('id_usuario');

        if ($request->tipo === 'gasto') {
            DB::table('gasto')->insert([
                'descripcion'    => $request->descripcion,
                'categoria'      => $request->categoria ?? 'Otros',
                'fecha'          => $request->fecha,
                'monto'          => $request->monto,
                'presupuesto'    => $request->presupuesto ?? $request->monto,
                'id_emprendedor' => $emprendedorId,
            ]);
        } else {
            DB::table('compras_menores')->insert([
                'descripcion'    => $request->descripcion,
                'fecha'          => $request->fecha,
                'monto'          => $request->monto,
                'id_emprendedor' => $emprendedorId,
                'id_categoria'   => $request->id_categoria ?? 7,
            ]);
        }
        return redirect()->route('compras.index')->with('success', 'Egreso registrado correctamente.');
    }

    public function destroy($id)
    {
        DB::table('gasto')->where('id_gasto', $id)->delete();
        return redirect()->route('compras.index')->with('success', 'Gasto eliminado.');
    }

    // Editar gasto
    public function edit($id)
    {
        $gasto = DB::table('gasto')->where('id_gasto', $id)->first();
        if (!$gasto) abort(404);
        return view('compras.edit', compact('gasto'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'descripcion' => 'required|max:200',
            'monto'       => 'required|numeric|min:1',
            'fecha'       => 'required|date',
        ]);
        DB::table('gasto')->where('id_gasto', $id)->update([
            'descripcion' => $request->descripcion,
            'categoria'   => $request->categoria ?? 'Otros',
            'fecha'       => $request->fecha,
            'monto'       => $request->monto,
            'presupuesto' => $request->presupuesto ?? $request->monto,
        ]);
        return redirect()->route('compras.index')->with('success', 'Gasto actualizado.');
    }

    // Requerido por resource (aunque no usemos create separado)
    public function create() { return view('compras.create', ['categorias' => Categoria::all()]); }
    public function show($id) { abort(404); }
}
