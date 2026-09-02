@extends('layouts.app')
@section('title','Editar Empleado')
@section('content')
<div class="sec-hdr"><div class="sec-title">✏️ Editar Empleado #{{ $empleado->id_usuario }}</div></div>

<div class="fcard" style="max-width:600px">
  <form method="POST" action="{{ route('empleados.update', $empleado->id_usuario) }}">
    @csrf @method('PUT')
    <div class="fgrid c2">
      <div class="fg full">
        <label>Nombre completo</label>
        <input type="text" name="nombre" value="{{ old('nombre', $empleado->nombre) }}" required>
        @error('nombre')<div class="invalid-feedback">{{ $message }}</div>@enderror
      </div>
      <div class="fg">
        <label>Correo electrónico</label>
        <input type="email" name="correo" value="{{ old('correo', $empleado->correo) }}" required>
        @error('correo')<div class="invalid-feedback">{{ $message }}</div>@enderror
      </div>
      <div class="fg">
        <label>Fecha de nacimiento</label>
        <input type="date" name="fecha_nacimiento" value="{{ old('fecha_nacimiento', $empleado->fecha_nacimiento) }}"
               max="{{ now()->subYears(16)->toDateString() }}"
               min="{{ now()->subYears(99)->toDateString() }}" required>
        @error('fecha_nacimiento')<div class="invalid-feedback">{{ $message }}</div>@enderror
      </div>
      <div class="fg">
        <label>Nueva contraseña (opcional)</label>
        <input type="password" name="pass" placeholder="Dejar en blanco para no cambiarla">
        @error('pass')<div class="invalid-feedback">{{ $message }}</div>@enderror
      </div>
      <div class="fg">
        <label>Cargo</label>
        <input type="text" name="cargo" value="{{ old('cargo', $empleado->cargo) }}" required>
        @error('cargo')<div class="invalid-feedback">{{ $message }}</div>@enderror
      </div>
      <div class="fg">
        <label>Salario</label>
        <input type="number" name="salario" value="{{ old('salario', $empleado->salario) }}" min="0" step="1000" required>
        @error('salario')<div class="invalid-feedback">{{ $message }}</div>@enderror
      </div>
      <div class="fg">
        <label>Horas trabajadas (mensual)</label>
        <input type="number" name="horas_trabajadas" value="{{ old('horas_trabajadas', $empleado->horas_trabajadas) }}" min="0" required>
        @error('horas_trabajadas')<div class="invalid-feedback">{{ $message }}</div>@enderror
      </div>
    </div>
    <div style="margin-top:16px">
      <button type="submit" class="btn-p">Guardar cambios</button>
      <a href="{{ route('empleados.index') }}" class="btn-s" style="margin-left:8px">Cancelar</a>
    </div>
  </form>
</div>
@endsection
