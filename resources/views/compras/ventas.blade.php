@extends('layouts.app')
@section('title','Ventas')
@section('content')
<div class="sec-hdr">
  <div class="sec-title">💰 Registro de Ventas</div>
  <div class="sec-sub">sp_listar_ventas() · trg_descontar_stock · trg_ingreso_por_venta</div>
</div>

<div class="fcard">
  <div class="fcard-title">➕ Registrar Nueva Venta</div>
  <form method="POST" action="{{ route('ventas.store') }}">
    @csrf
    <div class="fgrid c3">
      <div class="fg">
        <label>Producto</label>
        <select name="id_producto" required>
          <option value="">— Seleccionar —</option>
          @foreach($productos as $p)
          <option value="{{ $p->id_producto }}">{{ $p->descripcion }} · Stock: {{ $p->stock }} · ${{ number_format($p->precio,0,'.','.') }}</option>
          @endforeach
        </select>
        @error('id_producto')<div class="invalid-feedback">{{ $message }}</div>@enderror
      </div>
      <div class="fg">
        <label>Empleado responsable</label>
        <select name="id_empleado" required>
          <option value="">— Seleccionar —</option>
          @foreach($empleados as $e)
          <option value="{{ $e->id_usuario }}">{{ $e->nombre }} ({{ $e->cargo }})</option>
          @endforeach
        </select>
        @error('id_empleado')<div class="invalid-feedback">{{ $message }}</div>@enderror
      </div>
      <div class="fg">
        <label>Fecha</label>
        <input type="date" name="fecha" value="{{ now()->format('Y-m-d') }}" required>
      </div>
    </div>
    <div style="margin-top:14px">
      <button type="submit" class="btn-p">Registrar Venta</button>
    </div>
  </form>
</div>

<div class="card">
  <div class="card-hdr">
    <div class="card-title">Historial de Ventas</div>
    <span class="chip">{{ count($ventas) }} registros</span>
  </div>
  <table class="tbl">
    <thead>
      <tr><th>#</th><th>Fecha</th><th>Empleado</th><th>Cargo</th><th>Producto</th><th>Total</th><th>Acción</th></tr>
    </thead>
    <tbody>
      @forelse($ventas as $v)
      <tr>
        <td>{{ $v->id_venta }}</td>
        <td>{{ $v->fecha }}</td>
        <td>{{ $v->empleado }}</td>
        <td><span class="chip">{{ $v->cargo }}</span></td>
        <td>{{ $v->producto }}</td>
        <td style="color:#2d7a4f;font-weight:600">${{ number_format($v->total,0,'.','.') }}</td>
        <td>
          <form method="POST" action="{{ route('ventas.destroy', $v->id_venta) }}" style="display:inline" onsubmit="return confirm('¿Eliminar venta?')">
            @csrf @method('DELETE')
            <button type="submit" class="btn-d">Eliminar</button>
          </form>
        </td>
      </tr>
      @empty
      <tr><td colspan="7" style="text-align:center;color:#8a6a50;padding:30px">Sin ventas registradas</td></tr>
      @endforelse
    </tbody>
  </table>
</div>
@endsection
