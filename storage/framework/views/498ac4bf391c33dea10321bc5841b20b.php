<?php $__env->startSection('title','Presupuesto'); ?>
<?php $__env->startSection('content'); ?>
<div class="sec-hdr">
  <div class="sec-title">🎯 Presupuesto por periodo</div>
  <div class="sec-sub">Fija metas de gasto diarias, semanales, mensuales o anuales</div>
</div>

<div class="fcard" style="max-width:640px">
  <div class="fcard-title">➕ Fijar nuevo presupuesto</div>
  <form method="POST" action="<?php echo e(route('presupuestos.store')); ?>">
    <?php echo csrf_field(); ?>
    <div class="fgrid c3">
      <div class="fg"><label>Periodo</label>
        <select name="tipo">
          <option value="diario">Diario</option>
          <option value="semanal">Semanal</option>
          <option value="mensual" selected>Mensual</option>
          <option value="anual">Anual</option>
        </select></div>
      <div class="fg"><label>Monto ($)</label>
        <input type="number" name="monto" min="1" step="1000" placeholder="500000" required></div>
      <div class="fg"><label>Fecha de inicio</label>
        <input type="date" name="fecha_inicio" value="<?php echo e(now()->toDateString()); ?>" required></div>
    </div>
    <div style="margin-top:14px"><button type="submit" class="btn-p">Fijar presupuesto</button></div>
  </form>
</div>

<div class="card">
  <div class="card-hdr">
    <div class="card-title">Presupuestos activos</div>
    <span class="chip"><?php echo e($presupuestos->count()); ?></span>
  </div>
  <?php $__empty_1 = true; $__currentLoopData = $presupuestos; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
  <?php
    $color = $p->porcentaje >= 100 ? '#c0392b' : ($p->porcentaje >= 80 ? '#e8a84c' : '#2d7a4f');
  ?>
  <div style="padding:16px 0;border-bottom:1px solid #e8d8c8">
    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:6px">
      <div>
        <strong style="text-transform:capitalize"><?php echo e($p->tipo); ?></strong>
        <span style="color:#8a6a50;font-size:.82rem"> · <?php echo e($p->rango_ini); ?> a <?php echo e($p->rango_fin); ?></span>
      </div>
      <form method="POST" action="<?php echo e(route('presupuestos.destroy', $p->id_presupuesto)); ?>" onsubmit="return confirm('¿Eliminar este presupuesto?')">
        <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
        <button type="submit" class="btn-d">Eliminar</button>
      </form>
    </div>
    <div style="background:#f0e6da;border-radius:10px;overflow:hidden;height:22px">
      <div style="width:<?php echo e(min($p->porcentaje,100)); ?>%;background:<?php echo e($color); ?>;height:100%;transition:width .3s"></div>
    </div>
    <div style="margin-top:4px;font-size:.85rem;color:#3d1f0a">
      $<?php echo e(number_format($p->gasto_real,0,'.','.')); ?> de $<?php echo e(number_format($p->monto,0,'.','.')); ?>

      <strong style="color:<?php echo e($color); ?>">(<?php echo e($p->porcentaje); ?>%)</strong>
      <?php if($p->porcentaje >= 100): ?> <span class="badge br">⛔ Presupuesto superado</span>
      <?php elseif($p->porcentaje >= 80): ?> <span class="badge bo">⚠️ Cerca del límite</span>
      <?php endif; ?>
    </div>
  </div>
  <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
  <p style="color:#8a6a50;text-align:center;padding:20px 0">No has fijado presupuestos todavía.</p>
  <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\postres\resources\views/presupuestos/index.blade.php ENDPATH**/ ?>