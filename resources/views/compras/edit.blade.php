@extends('layouts.app')
@section('title','Editar Gasto')
@section('content')
<div class="sec-hdr">
  <div class="sec-title">✏️ Editar Gasto</div>
</div>

<div class="fcard" style="max-width:600px">
  <div class="fcard-title">Modificar registro de gasto</div>
  <form method="POST" action="{{ route('compras.update', $gasto->id_gasto) }}">
    @csrf @method('PUT')
    <div class="fgrid c2">
      <div class="fg full">
        <label>Descripción</label>
        <input type="text" name="descripcion" value="{{ old('descripcion', $gasto->descripcion) }}" required>
        @error('descripcion')<div class="invalid-feedback">{{ $message }}</div>@enderror
      </div>
      <div class="fg">
        <label>Fecha</label>
        <input type="date" name="fecha" value="{{ old('fecha', $gasto->fecha) }}" required>
      </div>
      <div class="fg">
        <label>Monto ($)</label>
        <input type="number" name="monto" value="{{ old('monto', $gasto->monto) }}" min="1" step="100" required>
      </div>
      <div class="fg">
        <label>Categoría</label>
        <select name="categoria">
          @foreach(['Servicios','Nómina','Insumos','Marketing','Equipos','Otros'] as $cat)
          <option value="{{ $cat }}" {{ old('categoria', $gasto->categoria) === $cat ? 'selected' : '' }}>{{ $cat }}</option>
          @endforeach
        </select>
      </div>
      <div class="fg">
        <label>Presupuesto ($)</label>
        <input type="number" name="presupuesto" value="{{ old('presupuesto', $gasto->presupuesto) }}" min="0" step="100">
      </div>
    </div>
    <div style="margin-top:16px;display:flex;gap:12px">
      <button type="submit" class="btn-p">Guardar cambios</button>
      <a href="{{ route('compras.index') }}" class="btn-p" style="background:#8a6a50">Cancelar</a>
    </div>
  </form>
</div>
@endsection
