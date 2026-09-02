@extends('layouts.app')
@section('title','Gastos y Compras')
@section('content')
<div class="sec-hdr">
  <div class="sec-title">📋 Registro de Gastos y Compras</div>
  <div class="sec-sub">sp_listar_gastos() · sp_listar_compras_menores() · sp_gastos_por_categoria()</div>
</div>

<div class="stats-row">
  <div class="stat c2"><div class="ic">💸</div>
    <div class="val">${{ number_format($totalGastos/1000,1,'.','.') }}K</div>
    <div class="lbl">Total egresos</div></div>
  <div class="stat c1"><div class="ic">📊</div>
    <div class="val">{{ count($gastos) }}</div>
    <div class="lbl">Gastos generales</div></div>
  <div class="stat c4"><div class="ic">🛒</div>
    <div class="val">{{ count($compras) }}</div>
    <div class="lbl">Compras menores</div></div>
</div>

<div class="fcard">
  <div class="fcard-title">➕ Registrar Egreso</div>
  <form method="POST" action="{{ route('compras.store') }}">
    @csrf
    <div class="fgrid c3">
      <div class="fg"><label>Tipo</label>
        <select name="tipo" onchange="toggleCat(this.value)">
          <option value="gasto">💡 Gasto General</option>
          <option value="compra">🛒 Compra Menor</option>
        </select></div>
      <div class="fg"><label>Fecha</label>
        <input type="date" name="fecha" value="{{ now()->format('Y-m-d') }}"></div>
      <div class="fg"><label>Monto ($)</label>
        <input type="number" name="monto" placeholder="Ej: 45000" min="1"></div>
      <div class="fg" id="wrap-cat-gasto"><label>Categoría (gasto)</label>
        <select name="categoria">
          <option value="Servicios">💡 Servicios Públicos</option>
          <option value="Nómina">👔 Nómina</option>
          <option value="Insumos">🌾 Insumos</option>
          <option value="Marketing">📢 Marketing</option>
          <option value="Equipos">🔧 Equipos</option>
          <option value="Otros">🚚 Otros</option>
        </select></div>
      <div class="fg" id="wrap-cat-compra" style="display:none"><label>Categoría (compra)</label>
        <select name="id_categoria">
          @foreach($categorias as $cat)
          <option value="{{ $cat->id_categoria }}">{{ $cat->nombre_categoria }}</option>
          @endforeach
        </select></div>
      <div class="fg full"><label>Descripción detallada</label>
        <input type="text" name="descripcion" placeholder="Ej: Compra de 5kg de fresas frescas"></div>
    </div>
    <div style="margin-top:14px"><button type="submit" class="btn-p">Registrar Egreso</button></div>
  </form>
</div>

<div class="card">
  <div class="card-hdr"><div class="card-title">Gastos Generales</div><a href="{{ route('reportes.gastos.pdf') }}" class="chip">📄 PDF</a></div>
  <table class="tbl">
    <thead><tr><th>Fecha</th><th>Descripción</th><th>Categoría</th><th>Monto</th><th>Presupuesto</th><th>Acción</th></tr></thead>
    <tbody>
      @forelse($gastos as $g)
      <tr>
        <td>{{ $g->fecha }}</td>
        <td><strong>{{ $g->descripcion }}</strong></td>
        <td><span class="chip">{{ $g->categoria }}</span></td>
        <td style="color:#c0392b;font-weight:600">-${{ number_format($g->monto,0,'.','.') }}</td>
        <td>${{ number_format($g->presupuesto,0,'.','.') }}</td>
        <td>
          <a href="{{ route('compras.edit', $g->id_gasto) }}" class="btn-e">Editar</a>
          <form method="POST" action="{{ route('compras.destroy', $g->id_gasto) }}" style="display:inline" onsubmit="return confirm('¿Eliminar?')">
            @csrf @method('DELETE')
            <button type="submit" class="btn-d">Eliminar</button>
          </form>
        </td>
      </tr>
      @empty
      <tr><td colspan="6" style="text-align:center;color:#8a6a50">Sin gastos</td></tr>
      @endforelse
    </tbody>
  </table>
</div>

<div class="card">
  <div class="card-hdr"><div class="card-title">Compras Menores</div></div>
  <table class="tbl">
    <thead><tr><th>Fecha</th><th>Descripción</th><th>Categoría</th><th>Monto</th></tr></thead>
    <tbody>
      @forelse($compras as $c)
      <tr>
        <td>{{ $c->fecha }}</td>
        <td>{{ $c->descripcion }}</td>
        <td><span class="chip">{{ $c->categoria }}</span></td>
        <td style="color:#c0392b;font-weight:600">-${{ number_format($c->monto,0,'.','.') }}</td>
      </tr>
      @empty
      <tr><td colspan="4" style="text-align:center;color:#8a6a50">Sin compras</td></tr>
      @endforelse
    </tbody>
  </table>
</div>
@endsection
@push('scripts')
<script>
function toggleCat(v){
  document.getElementById('wrap-cat-gasto').style.display = v==='gasto'?'':'none';
  document.getElementById('wrap-cat-compra').style.display= v==='compra'?'':'none';
}
</script>
@endpush
