@extends('layouts.app')
@section('title','Usuarios')
@section('content')

<div class="sec-hdr">
  <div class="sec-title">👥 Gestión de Usuarios</div>
  <div class="sec-sub">sp_listar_usuarios() · sp_buscar_usuario() · sp_usuarios_sin_rol()</div>
</div>

<div class="fcard">
  <div class="fcard-title">➕ Registrar nuevo usuario</div>
  <form method="POST" action="{{ route('usuarios.store') }}">
    @csrf
    <div class="fgrid">
      <div class="fg"><label>Nombre completo</label>
        <input type="text" name="nombre" value="{{ old('nombre') }}" placeholder="Ej: Ana García" class="{{ $errors->has('nombre')?'is-invalid':'' }}">
        @error('nombre')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
      <div class="fg"><label>Correo</label>
        <input type="email" name="correo" value="{{ old('correo') }}" placeholder="correo@email.com" class="{{ $errors->has('correo')?'is-invalid':'' }}">
        @error('correo')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
      <div class="fg"><label>Edad</label>
        <input type="number" name="edad" value="{{ old('edad') }}" placeholder="25" min="16"></div>
      <div class="fg"><label>Contraseña</label>
        <input type="password" name="pass" placeholder="Mínimo 6 caracteres" class="{{ $errors->has('pass')?'is-invalid':'' }}">
        @error('pass')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
      <div class="fg full"><label>Rol</label>
        <select name="rol">
          <option value="cliente">🛍️ Cliente</option>
          <option value="empleado">👨‍🍳 Empleado</option>
          <option value="emprendedor">🏢 Emprendedor</option>
        </select></div>
    </div>
    <div style="margin-top:16px">
      <button type="submit" class="btn-p">Registrar usuario</button>
      <a href="{{ route('usuarios.index') }}" class="btn-s" style="margin-left:8px">Cancelar</a>
    </div>
  </form>
</div>

<div class="card">
  <div class="card-hdr">
    <div class="card-title">Usuarios del sistema</div>
    <span class="chip">{{ count($usuarios) }} registrados</span>
  </div>
  <table class="tbl">
    <thead>
      <tr><th>ID</th><th>Nombre</th><th>Correo</th><th>Edad</th><th>Registro</th><th>Rol</th><th>Acciones</th></tr>
    </thead>
    <tbody>
      @forelse($usuarios as $u)
      <tr>
        <td>#{{ $u->id_usuario }}</td>
        <td><strong>{{ $u->nombre }}</strong></td>
        <td>{{ $u->correo }}</td>
        <td>{{ $u->edad ?? '—' }} años</td>
        <td>{{ $u->fecha_registro }}</td>
        <td>
          <span class="badge {{ $u->rol==='emprendedor'?'bp':($u->rol==='empleado'?'bb':'bo') }}">
            {{ $u->rol }}
          </span>
        </td>
        <td>
          <a href="{{ route('usuarios.show', $u->id_usuario) }}" class="btn-e">Ver</a>
          <a href="{{ route('usuarios.edit', $u->id_usuario) }}" class="btn-e">Editar</a>
          <form method="POST" action="{{ route('usuarios.destroy', $u->id_usuario) }}" style="display:inline" onsubmit="return confirm('¿Eliminar usuario?')">
            @csrf @method('DELETE')
            <button type="submit" class="btn-d">Eliminar</button>
          </form>
        </td>
      </tr>
      @empty
      <tr><td colspan="7" style="text-align:center;color:#8a6a50">Sin usuarios</td></tr>
      @endforelse
    </tbody>
  </table>
</div>
@endsection
