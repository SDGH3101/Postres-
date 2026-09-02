<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Empleado extends Model
{
    protected $table      = 'empleado';
    protected $primaryKey = 'id_usuario';
    public    $timestamps = false;
    protected $fillable   = ['id_usuario', 'cargo', 'salario', 'horas_trabajadas'];

    public function usuario() { return $this->belongsTo(Usuario::class, 'id_usuario'); }
    public function ventas()  { return $this->hasMany(Venta::class, 'id_empleado', 'id_usuario'); }
}

