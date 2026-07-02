<?php $__env->startSection('title','Inventario'); ?>
<?php $__env->startSection('content'); ?>
<div class="sec-hdr">
  <div class="sec-title">🍰 Gestión de Productos</div>
  <div class="sec-sub">Inventario completo — CRUD con control de stock</div>
</div>

<div class="fcard">
  <div class="fcard-title">➕ Agregar nuevo producto</div>
  <form method="POST" action="<?php echo e(route('productos.store')); ?>">
    <?php echo csrf_field(); ?>
    <div class="fgrid c3">
      <div class="fg"><label>Nombre / Descripción</label>
        <input type="text" name="descripcion" value="<?php echo e(old('descripcion')); ?>" placeholder="Torta de naranja" class="<?php echo e($errors->has('descripcion')?'is-invalid':''); ?>">
        <?php $__errorArgs = ['descripcion'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="invalid-feedback"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?></div>
      <div class="fg"><label>Precio ($)</label>
        <input type="number" name="precio" value="<?php echo e(old('precio')); ?>" placeholder="85000" min="0">
      </div>
      <div class="fg"><label>Stock inicial</label>
        <input type="number" name="stock" value="<?php echo e(old('stock',0)); ?>" min="0" placeholder="10">
      </div>
    </div>
    <div style="margin-top:14px"><button type="submit" class="btn-p">Agregar producto</button></div>
  </form>
</div>

<div class="card">
  <div class="card-hdr">
    <div class="card-title">Listado de Inventario</div>
    <span class="chip"><?php echo e($productos->count()); ?> productos</span>
  </div>
  <table class="tbl">
    <thead>
      <tr><th>ID</th><th>Producto</th><th>Precio</th><th>Stock</th><th>Estado</th><th>Acciones</th></tr>
    </thead>
    <tbody>
      <?php $__empty_1 = true; $__currentLoopData = $productos; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
      <tr>
        <td>#<?php echo e($p->id_producto); ?></td>
        <td><strong><?php echo e($p->descripcion); ?></strong></td>
        <td>$<?php echo e(number_format($p->precio,0,'.','.')); ?></td>
        <td><?php echo e($p->stock); ?> unds</td>
        <td><span class="badge <?php echo e($p->stock > 0 ? 'bg':'br'); ?>"><?php echo e($p->stock > 0 ? 'Disponible':'Agotado'); ?></span></td>
        <td>
          <a href="<?php echo e(route('productos.edit', $p->id_producto)); ?>" class="btn-e">Editar</a>
          <form method="POST" action="<?php echo e(route('productos.destroy', $p->id_producto)); ?>" style="display:inline" onsubmit="return confirm('¿Eliminar producto?')">
            <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
            <button type="submit" class="btn-d">Eliminar</button>
          </form>
        </td>
      </tr>
      <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
      <tr><td colspan="6" style="text-align:center;color:#8a6a50">Sin productos registrados</td></tr>
      <?php endif; ?>
    </tbody>
  </table>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\postres\resources\views/productos/index.blade.php ENDPATH**/ ?>