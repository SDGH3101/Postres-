@extends('layouts.app')
@section('title','Nuevo Empleado')
@section('content')
<div class="sec-hdr">
  <div class="sec-title">👨‍🍳 Contratar Empleado</div>
</div>

<div class="fcard" style="max-width:600px">
  <div class="fcard-title">Crea la cuenta y el cargo del nuevo empleado</div>
  <form method="POST" action="{{ route('empleados.store') }}">
    @csrf
    <div class="fgrid c2">
      <div class="fg full">
        <label>Nombre completo</label>
        <input type="text" name="nombre" value="{{ old('nombre') }}" placeholder="Ej: Juan Pérez" required>
        @error('nombre')<div class="invalid-feedback">{{ $message }}</div>@enderror
      </div>
      <div class="fg">
        <label>Correo electrónico</label>
        <input type="email" name="correo" value="{{ old('correo') }}" placeholder="correo@email.com" required>
        @error('correo')<div class="invalid-feedback">{{ $message }}</div>@enderror
      </div>
      <div class="fg">
        <label>Fecha de nacimiento</label>
        <input type="date" name="fecha_nacimiento" value="{{ old('fecha_nacimiento') }}"
               max="{{ now()->subYears(16)->toDateString() }}"
               min="{{ now()->subYears(99)->toDateString() }}" required>
        @error('fecha_nacimiento')<div class="invalid-feedback">{{ $message }}</div>@enderror
      </div>
      <div class="fg">
        <label>Contraseña</label>
        <input type="password" name="pass" placeholder="Mínimo 6 caracteres" required>
        @error('pass')<div class="invalid-feedback">{{ $message }}</div>@enderror
      </div>
      <div class="fg">
        <label>Cargo</label>
        <input type="text" name="cargo" value="{{ old('cargo') }}" placeholder="Ej: Repostero, Cajero, Domiciliario" required>
        @error('cargo')<div class="invalid-feedback">{{ $message }}</div>@enderror
      </div>
      <div class="fg">
        <label>Salario</label>
        <input type="number" name="salario" value="{{ old('salario') }}" placeholder="Ej: 1300000" min="0" step="1000" required>
        @error('salario')<div class="invalid-feedback">{{ $message }}</div>@enderror
      </div>
      <div class="fg">
        <label>Horas trabajadas (mensual)</label>
        <input type="number" name="horas_trabajadas" value="{{ old('horas_trabajadas', 0) }}" min="0" required>
        @error('horas_trabajadas')<div class="invalid-feedback">{{ $message }}</div>@enderror
      </div>
    </div>
    <div style="margin-top:16px;display:flex;gap:12px">
      <button type="submit" class="btn-p">Contratar empleado</button>
      <a href="{{ route('empleados.index') }}" class="btn-p" style="background:#8a6a50">Cancelar</a>
    </div>
  </form>
</div>
@endsection
