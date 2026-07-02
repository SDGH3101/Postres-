@extends('layouts.app')
@section('title','Dashboard')
@section('content')

<div class="hero">
  <h2>¡Bienvenid@, {{ explode(' ', session('usuario.nombre'))[0] }}! 🎂</h2>
  <p>Aquí tienes el resumen del estado actual de Postres Laura — {{ now()->format('F Y') }}</p>
</div>

<div class="stats-row">
  <div class="stat c1">
    <div class="ic">💰</div>
    <div class="val">${{ number_format($totalIngresos/1000,0,'.','.') }}K</div>
    <div class="lbl">Ingresos totales</div>
  </div>
  <div class="stat c2">
    <div class="ic">📋</div>
    <div class="val">${{ number_format($totalGastos/1000,0,'.','.') }}K</div>
    <div class="lbl">Gastos totales</div>
  </div>
  <div class="stat c3">
    <div class="ic">🛍️</div>
    <div class="val">{{ count($ventas) }}</div>
    <div class="lbl">Últimas ventas</div>
  </div>
  <div class="stat c4">
    <div class="ic">🍰</div>
    <div class="val">{{ $totalProductos }}</div>
    <div class="lbl">Productos activos</div>
  </div>
</div>

@if(count($ventasMes) > 0)
<div class="card">
  <div class="card-hdr">
    <div class="card-title">📊 Ventas por mes</div>
    <span class="chip">{{ count($ventasMes) }} meses</span>
  </div>
  <div style="padding:22px 26px">
    @php $maxVenta = max(array_column((array)$ventasMes,'total')); @endphp
    @foreach($ventasMes as $vm)
    <div class="bar-row">
      <div class="bar-lbl">{{ $vm->mes }}</div>
      <div class="bar-track">
        <div class="bar-fill" style="width:{{ $maxVenta > 0 ? round(($vm->total/$maxVenta)*100) : 0 }}%"></div>
      </div>
      <div class="bar-val">${{ number_format($vm->total,0,'.','.') }}</div>
    </div>
    @endforeach
  </div>
</div>
@endif

<div style="display:grid;grid-template-columns:1fr 1fr;gap:18px">

  <div class="card">
    <div class="card-hdr">
      <div class="card-title">🏆 Productos más vendidos</div>
      <span class="chip">sp_productos_mas_vendidos()</span>
    </div>
    <table class="tbl">
      <thead><tr><th>Producto</th><th>Veces</th><th>Ingresos</th></tr></thead>
      <tbody>
        @forelse($masVendidos as $p)
        <tr>
          <td>{{ $p->descripcion }}</td>
          <td><span class="badge bg">{{ $p->veces }}</span></td>
          <td>${{ number_format($p->ingresos,0,'.','.') }}</td>
        </tr>
        @empty
        <tr><td colspan="3" style="text-align:center;color:#8a6a50">Sin datos</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>

  <div class="card">
    <div class="card-hdr">
      <div class="card-title">🕒 Últimas ventas</div>
      @if(session('usuario.rol') !== 'cliente')
      <a href="{{ route('ventas.index') }}" class="chip">Ver todas →</a>
      @endif
    </div>
    <table class="tbl">
      <thead><tr><th>Fecha</th><th>Producto</th><th>Total</th></tr></thead>
      <tbody>
        @forelse($ventas as $v)
        <tr>
          <td>{{ $v->fecha }}</td>
          <td>{{ $v->producto }}</td>
          <td>${{ number_format($v->total,0,'.','.') }}</td>
        </tr>
        @empty
        <tr><td colspan="3" style="text-align:center;color:#8a6a50">Sin ventas registradas</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>

</div>
@endsection
