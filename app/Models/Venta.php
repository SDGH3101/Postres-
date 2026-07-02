<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Venta extends Model
{
    protected $table      = 'venta';
    protected $primaryKey = 'id_venta';
    public    $timestamps = false;
    protected $fillable   = ['fecha', 'total', 'id_empleado', 'id_producto'];

    public function empleado() { return $this->belongsTo(Empleado::class, 'id_empleado', 'id_usuario'); }
    public function producto() { return $this->belongsTo(Producto::class, 'id_producto'); }
}
