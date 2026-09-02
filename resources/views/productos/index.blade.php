@extends('layouts.app')
@section('title','Inventario')
@section('content')
<div class="sec-hdr">
  <div class="sec-title">🍰 Gestión de Productos</div>
  <div class="sec-sub">Inventario completo — CRUD con control de stock</div>
</div>

<div class="fcard">
  <div class="fcard-title">➕ Agregar nuevo producto</div>
  <form method="POST" action="{{ route('productos.store') }}">
    @csrf
    <div class="fgrid c3">
      <div class="fg"><label>Nombre / Descripción</label>
        <input type="text" name="descripcion" value="{{ old('descripcion') }}" placeholder="Torta de naranja" class="{{ $errors->has('descripcion')?'is-invalid':'' }}">
        @error('descripcion')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
      <div class="fg"><label>Tipo</label>
        <select name="tipo">
          <option value="postre" {{ old('tipo','postre')=='postre'?'selected':'' }}>🍰 Postre</option>
          <option value="bebida" {{ old('tipo')=='bebida'?'selected':'' }}>🥤 Bebida</option>
          <option value="otro" {{ old('tipo')=='otro'?'selected':'' }}>📦 Otro</option>
        </select>
      </div>
      <div class="fg"><label>Precio ($)</label>
        <input type="number" name="precio" value="{{ old('precio') }}" placeholder="85000" min="0">
      </div>
      <div class="fg"><label>Stock inicial</label>
        <input type="number" name="stock" value="{{ old('stock',0) }}" min="0" placeholder="10">
      </div>
      <div class="fg"><label>Stock mínimo (alerta)</label>
        <input type="number" name="stock_minimo" value="{{ old('stock_minimo',5) }}" min="0">
      </div>
      <div class="fg"><label>Fecha de caducidad</label>
        <input type="date" name="fecha_caducidad" value="{{ old('fecha_caducidad') }}">
      </div>
    </div>
    <div style="margin-top:14px"><button type="submit" class="btn-p">Agregar producto</button></div>
  </form>
</div>

<div class="card">
  <div class="card-hdr">
    <div class="card-title">Listado de Inventario</div>
    <span class="chip">{{ $productos->count() }} productos</span>
  </div>
  <table class="tbl">
    <thead>
      <tr><th>ID</th><th>Producto</th><th>Tipo</th><th>Precio</th><th>Stock</th><th>Caducidad</th><th>Estado</th><th>Acciones</th></tr>
    </thead>
    <tbody>
      @forelse($productos as $p)
      @php
        $estado = $p->estado_inventario;
        $badge = match($estado) {
          'vencido'    => ['br', '⛔ Vencido'],
          'por_vencer' => ['bo', '⚠️ Por vencer'],
          'agotado'    => ['br', '❌ Agotado'],
          'bajo'       => ['bo', '⚠️ Stock bajo'],
          default      => ['bg', '✅ OK'],
        };
      @endphp
      <tr>
        <td>#{{ $p->id_producto }}</td>
        <td><strong>{{ $p->descripcion }}</strong></td>
        <td>{{ ['postre'=>'🍰 Postre','bebida'=>'🥤 Bebida','otro'=>'📦 Otro'][$p->tipo] ?? $p->tipo }}</td>
        <td>${{ number_format($p->precio,0,'.','.') }}</td>
        <td>{{ $p->stock }} unds <small style="color:#8a6a50">(mín. {{ $p->stock_minimo }})</small></td>
        <td>{{ $p->fecha_caducidad ? $p->fecha_caducidad->format('d-m-Y') : '—' }}</td>
        <td><span class="badge {{ $badge[0] }}">{{ $badge[1] }}</span></td>
        <td>
          <a href="{{ route('productos.show', $p->id_producto) }}" class="btn-e" style="background:#8a6a50">Ver</a>
          <a href="{{ route('productos.edit', $p->id_producto) }}" class="btn-e">Editar</a>
          <form method="POST" action="{{ route('productos.destroy', $p->id_producto) }}" style="display:inline" onsubmit="return confirm('¿Eliminar producto?')">
            @csrf @method('DELETE')
            <button type="submit" class="btn-d">Eliminar</button>
          </form>
        </td>
      </tr>
      @empty
      <tr><td colspan="7" style="text-align:center;color:#8a6a50">Sin productos registrados</td></tr>
      @endforelse
    </tbody>
  </table>
</div>
@endsection
