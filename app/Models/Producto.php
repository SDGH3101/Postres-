<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Producto extends Model
{
    protected $table      = 'producto';
    protected $primaryKey = 'id_producto';
    public    $timestamps = false;
    protected $fillable   = ['descripcion', 'precio', 'stock'];

    public function ventas()   { return $this->hasMany(Venta::class,   'id_producto'); }
    public function ingresos() { return $this->hasMany(Ingreso::class, 'id_producto'); }
}
