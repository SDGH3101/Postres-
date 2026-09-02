<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Venta extends Model
{
    protected $table      = 'venta';
    protected $primaryKey = 'id_venta';
    public    $timestamps = false;
    protected $fillable   = ['fecha', 'total', 'medio_pago', 'id_empleado'];

    public function empleado() { return $this->belongsTo(Empleado::class, 'id_empleado', 'id_usuario'); }
    public function detalles()  { return $this->hasMany(DetalleVenta::class, 'id_venta'); }
}
