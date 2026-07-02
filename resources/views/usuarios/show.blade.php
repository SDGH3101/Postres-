@extends('layouts.app')
@section('title','Detalle Usuario')
@section('content')
<div class="sec-hdr">
  <div class="sec-title">👤 Detalle del Usuario — sp_buscar_usuario({{ $usuario->id_usuario }})</div>
</div>
<div class="fcard">
  <div class="fgrid">
    <div><div class="sec-sub">Nombre</div><strong>{{ $usuario->nombre }}</strong></div>
    <div><div class="sec-sub">Correo</div>{{ $usuario->correo }}</div>
    <div><div class="sec-sub">Edad</div>{{ $usuario->edad ?? '—' }}</div>
    <div><div class="sec-sub">Rol</div><span class="badge {{ $usuario->rol==='emprendedor'?'bp':($usuario->rol==='empleado'?'bb':'bo') }}">{{ $usuario->rol }}</span></div>
    @if($usuario->empresa)<div><div class="sec-sub">Empresa</div>{{ $usuario->empresa }}</div>@endif
    @if($usuario->cargo)<div><div class="sec-sub">Cargo</div>{{ $usuario->cargo }}</div>@endif
    @if($usuario->salario)<div><div class="sec-sub">Salario</div>${{ number_format($usuario->salario,0,'.','.') }}</div>@endif
  </div>
  <div style="margin-top:20px">
    <a href="{{ route('usuarios.edit', $usuario->id_usuario) }}" class="btn-p">Editar</a>
    <a href="{{ route('usuarios.index') }}" class="btn-s" style="margin-left:8px">Volver</a>
  </div>
</div>
@endsection
