<?php $__env->startSection('title','Nuevo Producto'); ?>
<?php $__env->startSection('content'); ?>
<div class="sec-hdr">
  <div class="sec-title">➕ Agregar Producto</div>
</div>

<div class="fcard" style="max-width:560px">
  <div class="fcard-title">Nuevo producto al inventario</div>
  <form method="POST" action="<?php echo e(route('productos.store')); ?>">
    <?php echo csrf_field(); ?>
    <div class="fgrid c2">
      <div class="fg full">
        <label>Descripción del producto</label>
        <input type="text" name="descripcion" value="<?php echo e(old('descripcion')); ?>" placeholder="Ej: Torta de Chocolate 1kg" required>
        <?php $__errorArgs = ['descripcion'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="invalid-feedback"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
      </div>
      <div class="fg">
        <label>Tipo de producto</label>
        <select name="tipo" required>
          <option value="postre" <?php echo e(old('tipo','postre')=='postre'?'selected':''); ?>>🍰 Postre</option>
          <option value="bebida" <?php echo e(old('tipo')=='bebida'?'selected':''); ?>>🥤 Bebida</option>
          <option value="otro" <?php echo e(old('tipo')=='otro'?'selected':''); ?>>📦 Otro</option>
        </select>
        <?php $__errorArgs = ['tipo'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="invalid-feedback"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
      </div>
      <div class="fg">
        <label>Precio ($)</label>
        <input type="number" name="precio" value="<?php echo e(old('precio')); ?>" placeholder="Ej: 85000" min="0" step="100" required>
        <?php $__errorArgs = ['precio'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="invalid-feedback"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
      </div>
      <div class="fg">
        <label>Stock inicial</label>
        <input type="number" name="stock" value="<?php echo e(old('stock', 0)); ?>" placeholder="Ej: 10" min="0" required>
        <?php $__errorArgs = ['stock'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="invalid-feedback"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
      </div>
      <div class="fg">
        <label>Stock mínimo (alerta)</label>
        <input type="number" name="stock_minimo" value="<?php echo e(old('stock_minimo', 5)); ?>" min="0">
        <?php $__errorArgs = ['stock_minimo'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="invalid-feedback"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
      </div>
      <div class="fg">
        <label>Fecha de caducidad</label>
        <input type="date" name="fecha_caducidad" value="<?php echo e(old('fecha_caducidad')); ?>">
        <?php $__errorArgs = ['fecha_caducidad'];
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
      <button type="submit" class="btn-p">Agregar producto</button>
      <a href="<?php echo e(route('productos.index')); ?>" class="btn-p" style="background:#8a6a50">Cancelar</a>
    </div>
  </form>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\postres\resources\views/productos/create.blade.php ENDPATH**/ ?>