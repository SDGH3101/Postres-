<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Usuario extends Model
{
    protected $table      = 'usuario';
    protected $primaryKey = 'id_usuario';
    public    $timestamps = false;

    protected $fillable = ['nombre', 'correo', 'contrasena', 'edad', 'registro'];

    protected $hidden = ['contrasena'];

    public function emprendedor() { return $this->hasOne(Emprendedor::class, 'id_usuario', 'id_usuario'); }
    public function empleado()    { return $this->hasOne(Empleado::class,    'id_usuario', 'id_usuario'); }
    public function cliente()     { return $this->hasOne(Cliente::class,     'id_usuario', 'id_usuario'); }

    public function getRolAttribute(): string
    {
        if ($this->emprendedor) return 'emprendedor';
        if ($this->empleado)    return 'empleado';
        if ($this->cliente)     return 'cliente';
        return 'sin rol';
    }
}
