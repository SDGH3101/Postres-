<?php $__env->startSection('title','Nuevo Empleado'); ?>
<?php $__env->startSection('content'); ?>
<div class="sec-hdr">
  <div class="sec-title">👨‍🍳 Contratar Empleado</div>
</div>

<div class="fcard" style="max-width:600px">
  <div class="fcard-title">Crea la cuenta y el cargo del nuevo empleado</div>
  <form method="POST" action="<?php echo e(route('empleados.store')); ?>">
    <?php echo csrf_field(); ?>
    <div class="fgrid c2">
      <div class="fg full">
        <label>Nombre completo</label>
        <input type="text" name="nombre" value="<?php echo e(old('nombre')); ?>" placeholder="Ej: Juan Pérez" required>
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
        <input type="email" name="correo" value="<?php echo e(old('correo')); ?>" placeholder="correo@email.com" required>
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
        <input type="date" name="fecha_nacimiento" value="<?php echo e(old('fecha_nacimiento')); ?>"
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
        <label>Contraseña</label>
        <input type="password" name="pass" placeholder="Mínimo 6 caracteres" required>
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
        <input type="text" name="cargo" value="<?php echo e(old('cargo')); ?>" placeholder="Ej: Repostero, Cajero, Domiciliario" required>
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
        <input type="number" name="salario" value="<?php echo e(old('salario')); ?>" placeholder="Ej: 1300000" min="0" step="1000" required>
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
        <input type="number" name="horas_trabajadas" value="<?php echo e(old('horas_trabajadas', 0)); ?>" min="0" required>
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
    <div style="margin-top:16px;display:flex;gap:12px">
      <button type="submit" class="btn-p">Contratar empleado</button>
      <a href="<?php echo e(route('empleados.index')); ?>" class="btn-p" style="background:#8a6a50">Cancelar</a>
    </div>
  </form>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\postres\resources\views/empleados/create.blade.php ENDPATH**/ ?>