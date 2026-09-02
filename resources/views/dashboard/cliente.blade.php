@extends('layouts.app')
@section('title','Bienvenida')
@section('content')

<div class="hero" style="text-align:center">
  <h2>¡Bienvenid@, {{ explode(' ', $usuario['nombre'])[0] }}! 🎂</h2>
  <p>Gracias por ser parte de la familia Postres Laura</p>
</div>

<div class="card" style="overflow:hidden;padding:0">
  <img src="{{ asset('images/fachada.jpeg') }}" alt="Fachada Postres Laura"
       style="width:100%;max-height:420px;object-fit:cover;display:block">
  <div style="padding:26px 30px;text-align:center">
    <h3 style="margin:0 0 8px;font-family:'Playfair Display',serif;color:#5a3a24">
      Postres Laura
    </h3>
    <p style="margin:0;color:#8a6a50">
      📍 Calle 66 #11-77
    </p>
    <p style="margin:10px 0 0;color:#8a6a50">
      Endulzamos tus momentos especiales con postres hechos con amor.
      Visítanos o consulta nuestro catálogo para conocer todo lo que
      tenemos para ti.
    </p>
    <a href="{{ route('catalogo') }}" class="btn-p" style="display:inline-block;margin-top:18px;text-decoration:none">
      Ver catálogo 🍰
    </a>
  </div>
</div>

@endsection
