<?php $__env->startSection('title','Personal'); ?>
<?php $__env->startSection('content'); ?>
<div class="sec-hdr">
  <div class="sec-title">👨‍🍳 Gestión de Personal</div>
  <div class="sec-sub">sp_listar_empleados() · sp_rendimiento_empleado()</div>
</div>

<div class="card">
  <div class="card-hdr">
    <div class="card-title">Equipo de Trabajo</div>
    <span class="chip"><?php echo e(count($empleados)); ?> empleados</span>
  </div>
  <table class="tbl">
    <thead>
      <tr>
        <th>Nombre</th><th>Cargo</th><th>Salario</th>
        <th>Hrs. Trabajadas</th><th>Ventas</th><th>Ingresos Generados</th><th>Días en empresa</th><th>Acción</th>
      </tr>
    </thead>
    <tbody>
      <?php $__empty_1 = true; $__currentLoopData = $empleados; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $e): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
      <tr>
        <td><strong><?php echo e($e->nombre); ?></strong><br><small style="color:#8a6a50"><?php echo e($e->correo); ?></small></td>
        <td><span class="chip"><?php echo e($e->cargo ?? 'Sin cargo'); ?></span></td>
        <td>$<?php echo e(number_format($e->salario,0,'.','.')); ?></td>
        <td><?php echo e($e->horas_trabajadas); ?>h</td>
        <td><span class="badge bg"><?php echo e($e->total_ventas); ?></span></td>
        <td style="color:#2d7a4f;font-weight:600">$<?php echo e(number_format($e->ingresos_generados,0,'.','.')); ?></td>
        <td><?php echo e($e->dias_empresa); ?> días</td>
        <td><a href="<?php echo e(route('empleados.show', $e->id_usuario)); ?>" class="btn-e">Ver rendimiento</a></td>
      </tr>
      <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
      <tr><td colspan="8" style="text-align:center;color:#8a6a50;padding:30px">Sin empleados registrados</td></tr>
      <?php endif; ?>
    </tbody>
  </table>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\postres\resources\views/empleados/index.blade.php ENDPATH**/ ?>