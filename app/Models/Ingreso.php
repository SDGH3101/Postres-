<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ingreso extends Model
{
    protected $table      = 'ingreso';
    protected $primaryKey = 'id_ingreso';
    public    $timestamps = false;
    protected $fillable   = ['fecha', 'monto', 'concepto', 'id_producto'];

    public function producto()    { return $this->belongsTo(Producto::class, 'id_producto'); }
}
