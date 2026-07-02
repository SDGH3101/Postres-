@extends('layouts.app')
@section('title','Editar Usuario')
@section('content')
<div class="sec-hdr">
  <div class="sec-title">✏️ Editar Usuario #{{ $usuario->id_usuario }}</div>
</div>
<div class="fcard">
  <div class="fcard-title">Actualizar datos</div>
  <form method="POST" action="{{ route('usuarios.update', $usuario->id_usuario) }}">
    @csrf @method('PUT')
    <div class="fgrid">
      <div class="fg"><label>Nombre</label>
        <input type="text" name="nombre" value="{{ old('nombre', $usuario->nombre) }}" class="{{ $errors->has('nombre')?'is-invalid':'' }}">
        @error('nombre')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
      <div class="fg"><label>Edad</label>
        <input type="number" name="edad" value="{{ old('edad', $usuario->edad) }}" min="16"></div>
      <div class="fg full"><label>Nueva contraseña (dejar vacío para no cambiar)</label>
        <input type="password" name="pass" placeholder="Mínimo 6 caracteres"></div>
    </div>
    <div style="margin-top:16px">
      <button type="submit" class="btn-p">Guardar cambios</button>
      <a href="{{ route('usuarios.index') }}" class="btn-s" style="margin-left:8px">Cancelar</a>
    </div>
  </form>
</div>
@endsection
