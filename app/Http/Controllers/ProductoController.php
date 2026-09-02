<?php

namespace App\Http\Controllers;

use App\Models\Producto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProductoController extends Controller
{
    public function index()
    {
        $productos = Producto::orderBy('id_producto')->get();
        return view('productos.index', compact('productos'));
    }

    public function catalogo()
    {
        $productos = Producto::orderBy('descripcion')->get();
        return view('catalogo.index', compact('productos'));
    }

    public function create()
    {
        return view('productos.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'descripcion'     => 'required|max:200',
            'tipo'            => 'required|in:postre,bebida,otro',
            'precio'          => 'required|numeric|min:0',
            'stock'           => 'required|integer|min:0',
            'stock_minimo'    => 'nullable|integer|min:0',
            'fecha_caducidad' => 'nullable|date',
        ]);
        Producto::create($request->only('descripcion', 'tipo', 'precio', 'stock', 'stock_minimo', 'fecha_caducidad'));
        return redirect()->route('productos.index')->with('success', 'Producto agregado.');
    }

    public function show($id)
    {
        $producto = Producto::with('detalleVentas')->findOrFail($id);
        return view('productos.show', compact('producto'));
    }

    public function edit($id)
    {
        $producto = Producto::findOrFail($id);
        return view('productos.edit', compact('producto'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'descripcion'     => 'required|max:200',
            'tipo'            => 'required|in:postre,bebida,otro',
            'precio'          => 'required|numeric|min:0',
            'stock'           => 'required|integer|min:0',
            'stock_minimo'    => 'nullable|integer|min:0',
            'fecha_caducidad' => 'nullable|date',
        ]);
        Producto::findOrFail($id)->update($request->only('descripcion', 'tipo', 'precio', 'stock', 'stock_minimo', 'fecha_caducidad'));
        return redirect()->route('productos.index')->with('success', 'Producto actualizado.');
    }

    public function destroy($id)
    {
        Producto::findOrFail($id)->delete();
        return redirect()->route('productos.index')->with('success', 'Producto eliminado.');
    }
}
