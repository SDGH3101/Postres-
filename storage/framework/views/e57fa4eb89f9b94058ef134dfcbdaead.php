<?php $__env->startSection('title','Ventas'); ?>
<?php $__env->startSection('content'); ?>
<div class="sec-hdr">
  <div class="sec-title">💰 Registro de Ventas</div>
  <div class="sec-sub">sp_listar_ventas() · trg_descontar_stock · trg_ingreso_por_venta</div>
</div>

<div class="fcard">
  <div class="fcard-title">➕ Registrar Nueva Venta</div>
  <form method="POST" action="<?php echo e(route('ventas.store')); ?>">
    <?php echo csrf_field(); ?>
    <div class="fgrid c3">
      <div class="fg">
        <label>Producto</label>
        <select name="id_producto" required>
          <option value="">— Seleccionar —</option>
          <?php $__currentLoopData = $productos; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
          <option value="<?php echo e($p->id_producto); ?>"><?php echo e($p->descripcion); ?> · Stock: <?php echo e($p->stock); ?> · $<?php echo e(number_format($p->precio,0,'.','.')); ?></option>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </select>
        <?php $__errorArgs = ['id_producto'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="invalid-feedback"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
      </div>
      <div class="fg">
        <label>Empleado responsable</label>
        <select name="id_empleado" required>
          <option value="">— Seleccionar —</option>
          <?php $__currentLoopData = $empleados; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $e): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
          <option value="<?php echo e($e->id_usuario); ?>"><?php echo e($e->nombre); ?> (<?php echo e($e->cargo); ?>)</option>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </select>
        <?php $__errorArgs = ['id_empleado'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="invalid-feedback"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
      </div>
      <div class="fg">
        <label>Fecha</label>
        <input type="date" name="fecha" value="<?php echo e(now()->format('Y-m-d')); ?>" required>
      </div>
    </div>
    <div style="margin-top:14px">
      <button type="submit" class="btn-p">Registrar Venta</button>
    </div>
  </form>
</div>

<div class="card">
  <div class="card-hdr">
    <div class="card-title">Historial de Ventas</div>
    <span class="chip"><?php echo e(count($ventas)); ?> registros</span>
  </div>
  <table class="tbl">
    <thead>
      <tr><th>#</th><th>Fecha</th><th>Empleado</th><th>Cargo</th><th>Producto</th><th>Total</th><th>Acción</th></tr>
    </thead>
    <tbody>
      <?php $__empty_1 = true; $__currentLoopData = $ventas; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $v): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
      <tr>
        <td><?php echo e($v->id_venta); ?></td>
        <td><?php echo e($v->fecha); ?></td>
        <td><?php echo e($v->empleado); ?></td>
        <td><span class="chip"><?php echo e($v->cargo); ?></span></td>
        <td><?php echo e($v->producto); ?></td>
        <td style="color:#2d7a4f;font-weight:600">$<?php echo e(number_format($v->total,0,'.','.')); ?></td>
        <td>
          <form method="POST" action="<?php echo e(route('ventas.destroy', $v->id_venta)); ?>" style="display:inline" onsubmit="return confirm('¿Eliminar venta?')">
            <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
            <button type="submit" class="btn-d">Eliminar</button>
          </form>
        </td>
      </tr>
      <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
      <tr><td colspan="7" style="text-align:center;color:#8a6a50;padding:30px">Sin ventas registradas</td></tr>
      <?php endif; ?>
    </tbody>
  </table>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\postres\resources\views/compras/ventas.blade.php ENDPATH**/ ?>