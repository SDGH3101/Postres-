<?php $__env->startSection('title','Editar Empleado'); ?>
<?php $__env->startSection('content'); ?>
<div class="sec-hdr"><div class="sec-title">✏️ Editar Empleado #<?php echo e($empleado->id_usuario); ?></div></div>

<div class="fcard" style="max-width:600px">
  <form method="POST" action="<?php echo e(route('empleados.update', $empleado->id_usuario)); ?>">
    <?php echo csrf_field(); ?> <?php echo method_field('PUT'); ?>
    <div class="fgrid c2">
      <div class="fg full">
        <label>Nombre completo</label>
        <input type="text" name="nombre" value="<?php echo e(old('nombre', $empleado->nombre)); ?>" required>
        <?php $__errorArgs = ['nombre'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="invalid-feedback"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
      </div>
      <div class="fg">
        <label>Correo electrónico</label>
        <input type="email" name="correo" value="<?php echo e(old('correo', $empleado->correo)); ?>" required>
        <?php $__errorArgs = ['correo'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="invalid-feedback"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
      </div>
      <div class="fg">
        <label>Fecha de nacimiento</label>
        <input type="date" name="fecha_nacimiento" value="<?php echo e(old('fecha_nacimiento', $empleado->fecha_nacimiento)); ?>"
               max="<?php echo e(now()->subYears(16)->toDateString()); ?>"
               min="<?php echo e(now()->subYears(99)->toDateString()); ?>" required>
        <?php $__errorArgs = ['fecha_nacimiento'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="invalid-feedback"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
      </div>
      <div class="fg">
        <label>Nueva contraseña (opcional)</label>
        <input type="password" name="pass" placeholder="Dejar en blanco para no cambiarla">
        <?php $__errorArgs = ['pass'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="invalid-feedback"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
      </div>
      <div class="fg">
        <label>Cargo</label>
        <input type="text" name="cargo" value="<?php echo e(old('cargo', $empleado->cargo)); ?>" required>
        <?php $__errorArgs = ['cargo'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="invalid-feedback"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
      </div>
      <div class="fg">
        <label>Salario</label>
        <input type="number" name="salario" value="<?php echo e(old('salario', $empleado->salario)); ?>" min="0" step="1000" required>
        <?php $__errorArgs = ['salario'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="invalid-feedback"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
      </div>
      <div class="fg">
        <label>Horas trabajadas (mensual)</label>
        <input type="number" name="horas_trabajadas" value="<?php echo e(old('horas_trabajadas', $empleado->horas_trabajadas)); ?>" min="0" required>
        <?php $__errorArgs = ['horas_trabajadas'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="invalid-feedback"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
      </div>
    </div>
    <div style="margin-top:16px">
      <button type="submit" class="btn-p">Guardar cambios</button>
      <a href="<?php echo e(route('empleados.index')); ?>" class="btn-s" style="margin-left:8px">Cancelar</a>
    </div>
  </form>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\postres\resources\views/empleados/edit.blade.php ENDPATH**/ ?>