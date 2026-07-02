<?php $__env->startSection('title','Usuarios'); ?>
<?php $__env->startSection('content'); ?>

<div class="sec-hdr">
  <div class="sec-title">👥 Gestión de Usuarios</div>
  <div class="sec-sub">sp_listar_usuarios() · sp_buscar_usuario() · sp_usuarios_sin_rol()</div>
</div>

<div class="fcard">
  <div class="fcard-title">➕ Registrar nuevo usuario</div>
  <form method="POST" action="<?php echo e(route('usuarios.store')); ?>">
    <?php echo csrf_field(); ?>
    <div class="fgrid">
      <div class="fg"><label>Nombre completo</label>
        <input type="text" name="nombre" value="<?php echo e(old('nombre')); ?>" placeholder="Ej: Ana García" class="<?php echo e($errors->has('nombre')?'is-invalid':''); ?>">
        <?php $__errorArgs = ['nombre'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="invalid-feedback"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?></div>
      <div class="fg"><label>Correo</label>
        <input type="email" name="correo" value="<?php echo e(old('correo')); ?>" placeholder="correo@email.com" class="<?php echo e($errors->has('correo')?'is-invalid':''); ?>">
        <?php $__errorArgs = ['correo'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="invalid-feedback"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?></div>
      <div class="fg"><label>Edad</label>
        <input type="number" name="edad" value="<?php echo e(old('edad')); ?>" placeholder="25" min="16"></div>
      <div class="fg"><label>Contraseña</label>
        <input type="password" name="pass" placeholder="Mínimo 6 caracteres" class="<?php echo e($errors->has('pass')?'is-invalid':''); ?>">
        <?php $__errorArgs = ['pass'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="invalid-feedback"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?></div>
      <div class="fg full"><label>Rol</label>
        <select name="rol">
          <option value="cliente">🛍️ Cliente</option>
          <option value="empleado">👨‍🍳 Empleado</option>
          <option value="emprendedor">🏢 Emprendedor</option>
        </select></div>
    </div>
    <div style="margin-top:16px">
      <button type="submit" class="btn-p">Registrar usuario</button>
      <a href="<?php echo e(route('usuarios.index')); ?>" class="btn-s" style="margin-left:8px">Cancelar</a>
    </div>
  </form>
</div>

<div class="card">
  <div class="card-hdr">
    <div class="card-title">Usuarios del sistema</div>
    <span class="chip"><?php echo e(count($usuarios)); ?> registrados</span>
  </div>
  <table class="tbl">
    <thead>
      <tr><th>ID</th><th>Nombre</th><th>Correo</th><th>Edad</th><th>Registro</th><th>Rol</th><th>Acciones</th></tr>
    </thead>
    <tbody>
      <?php $__empty_1 = true; $__currentLoopData = $usuarios; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $u): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
      <tr>
        <td>#<?php echo e($u->id_usuario); ?></td>
        <td><strong><?php echo e($u->nombre); ?></strong></td>
        <td><?php echo e($u->correo); ?></td>
        <td><?php echo e($u->edad ?? '—'); ?> años</td>
        <td><?php echo e($u->fecha_registro); ?></td>
        <td>
          <span class="badge <?php echo e($u->rol==='emprendedor'?'bp':($u->rol==='empleado'?'bb':'bo')); ?>">
            <?php echo e($u->rol); ?>

          </span>
        </td>
        <td>
          <a href="<?php echo e(route('usuarios.show', $u->id_usuario)); ?>" class="btn-e">Ver</a>
          <a href="<?php echo e(route('usuarios.edit', $u->id_usuario)); ?>" class="btn-e">Editar</a>
          <form method="POST" action="<?php echo e(route('usuarios.destroy', $u->id_usuario)); ?>" style="display:inline" onsubmit="return confirm('¿Eliminar usuario?')">
            <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
            <button type="submit" class="btn-d">Eliminar</button>
          </form>
        </td>
      </tr>
      <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
      <tr><td colspan="7" style="text-align:center;color:#8a6a50">Sin usuarios</td></tr>
      <?php endif; ?>
    </tbody>
  </table>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\postres\resources\views/usuarios/index.blade.php ENDPATH**/ ?>