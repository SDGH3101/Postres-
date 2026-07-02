<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Gasto extends Model
{
    protected $table      = 'gasto';
    protected $primaryKey = 'id_gasto';
    public    $timestamps = false;
    protected $fillable   = ['descripcion', 'categoria', 'fecha', 'monto', 'presupuesto', 'id_emprendedor'];

    public function emprendedor() { return $this->belongsTo(Emprendedor::class, 'id_emprendedor', 'id_usuario'); }
}
