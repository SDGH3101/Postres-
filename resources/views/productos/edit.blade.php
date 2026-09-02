@extends('layouts.app')
@section('title','Editar Producto')
@section('content')
<div class="sec-hdr"><div class="sec-title">✏️ Editar Producto #{{ $producto->id_producto }}</div></div>
<div class="fcard">
  <form method="POST" action="{{ route('productos.update', $producto->id_producto) }}">
    @csrf @method('PUT')
    <div class="fgrid c3">
      <div class="fg"><label>Descripción</label>
        <input type="text" name="descripcion" value="{{ old('descripcion',$producto->descripcion) }}" class="{{ $errors->has('descripcion')?'is-invalid':'' }}">
        @error('descripcion')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
      <div class="fg"><label>Tipo de producto</label>
        <select name="tipo">
          <option value="postre" {{ old('tipo',$producto->tipo)=='postre'?'selected':'' }}>🍰 Postre</option>
          <option value="bebida" {{ old('tipo',$producto->tipo)=='bebida'?'selected':'' }}>🥤 Bebida</option>
          <option value="otro" {{ old('tipo',$producto->tipo)=='otro'?'selected':'' }}>📦 Otro</option>
        </select></div>
      <div class="fg"><label>Precio ($)</label>
        <input type="number" name="precio" value="{{ old('precio',$producto->precio) }}" min="0"></div>
      <div class="fg"><label>Stock</label>
        <input type="number" name="stock" value="{{ old('stock',$producto->stock) }}" min="0"></div>
      <div class="fg"><label>Stock mínimo (alerta)</label>
        <input type="number" name="stock_minimo" value="{{ old('stock_minimo',$producto->stock_minimo) }}" min="0"></div>
      <div class="fg"><label>Fecha de caducidad</label>
        <input type="date" name="fecha_caducidad" value="{{ old('fecha_caducidad', optional($producto->fecha_caducidad)->format('Y-m-d')) }}"></div>
    </div>
    <div style="margin-top:16px">
      <button type="submit" class="btn-p">Guardar cambios</button>
      <a href="{{ route('productos.index') }}" class="btn-s" style="margin-left:8px">Cancelar</a>
    </div>
  </form>
</div>
@endsection
