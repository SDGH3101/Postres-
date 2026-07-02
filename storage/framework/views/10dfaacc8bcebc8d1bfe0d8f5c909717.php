<?php $__env->startSection('title','Dashboard'); ?>
<?php $__env->startSection('content'); ?>

<div class="hero">
  <h2>¡Bienvenid@, <?php echo e(explode(' ', session('usuario.nombre'))[0]); ?>! 🎂</h2>
  <p>Aquí tienes el resumen del estado actual de Postres Laura — <?php echo e(now()->format('F Y')); ?></p>
</div>

<div class="stats-row">
  <div class="stat c1">
    <div class="ic">💰</div>
    <div class="val">$<?php echo e(number_format($totalIngresos/1000,0,'.','.')); ?>K</div>
    <div class="lbl">Ingresos totales</div>
  </div>
  <div class="stat c2">
    <div class="ic">📋</div>
    <div class="val">$<?php echo e(number_format($totalGastos/1000,0,'.','.')); ?>K</div>
    <div class="lbl">Gastos totales</div>
  </div>
  <div class="stat c3">
    <div class="ic">🛍️</div>
    <div class="val"><?php echo e(count($ventas)); ?></div>
    <div class="lbl">Últimas ventas</div>
  </div>
  <div class="stat c4">
    <div class="ic">🍰</div>
    <div class="val"><?php echo e($totalProductos); ?></div>
    <div class="lbl">Productos activos</div>
  </div>
</div>

<?php if(count($ventasMes) > 0): ?>
<div class="card">
  <div class="card-hdr">
    <div class="card-title">📊 Ventas por mes</div>
    <span class="chip"><?php echo e(count($ventasMes)); ?> meses</span>
  </div>
  <div style="padding:22px 26px">
    <?php $maxVenta = max(array_column((array)$ventasMes,'total')); ?>
    <?php $__currentLoopData = $ventasMes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $vm): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
    <div class="bar-row">
      <div class="bar-lbl"><?php echo e($vm->mes); ?></div>
      <div class="bar-track">
        <div class="bar-fill" style="width:<?php echo e($maxVenta > 0 ? round(($vm->total/$maxVenta)*100) : 0); ?>%"></div>
      </div>
      <div class="bar-val">$<?php echo e(number_format($vm->total,0,'.','.')); ?></div>
    </div>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
  </div>
</div>
<?php endif; ?>

<div style="display:grid;grid-template-columns:1fr 1fr;gap:18px">

  <div class="card">
    <div class="card-hdr">
      <div class="card-title">🏆 Productos más vendidos</div>
      <span class="chip">sp_productos_mas_vendidos()</span>
    </div>
    <table class="tbl">
      <thead><tr><th>Producto</th><th>Veces</th><th>Ingresos</th></tr></thead>
      <tbody>
        <?php $__empty_1 = true; $__currentLoopData = $masVendidos; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
        <tr>
          <td><?php echo e($p->descripcion); ?></td>
          <td><span class="badge bg"><?php echo e($p->veces); ?></span></td>
          <td>$<?php echo e(number_format($p->ingresos,0,'.','.')); ?></td>
        </tr>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
        <tr><td colspan="3" style="text-align:center;color:#8a6a50">Sin datos</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>

  <div class="card">
    <div class="card-hdr">
      <div class="card-title">🕒 Últimas ventas</div>
      <?php if(session('usuario.rol') !== 'cliente'): ?>
      <a href="<?php echo e(route('ventas.index')); ?>" class="chip">Ver todas →</a>
      <?php endif; ?>
    </div>
    <table class="tbl">
      <thead><tr><th>Fecha</th><th>Producto</th><th>Total</th></tr></thead>
      <tbody>
        <?php $__empty_1 = true; $__currentLoopData = $ventas; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $v): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
        <tr>
          <td><?php echo e($v->fecha); ?></td>
          <td><?php echo e($v->producto); ?></td>
          <td>$<?php echo e(number_format($v->total,0,'.','.')); ?></td>
        </tr>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
        <tr><td colspan="3" style="text-align:center;color:#8a6a50">Sin ventas registradas</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>

</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\postres\resources\views/dashboard/index.blade.php ENDPATH**/ ?>