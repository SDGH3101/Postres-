<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Emprendedor extends Model
{
    protected $table      = 'emprendedor';
    protected $primaryKey = 'id_usuario';
    public    $timestamps = false;
    protected $fillable   = ['id_usuario', 'descripcion'];

    public function usuario() { return $this->belongsTo(Usuario::class, 'id_usuario'); }
    public function gastos()  { return $this->hasMany(Gasto::class,  'id_emprendedor', 'id_usuario'); }
    public function compras() { return $this->hasMany(ComprasMenores::class, 'id_emprendedor', 'id_usuario'); }
}
