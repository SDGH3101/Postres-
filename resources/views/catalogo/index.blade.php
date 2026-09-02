@extends('layouts.app')
@section('title','Catálogo')
@section('content')
<div class="sec-hdr">
  <div class="sec-title">🍰 Catálogo de Postres</div>
  <div class="sec-sub">Descubre nuestros productos artesanales</div>
</div>
<div class="pgrid">
  @forelse($productos as $p)
  <div class="pcard">
    <div class="pcard-img">🍰</div>
    <div class="pcard-body">
      <div class="pcard-name">{{ $p->descripcion }}</div>
      <div class="pcard-foot">
        <span class="pcard-price">${{ number_format($p->precio,0,'.','.') }}</span>
        <span class="pcard-stock">{{ $p->stock > 0 ? '🟢 '.$p->stock.' disponibles' : '🔴 Agotado' }}</span>
      </div>
    </div>
  </div>
  @empty
  <p style="color:#8a6a50">No hay productos disponibles.</p>
  @endforelse
</div>
@endsection
