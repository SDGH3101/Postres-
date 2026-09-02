<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cliente extends Model
{
    protected $table      = 'cliente';
    protected $primaryKey = 'id_usuario';
    public    $timestamps = false;
    protected $fillable   = ['id_usuario'];

    public function usuario() { return $this->belongsTo(Usuario::class, 'id_usuario'); }
}
