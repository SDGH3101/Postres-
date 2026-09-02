<?php $__env->startSection('title','Editar Producto'); ?>
<?php $__env->startSection('content'); ?>
<div class="sec-hdr"><div class="sec-title">✏️ Editar Producto #<?php echo e($producto->id_producto); ?></div></div>
<div class="fcard">
  <form method="POST" action="<?php echo e(route('productos.update', $producto->id_producto)); ?>">
    <?php echo csrf_field(); ?> <?php echo method_field('PUT'); ?>
    <div class="fgrid c3">
      <div class="fg"><label>Descripción</label>
        <input type="text" name="descripcion" value="<?php echo e(old('descripcion',$producto->descripcion)); ?>" class="<?php echo e($errors->has('descripcion')?'is-invalid':''); ?>">
        <?php $__errorArgs = ['descripcion'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="invalid-feedback"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?></div>
      <div class="fg"><label>Tipo de producto</label>
        <select name="tipo">
          <option value="postre" <?php echo e(old('tipo',$producto->tipo)=='postre'?'selected':''); ?>>🍰 Postre</option>
          <option value="bebida" <?php echo e(old('tipo',$producto->tipo)=='bebida'?'selected':''); ?>>🥤 Bebida</option>
          <option value="otro" <?php echo e(old('tipo',$producto->tipo)=='otro'?'selected':''); ?>>📦 Otro</option>
        </select></div>
      <div class="fg"><label>Precio ($)</label>
        <input type="number" name="precio" value="<?php echo e(old('precio',$producto->precio)); ?>" min="0"></div>
      <div class="fg"><label>Stock</label>
        <input type="number" name="stock" value="<?php echo e(old('stock',$producto->stock)); ?>" min="0"></div>
      <div class="fg"><label>Stock mínimo (alerta)</label>
        <input type="number" name="stock_minimo" value="<?php echo e(old('stock_minimo',$producto->stock_minimo)); ?>" min="0"></div>
      <div class="fg"><label>Fecha de caducidad</label>
        <input type="date" name="fecha_caducidad" value="<?php echo e(old('fecha_caducidad', optional($producto->fecha_caducidad)->format('Y-m-d'))); ?>"></div>
    </div>
    <div style="margin-top:16px">
      <button type="submit" class="btn-p">Guardar cambios</button>
      <a href="<?php echo e(route('productos.index')); ?>" class="btn-s" style="margin-left:8px">Cancelar</a>
    </div>
  </form>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\postres\resources\views/productos/edit.blade.php ENDPATH**/ ?>