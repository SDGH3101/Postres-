<?php $__env->startSection('title','Gastos y Compras'); ?>
<?php $__env->startSection('content'); ?>
<div class="sec-hdr">
  <div class="sec-title">📋 Registro de Gastos y Compras</div>
  <div class="sec-sub">sp_listar_gastos() · sp_listar_compras_menores() · sp_gastos_por_categoria()</div>
</div>

<div class="stats-row">
  <div class="stat c2"><div class="ic">💸</div>
    <div class="val">$<?php echo e(number_format($totalGastos/1000,1,'.','.')); ?>K</div>
    <div class="lbl">Total egresos</div></div>
  <div class="stat c1"><div class="ic">📊</div>
    <div class="val"><?php echo e(count($gastos)); ?></div>
    <div class="lbl">Gastos generales</div></div>
  <div class="stat c4"><div class="ic">🛒</div>
    <div class="val"><?php echo e(count($compras)); ?></div>
    <div class="lbl">Compras menores</div></div>
</div>

<div class="fcard">
  <div class="fcard-title">➕ Registrar Egreso</div>
  <form method="POST" action="<?php echo e(route('compras.store')); ?>">
    <?php echo csrf_field(); ?>
    <div class="fgrid c3">
      <div class="fg"><label>Tipo</label>
        <select name="tipo" onchange="toggleCat(this.value)">
          <option value="gasto">💡 Gasto General</option>
          <option value="compra">🛒 Compra Menor</option>
        </select></div>
      <div class="fg"><label>Fecha</label>
        <input type="date" name="fecha" value="<?php echo e(now()->format('Y-m-d')); ?>"></div>
      <div class="fg"><label>Monto ($)</label>
        <input type="number" name="monto" placeholder="Ej: 45000" min="1"></div>
      <div class="fg" id="wrap-cat-gasto"><label>Categoría (gasto)</label>
        <select name="categoria">
          <option value="Servicios">💡 Servicios Públicos</option>
          <option value="Nómina">👔 Nómina</option>
          <option value="Insumos">🌾 Insumos</option>
          <option value="Marketing">📢 Marketing</option>
          <option value="Equipos">🔧 Equipos</option>
          <option value="Otros">🚚 Otros</option>
        </select></div>
      <div class="fg" id="wrap-cat-compra" style="display:none"><label>Categoría (compra)</label>
        <select name="id_categoria">
          <?php $__currentLoopData = $categorias; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
          <option value="<?php echo e($cat->id_categoria); ?>"><?php echo e($cat->nombre_categoria); ?></option>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </select></div>
      <div class="fg full"><label>Descripción detallada</label>
        <input type="text" name="descripcion" placeholder="Ej: Compra de 5kg de fresas frescas"></div>
    </div>
    <div style="margin-top:14px"><button type="submit" class="btn-p">Registrar Egreso</button></div>
  </form>
</div>

<div class="card">
  <div class="card-hdr"><div class="card-title">Gastos Generales</div><a href="<?php echo e(route('reportes.gastos.pdf')); ?>" class="chip">📄 PDF</a></div>
  <table class="tbl">
    <thead><tr><th>Fecha</th><th>Descripción</th><th>Categoría</th><th>Monto</th><th>Presupuesto</th><th>Acción</th></tr></thead>
    <tbody>
      <?php $__empty_1 = true; $__currentLoopData = $gastos; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $g): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
      <tr>
        <td><?php echo e($g->fecha); ?></td>
        <td><strong><?php echo e($g->descripcion); ?></strong></td>
        <td><span class="chip"><?php echo e($g->categoria); ?></span></td>
        <td style="color:#c0392b;font-weight:600">-$<?php echo e(number_format($g->monto,0,'.','.')); ?></td>
        <td>$<?php echo e(number_format($g->presupuesto,0,'.','.')); ?></td>
        <td>
          <a href="<?php echo e(route('compras.edit', $g->id_gasto)); ?>" class="btn-e">Editar</a>
          <form method="POST" action="<?php echo e(route('compras.destroy', $g->id_gasto)); ?>" style="display:inline" onsubmit="return confirm('¿Eliminar?')">
            <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
            <button type="submit" class="btn-d">Eliminar</button>
          </form>
        </td>
      </tr>
      <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
      <tr><td colspan="6" style="text-align:center;color:#8a6a50">Sin gastos</td></tr>
      <?php endif; ?>
    </tbody>
  </table>
</div>

<div class="card">
  <div class="card-hdr"><div class="card-title">Compras Menores</div></div>
  <table class="tbl">
    <thead><tr><th>Fecha</th><th>Descripción</th><th>Categoría</th><th>Monto</th></tr></thead>
    <tbody>
      <?php $__empty_1 = true; $__currentLoopData = $compras; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $c): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
      <tr>
        <td><?php echo e($c->fecha); ?></td>
        <td><?php echo e($c->descripcion); ?></td>
        <td><span class="chip"><?php echo e($c->categoria); ?></span></td>
        <td style="color:#c0392b;font-weight:600">-$<?php echo e(number_format($c->monto,0,'.','.')); ?></td>
      </tr>
      <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
      <tr><td colspan="4" style="text-align:center;color:#8a6a50">Sin compras</td></tr>
      <?php endif; ?>
    </tbody>
  </table>
</div>
<?php $__env->stopSection(); ?>
<?php $__env->startPush('scripts'); ?>
<script>
function toggleCat(v){
  document.getElementById('wrap-cat-gasto').style.display = v==='gasto'?'':'none';
  document.getElementById('wrap-cat-compra').style.display= v==='compra'?'':'none';
}
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\postres\resources\views/compras/index.blade.php ENDPATH**/ ?>