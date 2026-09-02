@extends('layouts.app')
@section('title','Rendimiento Empleado')
@section('content')
<div class="sec-hdr">
  <div class="sec-title">📊 Rendimiento: {{ $emp->nombre }}</div>
  <div class="sec-sub">sp_rendimiento_empleado({{ request()->route('empleado') }})</div>
</div>

<div class="stats-row">
  <div class="stat c1">
    <div class="ic">💰</div>
    <div class="val">${{ number_format($emp->ingresos_generados/1000,0,'.','.') }}K</div>
    <div class="lbl">Ingresos generados</div>
  </div>
  <div class="stat c3">
    <div class="ic">🛍️</div>
    <div class="val">{{ $emp->total_ventas }}</div>
    <div class="lbl">Total ventas</div>
  </div>
  <div class="stat c4">
    <div class="ic">📈</div>
    <div class="val">${{ number_format($emp->promedio_venta,0,'.','.') }}</div>
    <div class="lbl">Ticket promedio</div>
  </div>
  <div class="stat c2">
    <div class="ic">⚡</div>
    <div class="val">{{ $emp->ratio }}</div>
    <div class="lbl">Ratio salario/ventas</div>
  </div>
</div>

<div class="card">
  <div class="card-hdr"><div class="card-title">Datos del empleado</div></div>
  <div style="padding:24px 28px;display:grid;grid-template-columns:1fr 1fr;gap:18px">
    <div><span style="color:#8a6a50;font-size:.8rem">Nombre</span><p style="font-weight:600;margin-top:4px">{{ $emp->nombre }}</p></div>
    <div><span style="color:#8a6a50;font-size:.8rem">Cargo</span><p style="font-weight:600;margin-top:4px">{{ $emp->cargo }}</p></div>
    <div><span style="color:#8a6a50;font-size:.8rem">Salario mensual</span><p style="font-weight:600;margin-top:4px;color:#c8773a">${{ number_format($emp->salario,0,'.','.') }}</p></div>
    <div><span style="color:#8a6a50;font-size:.8rem">Ratio productividad</span><p style="font-weight:600;margin-top:4px;color:#2d7a4f">{{ $emp->ratio }}x</p></div>
  </div>
</div>

<div style="margin-top:18px">
  <a href="{{ route('empleados.index') }}" class="btn-p">← Volver a Personal</a>
</div>
@endsection
