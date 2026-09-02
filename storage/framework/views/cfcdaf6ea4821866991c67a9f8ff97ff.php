<?php $__env->startSection('title','Catálogo'); ?>
<?php $__env->startSection('content'); ?>
<div class="sec-hdr">
  <div class="sec-title">🍰 Catálogo de Postres</div>
  <div class="sec-sub">Descubre nuestros productos artesanales</div>
</div>
<div class="pgrid">
  <?php $__empty_1 = true; $__currentLoopData = $productos; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
  <div class="pcard">
    <div class="pcard-img">🍰</div>
    <div class="pcard-body">
      <div class="pcard-name"><?php echo e($p->descripcion); ?></div>
      <div class="pcard-foot">
        <span class="pcard-price">$<?php echo e(number_format($p->precio,0,'.','.')); ?></span>
        <span class="pcard-stock"><?php echo e($p->stock > 0 ? '🟢 '.$p->stock.' disponibles' : '🔴 Agotado'); ?></span>
      </div>
    </div>
  </div>
  <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
  <p style="color:#8a6a50">No hay productos disponibles.</p>
  <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\postres\resources\views/catalogo/index.blade.php ENDPATH**/ ?>