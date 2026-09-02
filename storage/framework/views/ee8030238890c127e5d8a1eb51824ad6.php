<?php $__env->startSection('title','Personal'); ?>
<?php $__env->startSection('content'); ?>
<div class="sec-hdr">
  <div class="sec-title">👨‍🍳 Gestión de Personal</div>
  <div class="sec-sub">sp_listar_empleados() · sp_rendimiento_empleado()</div>
</div>

<?php $__errorArgs = ['delete'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="invalid-feedback" style="margin-bottom:14px"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>

<div style="margin-bottom:14px">
  <a href="<?php echo e(route('empleados.create')); ?>" class="btn-p">➕ Contratar empleado</a>
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
        <td>
          <a href="<?php echo e(route('empleados.show', $e->id_usuario)); ?>" class="btn-e" style="background:#8a6a50">Ver</a>
          <a href="<?php echo e(route('empleados.edit', $e->id_usuario)); ?>" class="btn-e">Editar</a>
          <form method="POST" action="<?php echo e(route('empleados.destroy', $e->id_usuario)); ?>" style="display:inline" onsubmit="return confirm('¿Eliminar del cargo a este empleado? La cuenta de usuario se conserva.')">
            <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
            <button type="submit" class="btn-d">Eliminar</button>
          </form>
        </td>
      </tr>
      <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
      <tr><td colspan="8" style="text-align:center;color:#8a6a50;padding:30px">Sin empleados registrados</td></tr>
      <?php endif; ?>
    </tbody>
  </table>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\postres\resources\views/empleados/index.blade.php ENDPATH**/ ?>