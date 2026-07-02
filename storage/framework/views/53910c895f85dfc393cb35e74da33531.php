<?php $__env->startSection('title','Reportes'); ?>
<?php $__env->startSection('content'); ?>
<div class="sec-hdr">
  <div class="sec-title">📈 Reportes y Análisis</div>
  <div class="sec-sub">sp_ventas_por_mes() · sp_gastos_por_categoria() · sp_productos_mas_vendidos() · sp_balance_ventas_gastos()</div>
</div>

<div style="display:flex;gap:12px;margin-bottom:24px">
  <a href="<?php echo e(route('reportes.ventas.pdf')); ?>" class="btn-p">📄 Exportar Ventas PDF</a>
  <a href="<?php echo e(route('reportes.gastos.pdf')); ?>"  class="btn-p" style="background:linear-gradient(135deg,#c9717a,#e8a84c)">📄 Exportar Gastos PDF</a>
  <a href="<?php echo e(route('reportes.excel')); ?>"        class="btn-p" style="background:linear-gradient(135deg,#2d7a4f,#6b8c6e)">📊 Exportar Excel (CSV)</a>
</div>


<div class="card" style="margin-bottom:22px">
  <div class="card-hdr">
    <div class="card-title">📅 Ventas por mes</div>
    <span class="chip">sp_ventas_por_mes()</span>
  </div>
  <table class="tbl">
    <thead><tr><th>Mes</th><th>N° Ventas</th><th>Ingresos</th><th>Ticket Promedio</th></tr></thead>
    <tbody>
      <?php $__empty_1 = true; $__currentLoopData = $ventasMes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $v): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
      <tr>
        <td><?php echo e($v->mes); ?></td>
        <td><span class="badge bg"><?php echo e($v->total_ventas); ?></span></td>
        <td style="color:#2d7a4f;font-weight:600">$<?php echo e(number_format($v->ingresos_mes,0,'.','.')); ?></td>
        <td>$<?php echo e(number_format($v->ticket_promedio,0,'.','.')); ?></td>
      </tr>
      <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
      <tr><td colspan="4" style="text-align:center;color:#8a6a50">Sin datos</td></tr>
      <?php endif; ?>
    </tbody>
  </table>
</div>


<div style="display:grid;grid-template-columns:1fr 1fr;gap:18px;margin-bottom:22px">
  <div class="card">
    <div class="card-hdr">
      <div class="card-title">💸 Gastos por Categoría</div>
      <span class="chip">sp_gastos_por_categoria()</span>
    </div>
    <table class="tbl">
      <thead><tr><th>Categoría</th><th>Cantidad</th><th>Total</th></tr></thead>
      <tbody>
        <?php $__empty_1 = true; $__currentLoopData = $gastosCat; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $g): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
        <tr>
          <td><span class="chip"><?php echo e($g->categoria); ?></span></td>
          <td><?php echo e($g->cantidad); ?></td>
          <td style="color:#c0392b;font-weight:600">-$<?php echo e(number_format($g->total,0,'.','.')); ?></td>
        </tr>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
        <tr><td colspan="3" style="text-align:center;color:#8a6a50">Sin datos</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>

  
  <div class="card">
    <div class="card-hdr">
      <div class="card-title">🏆 Productos más vendidos</div>
      <span class="chip">sp_productos_mas_vendidos()</span>
    </div>
    <table class="tbl">
      <thead><tr><th>Producto</th><th>Stock</th><th>Veces</th><th>Ingresos</th></tr></thead>
      <tbody>
        <?php $__empty_1 = true; $__currentLoopData = $masVendidos; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
        <tr>
          <td><?php echo e($p->descripcion); ?></td>
          <td><?php echo e($p->stock); ?></td>
          <td><span class="badge bg"><?php echo e($p->veces); ?></span></td>
          <td style="color:#2d7a4f;font-weight:600">$<?php echo e(number_format($p->ingresos,0,'.','.')); ?></td>
        </tr>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
        <tr><td colspan="4" style="text-align:center;color:#8a6a50">Sin datos</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>


<div class="card">
  <div class="card-hdr">
    <div class="card-title">⚖️ Balance Ventas vs Gastos</div>
    <span class="chip">sp_balance_ventas_gastos()</span>
  </div>
  <table class="tbl">
    <thead><tr><th>Mes</th><th>Ingresos</th><th>Gastos</th><th>Balance</th></tr></thead>
    <tbody>
      <?php $__empty_1 = true; $__currentLoopData = $balance; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $b): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
      <tr>
        <td><?php echo e($b->mes); ?></td>
        <td style="color:#2d7a4f;font-weight:600">$<?php echo e(number_format($b->ingresos,0,'.','.')); ?></td>
        <td style="color:#c0392b;font-weight:600">-$<?php echo e(number_format($b->gastos,0,'.','.')); ?></td>
        <td style="font-weight:700;color:<?php echo e($b->balance >= 0 ? '#2d7a4f' : '#c0392b'); ?>">
          <?php echo e($b->balance >= 0 ? '+' : ''); ?>$<?php echo e(number_format($b->balance,0,'.','.')); ?>

        </td>
      </tr>
      <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
      <tr><td colspan="4" style="text-align:center;color:#8a6a50">Sin datos</td></tr>
      <?php endif; ?>
    </tbody>
  </table>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\postres\resources\views/reportes/index.blade.php ENDPATH**/ ?>