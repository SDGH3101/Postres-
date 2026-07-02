@extends('layouts.app')
@section('title','Personal')
@section('content')
<div class="sec-hdr">
  <div class="sec-title">👨‍🍳 Gestión de Personal</div>
  <div class="sec-sub">sp_listar_empleados() · sp_rendimiento_empleado()</div>
</div>

<div class="card">
  <div class="card-hdr">
    <div class="card-title">Equipo de Trabajo</div>
    <span class="chip">{{ count($empleados) }} empleados</span>
  </div>
  <table class="tbl">
    <thead>
      <tr>
        <th>Nombre</th><th>Cargo</th><th>Salario</th>
        <th>Hrs. Trabajadas</th><th>Ventas</th><th>Ingresos Generados</th><th>Días en empresa</th><th>Acción</th>
      </tr>
    </thead>
    <tbody>
      @forelse($empleados as $e)
      <tr>
        <td><strong>{{ $e->nombre }}</strong><br><small style="color:#8a6a50">{{ $e->correo }}</small></td>
        <td><span class="chip">{{ $e->cargo ?? 'Sin cargo' }}</span></td>
        <td>${{ number_format($e->salario,0,'.','.') }}</td>
        <td>{{ $e->horas_trabajadas }}h</td>
        <td><span class="badge bg">{{ $e->total_ventas }}</span></td>
        <td style="color:#2d7a4f;font-weight:600">${{ number_format($e->ingresos_generados,0,'.','.') }}</td>
        <td>{{ $e->dias_empresa }} días</td>
        <td><a href="{{ route('empleados.show', $e->id_usuario) }}" class="btn-e">Ver rendimiento</a></td>
      </tr>
      @empty
      <tr><td colspan="8" style="text-align:center;color:#8a6a50;padding:30px">Sin empleados registrados</td></tr>
      @endforelse
    </tbody>
  </table>
</div>
@endsection
