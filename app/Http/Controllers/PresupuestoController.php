<?php

namespace App\Http\Controllers;

use App\Models\Presupuesto;
use Illuminate\Http\Request;

class PresupuestoController extends Controller
{
    public function index(Request $request)
    {
        $idEmprendedor = $request->session()->get('usuario')['id'];
        $presupuestos = Presupuesto::where('id_emprendedor', $idEmprendedor)
            ->where('activo', true)
            ->orderBy('tipo')
            ->get()
            ->map(function ($p) {
                [$ini, $fin] = $p->rangoActual();
                $p->gasto_real = $p->gastoReal();
                $p->porcentaje = $p->porcentajeUsado();
                $p->rango_ini  = $ini;
                $p->rango_fin  = $fin;
                return $p;
            });

        return view('presupuestos.index', compact('presupuestos'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'tipo'          => 'required|in:diario,semanal,mensual,anual',
            'monto'         => 'required|numeric|min:1',
            'fecha_inicio'  => 'required|date',
        ]);

        Presupuesto::create([
            'id_emprendedor' => $request->session()->get('usuario')['id'],
            'tipo'           => $request->tipo,
            'monto'          => $request->monto,
            'fecha_inicio'   => $request->fecha_inicio,
        ]);

        return redirect()->route('presupuestos.index')->with('success', 'Presupuesto fijado correctamente.');
    }

    public function destroy(Request $request, $id)
    {
        $idEmprendedor = $request->session()->get('usuario')['id'];
        Presupuesto::where('id_presupuesto', $id)->where('id_emprendedor', $idEmprendedor)->delete();
        return redirect()->route('presupuestos.index')->with('success', 'Presupuesto eliminado.');
    }
}
