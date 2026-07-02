@extends('layouts.app')
@section('title','Nuevo Usuario')
@section('content')
<div class="sec-hdr">
  <div class="sec-title">👤 Crear Usuario</div>
</div>

<div class="fcard" style="max-width:580px">
  <div class="fcard-title">Registro de nuevo usuario al sistema</div>
  <form method="POST" action="{{ route('usuarios.store') }}">
    @csrf
    <div class="fgrid c2">
      <div class="fg full">
        <label>Nombre completo</label>
        <input type="text" name="nombre" value="{{ old('nombre') }}" placeholder="Ej: Ana María Torres" required>
        @error('nombre')<div class="invalid-feedback">{{ $message }}</div>@enderror
      </div>
      <div class="fg">
        <label>Correo electrónico</label>
        <input type="email" name="correo" value="{{ old('correo') }}" placeholder="correo@email.com" required>
        @error('correo')<div class="invalid-feedback">{{ $message }}</div>@enderror
      </div>
      <div class="fg">
        <label>Edad</label>
        <input type="number" name="edad" value="{{ old('edad') }}" min="16" max="99" required>
        @error('edad')<div class="invalid-feedback">{{ $message }}</div>@enderror
      </div>
      <div class="fg">
        <label>Contraseña</label>
        <input type="password" name="pass" placeholder="Mínimo 6 caracteres" required>
        @error('pass')<div class="invalid-feedback">{{ $message }}</div>@enderror
      </div>
      <div class="fg">
        <label>Rol en el sistema</label>
        <select name="rol" required>
          <option value="cliente"      {{ old('rol')==='cliente'      ? 'selected':'' }}>🛍️ Cliente</option>
          <option value="empleado"     {{ old('rol')==='empleado'     ? 'selected':'' }}>👨‍🍳 Empleado</option>
          <option value="emprendedor"  {{ old('rol')==='emprendedor'  ? 'selected':'' }}>👩‍💼 Emprendedor</option>
        </select>
        @error('rol')<div class="invalid-feedback">{{ $message }}</div>@enderror
      </div>
    </div>
    <div style="margin-top:16px;display:flex;gap:12px">
      <button type="submit" class="btn-p">Crear usuario</button>
      <a href="{{ route('usuarios.index') }}" class="btn-p" style="background:#8a6a50">Cancelar</a>
    </div>
  </form>
</div>
@endsection
