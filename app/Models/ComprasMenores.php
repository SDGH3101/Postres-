<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ComprasMenores extends Model
{
    protected $table      = 'compras_menores';
    protected $primaryKey = 'id_compra';
    public    $timestamps = false;
    protected $fillable   = ['descripcion', 'fecha', 'monto', 'id_emprendedor', 'id_categoria'];

    public function emprendedor() { return $this->belongsTo(Emprendedor::class, 'id_emprendedor', 'id_usuario'); }
    public function categoria()   { return $this->belongsTo(Categoria::class,   'id_categoria'); }
}
