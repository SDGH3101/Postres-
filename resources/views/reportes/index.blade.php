@extends('layouts.app')
@section('title','Reportes')
@section('content')
<div class="sec-hdr">
  <div class="sec-title">📈 Reportes y Análisis</div>
  <div class="sec-sub">sp_ventas_por_mes() · sp_gastos_por_categoria() · sp_productos_mas_vendidos() · sp_balance_ventas_gastos()</div>
</div>

<div style="display:flex;gap:12px;margin-bottom:24px">
  <a href="{{ route('reportes.ventas.pdf') }}" class="btn-p">📄 Exportar Ventas PDF</a>
  <a href="{{ route('reportes.gastos.pdf') }}"  class="btn-p" style="background:linear-gradient(135deg,#c9717a,#e8a84c)">📄 Exportar Gastos PDF</a>
  <a href="{{ route('reportes.excel') }}"        class="btn-p" style="background:linear-gradient(135deg,#2d7a4f,#6b8c6e)">📊 Exportar Excel (CSV)</a>
</div>

{{-- VENTAS POR MES --}}
<div class="card" style="margin-bottom:22px">
  <div class="card-hdr">
    <div class="card-title">📅 Ventas por mes</div>
    <span class="chip">sp_ventas_por_mes()</span>
  </div>
  <table class="tbl">
    <thead><tr><th>Mes</th><th>N° Ventas</th><th>Ingresos</th><th>Ticket Promedio</th></tr></thead>
    <tbody>
      @forelse($ventasMes as $v)
      <tr>
        <td>{{ $v->mes }}</td>
        <td><span class="badge bg">{{ $v->total_ventas }}</span></td>
        <td style="color:#2d7a4f;font-weight:600">${{ number_format($v->ingresos_mes,0,'.','.') }}</td>
        <td>${{ number_format($v->ticket_promedio,0,'.','.') }}</td>
      </tr>
      @empty
      <tr><td colspan="4" style="text-align:center;color:#8a6a50">Sin datos</td></tr>
      @endforelse
    </tbody>
  </table>
</div>

{{-- GASTOS POR CATEGORÍA --}}
<div style="display:grid;grid-template-columns:1fr 1fr;gap:18px;margin-bottom:22px">
  <div class="card">
    <div class="card-hdr">
      <div class="card-title">💸 Gastos por Categoría</div>
      <span class="chip">sp_gastos_por_categoria()</span>
    </div>
    <table class="tbl">
      <thead><tr><th>Categoría</th><th>Cantidad</th><th>Total</th></tr></thead>
      <tbody>
        @forelse($gastosCat as $g)
        <tr>
          <td><span class="chip">{{ $g->categoria }}</span></td>
          <td>{{ $g->cantidad }}</td>
          <td style="color:#c0392b;font-weight:600">-${{ number_format($g->total,0,'.','.') }}</td>
        </tr>
        @empty
        <tr><td colspan="3" style="text-align:center;color:#8a6a50">Sin datos</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>

  {{-- PRODUCTOS MÁS VENDIDOS --}}
  <div class="card">
    <div class="card-hdr">
      <div class="card-title">🏆 Productos más vendidos</div>
      <span class="chip">sp_productos_mas_vendidos()</span>
    </div>
    <table class="tbl">
      <thead><tr><th>Producto</th><th>Stock</th><th>Veces</th><th>Ingresos</th></tr></thead>
      <tbody>
        @forelse($masVendidos as $p)
        <tr>
          <td>{{ $p->descripcion }}</td>
          <td>{{ $p->stock }}</td>
          <td><span class="badge bg">{{ $p->veces }}</span></td>
          <td style="color:#2d7a4f;font-weight:600">${{ number_format($p->ingresos,0,'.','.') }}</td>
        </tr>
        @empty
        <tr><td colspan="4" style="text-align:center;color:#8a6a50">Sin datos</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
</div>

{{-- BALANCE --}}
<div class="card">
  <div class="card-hdr">
    <div class="card-title">⚖️ Balance Ventas vs Gastos</div>
    <span class="chip">sp_balance_ventas_gastos()</span>
  </div>
  <table class="tbl">
    <thead><tr><th>Mes</th><th>Ingresos</th><th>Gastos</th><th>Balance</th></tr></thead>
    <tbody>
      @forelse($balance as $b)
      <tr>
        <td>{{ $b->mes }}</td>
        <td style="color:#2d7a4f;font-weight:600">${{ number_format($b->ingresos,0,'.','.') }}</td>
        <td style="color:#c0392b;font-weight:600">-${{ number_format($b->gastos,0,'.','.') }}</td>
        <td style="font-weight:700;color:{{ $b->balance >= 0 ? '#2d7a4f' : '#c0392b' }}">
          {{ $b->balance >= 0 ? '+' : '' }}${{ number_format($b->balance,0,'.','.') }}
        </td>
      </tr>
      @empty
      <tr><td colspan="4" style="text-align:center;color:#8a6a50">Sin datos</td></tr>
      @endforelse
    </tbody>
  </table>
</div>
@endsection
