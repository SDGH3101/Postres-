<?php $__env->startSection('title','Flujo de Caja'); ?>
<?php $__env->startSection('content'); ?>
<div class="sec-hdr">
  <div class="sec-title">💵 Flujo de Caja</div>
  <div class="sec-sub">Entradas, salidas y saldo corriente del negocio</div>
</div>

<div style="display:grid;grid-template-columns:repeat(3,1fr);gap:16px;margin-bottom:22px">
  <div class="fcard"><div class="fcard-title">💰 Saldo actual de caja</div>
    <div style="font-size:1.8rem;font-weight:700;color:<?php echo e($saldoCaja>=0?'#2d7a4f':'#c0392b'); ?>">$<?php echo e(number_format($saldoCaja,0,'.','.')); ?></div></div>
  <div class="fcard"><div class="fcard-title">📈 Ingresos históricos</div>
    <div style="font-size:1.8rem;font-weight:700;color:#2d7a4f">$<?php echo e(number_format($totalIngresos,0,'.','.')); ?></div></div>
  <div class="fcard"><div class="fcard-title">📉 Salidas históricas</div>
    <div style="font-size:1.8rem;font-weight:700;color:#c0392b">$<?php echo e(number_format($totalGastos,0,'.','.')); ?></div></div>
</div>

<div style="display:flex;gap:8px;margin-bottom:18px">
  <?php $__currentLoopData = ['diario'=>'📆 Diario','semanal'=>'🗓️ Semanal','mensual'=>'📅 Mensual','anual'=>'🗓️ Anual']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key=>$label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
    <a href="<?php echo e(route('flujo-caja.index', ['periodo'=>$key])); ?>"
       class="btn-p" style="<?php echo e($periodo===$key ? '' : 'background:#8a6a50'); ?>"><?php echo e($label); ?></a>
  <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
</div>

<div class="card">
  <div class="card-hdr">
    <div class="card-title">Detalle del periodo (<?php echo e(ucfirst($periodo)); ?>)</div>
    <span class="chip"><?php echo e(count($flujo)); ?> periodos</span>
  </div>
  <table class="tbl">
    <thead><tr><th>Periodo</th><th>Entradas</th><th>Salidas</th><th>Neto</th><th>Saldo acumulado</th></tr></thead>
    <tbody>
      <?php $__empty_1 = true; $__currentLoopData = $flujo; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $f): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
      <tr>
        <td><?php echo e($f->periodo); ?></td>
        <td style="color:#2d7a4f;font-weight:600">+$<?php echo e(number_format($f->entradas,0,'.','.')); ?></td>
        <td style="color:#c0392b;font-weight:600">-$<?php echo e(number_format($f->salidas,0,'.','.')); ?></td>
        <td style="font-weight:700;color:<?php echo e($f->neto>=0?'#2d7a4f':'#c0392b'); ?>">
          <?php echo e($f->neto>=0?'+':''); ?>$<?php echo e(number_format($f->neto,0,'.','.')); ?></td>
        <td style="font-weight:700">$<?php echo e(number_format($f->saldo_acumulado,0,'.','.')); ?></td>
      </tr>
      <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
      <tr><td colspan="5" style="text-align:center;color:#8a6a50">Sin movimientos en este periodo</td></tr>
      <?php endif; ?>
    </tbody>
  </table>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\postres\resources\views/flujo_caja/index.blade.php ENDPATH**/ ?>