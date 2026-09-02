@extends('layouts.app')
@section('title','Nuevo Producto')
@section('content')
<div class="sec-hdr">
  <div class="sec-title">➕ Agregar Producto</div>
</div>

<div class="fcard" style="max-width:560px">
  <div class="fcard-title">Nuevo producto al inventario</div>
  <form method="POST" action="{{ route('productos.store') }}">
    @csrf
    <div class="fgrid c2">
      <div class="fg full">
        <label>Descripción del producto</label>
        <input type="text" name="descripcion" value="{{ old('descripcion') }}" placeholder="Ej: Torta de Chocolate 1kg" required>
        @error('descripcion')<div class="invalid-feedback">{{ $message }}</div>@enderror
      </div>
      <div class="fg">
        <label>Tipo de producto</label>
        <select name="tipo" required>
          <option value="postre" {{ old('tipo','postre')=='postre'?'selected':'' }}>🍰 Postre</option>
          <option value="bebida" {{ old('tipo')=='bebida'?'selected':'' }}>🥤 Bebida</option>
          <option value="otro" {{ old('tipo')=='otro'?'selected':'' }}>📦 Otro</option>
        </select>
        @error('tipo')<div class="invalid-feedback">{{ $message }}</div>@enderror
      </div>
      <div class="fg">
        <label>Precio ($)</label>
        <input type="number" name="precio" value="{{ old('precio') }}" placeholder="Ej: 85000" min="0" step="100" required>
        @error('precio')<div class="invalid-feedback">{{ $message }}</div>@enderror
      </div>
      <div class="fg">
        <label>Stock inicial</label>
        <input type="number" name="stock" value="{{ old('stock', 0) }}" placeholder="Ej: 10" min="0" required>
        @error('stock')<div class="invalid-feedback">{{ $message }}</div>@enderror
      </div>
      <div class="fg">
        <label>Stock mínimo (alerta)</label>
        <input type="number" name="stock_minimo" value="{{ old('stock_minimo', 5) }}" min="0">
        @error('stock_minimo')<div class="invalid-feedback">{{ $message }}</div>@enderror
      </div>
      <div class="fg">
        <label>Fecha de caducidad</label>
        <input type="date" name="fecha_caducidad" value="{{ old('fecha_caducidad') }}">
        @error('fecha_caducidad')<div class="invalid-feedback">{{ $message }}</div>@enderror
      </div>
    </div>
    <div style="margin-top:16px;display:flex;gap:12px">
      <button type="submit" class="btn-p">Agregar producto</button>
      <a href="{{ route('productos.index') }}" class="btn-p" style="background:#8a6a50">Cancelar</a>
    </div>
  </form>
</div>
@endsection
