<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
<title><?php echo $__env->yieldContent('title','Postres Laura'); ?> — Sistema de Gestión</title>
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,700;0,900;1,400&family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">
<link rel="stylesheet" href="<?php echo e(asset('css/app.css')); ?>">
<?php echo $__env->yieldPushContent('styles'); ?>
</head>
<body>

<nav class="topnav">
  <div class="nav-brand">🍰 Postres<em>Laura</em></div>
  <div class="nav-right">
    <div style="text-align:right">
      <div class="nav-name"><?php echo e(session('usuario.nombre')); ?></div>
      <span class="nav-role role-<?php echo e(session('usuario.rol')); ?>"><?php echo e(session('usuario.rol')); ?></span>
    </div>
    <div class="nav-avatar"><?php echo e(strtoupper(substr(session('usuario.nombre','?'),0,1))); ?></div>
    <form method="POST" action="<?php echo e(route('logout')); ?>" style="margin:0">
      <?php echo csrf_field(); ?>
      <button type="submit" class="btn-logout">Salir</button>
    </form>
  </div>
</nav>

<div class="app-body">
  <nav class="sidebar">
    <?php $rol = session('usuario.rol'); ?>

    <?php if($rol !== 'cliente'): ?>
    <div class="sb-label">Operaciones</div>
    <a href="<?php echo e(route('dashboard')); ?>"  class="sb-item <?php echo e(request()->routeIs('dashboard') ? 'active':''); ?>"><span>📊</span> Dashboard</a>
    <a href="<?php echo e(route('productos.index')); ?>" class="sb-item <?php echo e(request()->routeIs('productos.*') ? 'active':''); ?>"><span>📦</span> Inventario</a>
    <a href="<?php echo e(route('compras.index')); ?>"   class="sb-item <?php echo e(request()->routeIs('compras.*') ? 'active':''); ?>"><span>📋</span> Gastos</a>
    <a href="<?php echo e(route('ventas.index')); ?>"    class="sb-item <?php echo e(request()->routeIs('ventas.*') ? 'active':''); ?>"><span>💰</span> Ventas</a>
    <?php endif; ?>

    <div class="sb-label">Comercial</div>
    <a href="<?php echo e(route('catalogo')); ?>" class="sb-item <?php echo e(request()->routeIs('catalogo') ? 'active':''); ?>"><span>🍰</span> Catálogo</a>

    <?php if($rol === 'emprendedor'): ?>
    <div class="sb-label">Administración</div>
    <a href="<?php echo e(route('usuarios.index')); ?>"  class="sb-item <?php echo e(request()->routeIs('usuarios.*') ? 'active':''); ?>"><span>👥</span> Usuarios</a>
    <a href="<?php echo e(route('empleados.index')); ?>" class="sb-item <?php echo e(request()->routeIs('empleados.*') ? 'active':''); ?>"><span>👨‍🍳</span> Personal</a>
    <a href="<?php echo e(route('reportes.index')); ?>"  class="sb-item <?php echo e(request()->routeIs('reportes.*') ? 'active':''); ?>"><span>📈</span> Reportes</a>
    <?php endif; ?>
  </nav>

  <main class="main">
    <?php if(session('success')): ?>
      <div class="alert alert-success">✔ <?php echo e(session('success')); ?></div>
    <?php endif; ?>
    <?php if(session('error')): ?>
      <div class="alert alert-danger">⚠ <?php echo e(session('error')); ?></div>
    <?php endif; ?>
    <?php if($errors->any()): ?>
      <div class="alert alert-danger">
        <ul style="margin:0;padding-left:18px">
          <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $e): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><li><?php echo e($e); ?></li><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </ul>
      </div>
    <?php endif; ?>

    <?php echo $__env->yieldContent('content'); ?>
  </main>
</div>

<?php echo $__env->yieldPushContent('scripts'); ?>
</body>
</html>
<?php /**PATH C:\xampp\htdocs\postres\resources\views/layouts/app.blade.php ENDPATH**/ ?>