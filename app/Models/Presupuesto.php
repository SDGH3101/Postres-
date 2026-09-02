<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class Presupuesto extends Model
{
    protected $table      = 'presupuesto';
    protected $primaryKey = 'id_presupuesto';
    const CREATED_AT = 'creado_en';
    const UPDATED_AT = null;

    protected $fillable = ['id_emprendedor', 'tipo', 'monto', 'fecha_inicio', 'activo'];
    protected $casts    = ['fecha_inicio' => 'date', 'activo' => 'boolean'];

    // Rango [inicio, fin] del periodo vigente MÁS CERCANO a hoy, a partir de fecha_inicio
    public function rangoActual(): array
    {
        $inicio = Carbon::parse($this->fecha_inicio);
        $hoy    = now()->startOfDay();

        [$unidad, $cantidad] = match ($this->tipo) {
            'diario'  => ['day', 1],
            'semanal' => ['week', 1],
            'mensual' => ['month', 1],
            'anual'   => ['year', 1],
        };

        // Avanza el periodo hacia adelante hasta cubrir "hoy"
        $cursor = $inicio->copy();
        while ($cursor->copy()->add($unidad, $cantidad) <= $hoy) {
            $cursor->add($unidad, $cantidad);
        }
        $fin = $cursor->copy()->add($unidad, $cantidad)->subDay();

        return [$cursor->toDateString(), $fin->toDateString()];
    }

    public function gastoReal(): float
    {
        [$ini, $fin] = $this->rangoActual();
        $gastos  = DB::table('gasto')->whereBetween('fecha', [$ini, $fin])
            ->where('id_emprendedor', $this->id_emprendedor)->sum('monto');
        $compras = DB::table('compras_menores')->whereBetween('fecha', [$ini, $fin])
            ->where('id_emprendedor', $this->id_emprendedor)->sum('monto');
        return (float) $gastos + (float) $compras;
    }

    public function porcentajeUsado(): float
    {
        if ((float) $this->monto <= 0) return 0;
        return round(($this->gastoReal() / (float) $this->monto) * 100, 1);
    }
}
