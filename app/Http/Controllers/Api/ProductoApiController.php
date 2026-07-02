<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Producto;
use Illuminate\Http\Request;

class ProductoApiController extends Controller
{
    public function index()
    {
        return response()->json(['data' => Producto::all()]);
    }

    public function show($id)
    {
        $p = Producto::find($id);
        if (!$p) return response()->json(['error' => 'No encontrado'], 404);
        return response()->json(['data' => $p]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'descripcion' => 'required|max:200',
            'precio'      => 'required|numeric|min:0',
            'stock'       => 'required|integer|min:0',
        ]);
        $p = Producto::create($request->only('descripcion', 'precio', 'stock'));
        return response()->json(['message' => 'Producto creado', 'data' => $p], 201);
    }

    public function update(Request $request, $id)
    {
        $p = Producto::find($id);
        if (!$p) return response()->json(['error' => 'No encontrado'], 404);
        $p->update($request->only('descripcion', 'precio', 'stock'));
        return response()->json(['message' => 'Actualizado', 'data' => $p]);
    }

    public function destroy($id)
    {
        $p = Producto::find($id);
        if (!$p) return response()->json(['error' => 'No encontrado'], 404);
        $p->delete();
        return response()->json(['message' => 'Eliminado']);
    }
}
