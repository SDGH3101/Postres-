<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Producto extends Model
{
    protected $table      = 'producto';
    protected $primaryKey = 'id_producto';
    public    $timestamps = false;
    protected $fillable   = ['descripcion', 'tipo', 'precio', 'stock', 'fecha_caducidad', 'stock_minimo'];
    protected $casts      = ['fecha_caducidad' => 'date'];
    // Se agrega para que estado_inventario viaje en las respuestas JSON de la
    // API (antes solo se veía en las vistas Blade que lo llamaban a mano).
    protected $appends    = ['estado_inventario'];

    public function detalleVentas() { return $this->hasMany(DetalleVenta::class, 'id_producto'); }
    public function ingresos()      { return $this->hasMany(Ingreso::class, 'id_producto'); }

    // Estado de inventario para alertas visuales
    public function getEstadoInventarioAttribute(): string
    {
        if ($this->fecha_caducidad) {
            $dias = now()->startOfDay()->diffInDays($this->fecha_caducidad, false);
            if ($dias < 0) return 'vencido';
            if ($dias <= 3) return 'por_vencer';
        }
        if ($this->stock <= 0) return 'agotado';
        if ($this->stock <= $this->stock_minimo) return 'bajo';
        return 'ok';
    }

    public function scopePorVencer($query, int $dias = 3)
    {
        return $query->whereNotNull('fecha_caducidad')
            ->whereBetween('fecha_caducidad', [now()->toDateString(), now()->addDays($dias)->toDateString()]);
    }

    public function scopeVencidos($query)
    {
        return $query->whereNotNull('fecha_caducidad')->where('fecha_caducidad', '<', now()->toDateString());
    }

    public function scopeStockBajo($query)
    {
        return $query->whereColumn('stock', '<=', 'stock_minimo');
    }
}
