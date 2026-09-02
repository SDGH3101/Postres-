@extends('layouts.app')
@section('title','Detalle Producto')
@section('content')
@php
  $estado = $producto->estado_inventario;
  $badge = match($estado) {
    'vencido'    => ['br', '⛔ Vencido'],
    'por_vencer' => ['bo', '⚠️ Por vencer'],
    'agotado'    => ['br', '❌ Agotado'],
    'bajo'       => ['bo', '⚠️ Stock bajo'],
    default      => ['bg', '✅ OK'],
  };
  $unidadesVendidas = $producto->detalleVentas->sum('cantidad');
  $ingresosGenerados = $producto->detalleVentas->sum('subtotal');
@endphp

<div class="sec-hdr">
  <div class="sec-title">🍰 {{ $producto->descripcion }}</div>
  <div class="sec-sub">Detalle e historial de ventas del producto #{{ $producto->id_producto }}</div>
</div>

<div class="stats-row">
  <div class="stat c1">
    <div class="ic">💲</div>
    <div class="val">${{ number_format($producto->precio,0,'.','.') }}</div>
    <div class="lbl">Precio unitario</div>
  </div>
  <div class="stat c3">
    <div class="ic">📦</div>
    <div class="val">{{ $producto->stock }}</div>
    <div class="lbl">Stock actual (mín. {{ $producto->stock_minimo }})</div>
  </div>
  <div class="stat c4">
    <div class="ic">🛍️</div>
    <div class="val">{{ $unidadesVendidas }}</div>
    <div class="lbl">Unidades vendidas</div>
  </div>
  <div class="stat c2">
    <div class="ic">💰</div>
    <div class="val">${{ number_format($ingresosGenerados,0,'.','.') }}</div>
    <div class="lbl">Ingresos generados</div>
  </div>
</div>

<div class="card">
  <div class="card-hdr"><div class="card-title">Datos del producto</div></div>
  <div style="padding:24px 28px;display:grid;grid-template-columns:1fr 1fr;gap:18px">
    <div><span style="color:#8a6a50;font-size:.8rem">Descripción</span>
      <p style="font-weight:600;margin-top:4px">{{ $producto->descripcion }}</p></div>
    <div><span style="color:#8a6a50;font-size:.8rem">Precio</span>
      <p style="font-weight:600;margin-top:4px;color:#c8773a">${{ number_format($producto->precio,0,'.','.') }}</p></div>
    <div><span style="color:#8a6a50;font-size:.8rem">Stock / mínimo</span>
      <p style="font-weight:600;margin-top:4px">{{ $producto->stock }} unds (mín. {{ $producto->stock_minimo }})</p></div>
    <div><span style="color:#8a6a50;font-size:.8rem">Fecha de caducidad</span>
      <p style="font-weight:600;margin-top:4px">{{ $producto->fecha_caducidad ? $producto->fecha_caducidad->format('d-m-Y') : '—' }}</p></div>
    <div><span style="color:#8a6a50;font-size:.8rem">Estado de inventario</span>
      <p style="margin-top:4px"><span class="badge {{ $badge[0] }}">{{ $badge[1] }}</span></p></div>
  </div>
</div>

<div class="card" style="margin-top:22px">
  <div class="card-hdr">
    <div class="card-title">🧾 Historial de ventas</div>
    <span class="chip">{{ $producto->detalleVentas->count() }} registros</span>
  </div>
  <table class="tbl">
    <thead><tr><th>ID Venta</th><th>Fecha</th><th>Cantidad</th><th>Precio unitario</th><th>Subtotal</th></tr></thead>
    <tbody>
      @forelse($producto->detalleVentas->sortByDesc('id_venta') as $d)
      <tr>
        <td>#{{ $d->id_venta }}</td>
        <td>{{ optional($d->venta)->fecha ? \Carbon\Carbon::parse($d->venta->fecha)->format('d-m-Y') : '—' }}</td>
        <td>{{ $d->cantidad }}</td>
        <td>${{ number_format($d->precio_unitario,0,'.','.') }}</td>
        <td style="color:#2d7a4f;font-weight:600">${{ number_format($d->subtotal,0,'.','.') }}</td>
      </tr>
      @empty
      <tr><td colspan="5" style="text-align:center;color:#8a6a50">Este producto todavía no registra ventas</td></tr>
      @endforelse
    </tbody>
  </table>
</div>

<div style="margin-top:18px">
  <a href="{{ route('productos.index') }}" class="btn-p">← Volver a Inventario</a>
</div>
@endsection
