<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>@yield('title','Postres Laura') — Sistema de Gestión</title>
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,700;0,900;1,400&family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">
<link rel="stylesheet" href="{{ asset('css/app.css') }}">
@stack('styles')
</head>
<body>

<nav class="topnav">
  <div class="nav-brand">🍰 Postres<em>Laura</em></div>
  <div class="nav-right">
    <div style="text-align:right">
      <div class="nav-name">{{ session('usuario.nombre') }}</div>
      <span class="nav-role role-{{ session('usuario.rol') }}">{{ session('usuario.rol') }}</span>
    </div>
    <div class="nav-avatar">{{ strtoupper(substr(session('usuario.nombre','?'),0,1)) }}</div>
    <form method="POST" action="{{ route('logout') }}" style="margin:0">
      @csrf
      <button type="submit" class="btn-logout">Salir</button>
    </form>
  </div>
</nav>

<div class="app-body">
  <nav class="sidebar">
    @php $rol = session('usuario.rol'); @endphp

    @if($rol !== 'cliente')
    <div class="sb-label">Operaciones</div>
    <a href="{{ route('dashboard') }}"  class="sb-item {{ request()->routeIs('dashboard') ? 'active':'' }}"><span>📊</span> Dashboard</a>
    <a href="{{ route('productos.index') }}" class="sb-item {{ request()->routeIs('productos.*') ? 'active':'' }}"><span>📦</span> Inventario</a>
    <a href="{{ route('compras.index') }}"   class="sb-item {{ request()->routeIs('compras.*') ? 'active':'' }}"><span>📋</span> Gastos</a>
    <a href="{{ route('ventas.index') }}"    class="sb-item {{ request()->routeIs('ventas.*') ? 'active':'' }}"><span>💰</span> Ventas</a>
    @endif

    <div class="sb-label">Comercial</div>
    <a href="{{ route('catalogo') }}" class="sb-item {{ request()->routeIs('catalogo') ? 'active':'' }}"><span>🍰</span> Catálogo</a>

    @if($rol === 'emprendedor')
    <div class="sb-label">Administración</div>
    <a href="{{ route('usuarios.index') }}"  class="sb-item {{ request()->routeIs('usuarios.*') ? 'active':'' }}"><span>👥</span> Usuarios</a>
    <a href="{{ route('empleados.index') }}" class="sb-item {{ request()->routeIs('empleados.*') ? 'active':'' }}"><span>👨‍🍳</span> Personal</a>
    <a href="{{ route('reportes.index') }}"  class="sb-item {{ request()->routeIs('reportes.*') ? 'active':'' }}"><span>📈</span> Reportes</a>
    @endif
  </nav>

  <main class="main">
    @if(session('success'))
      <div class="alert alert-success">✔ {{ session('success') }}</div>
    @endif
    @if(session('error'))
      <div class="alert alert-danger">⚠ {{ session('error') }}</div>
    @endif
    @if($errors->any())
      <div class="alert alert-danger">
        <ul style="margin:0;padding-left:18px">
          @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
        </ul>
      </div>
    @endif

    @yield('content')
  </main>
</div>

@stack('scripts')
</body>
</html>
