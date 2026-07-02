<?php $__env->startSection('title','Editar Usuario'); ?>
<?php $__env->startSection('content'); ?>
<div class="sec-hdr">
  <div class="sec-title">✏️ Editar Usuario #<?php echo e($usuario->id_usuario); ?></div>
</div>
<div class="fcard">
  <div class="fcard-title">Actualizar datos</div>
  <form method="POST" action="<?php echo e(route('usuarios.update', $usuario->id_usuario)); ?>">
    <?php echo csrf_field(); ?> <?php echo method_field('PUT'); ?>
    <div class="fgrid">
      <div class="fg"><label>Nombre</label>
        <input type="text" name="nombre" value="<?php echo e(old('nombre', $usuario->nombre)); ?>" class="<?php echo e($errors->has('nombre')?'is-invalid':''); ?>">
        <?php $__errorArgs = ['nombre'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="invalid-feedback"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?></div>
      <div class="fg"><label>Edad</label>
        <input type="number" name="edad" value="<?php echo e(old('edad', $usuario->edad)); ?>" min="16"></div>
      <div class="fg full"><label>Nueva contraseña (dejar vacío para no cambiar)</label>
        <input type="password" name="pass" placeholder="Mínimo 6 caracteres"></div>
    </div>
    <div style="margin-top:16px">
      <button type="submit" class="btn-p">Guardar cambios</button>
      <a href="<?php echo e(route('usuarios.index')); ?>" class="btn-s" style="margin-left:8px">Cancelar</a>
    </div>
  </form>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\postres\resources\views/usuarios/edit.blade.php ENDPATH**/ ?>