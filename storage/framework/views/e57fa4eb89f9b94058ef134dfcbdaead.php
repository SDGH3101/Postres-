<?php $__env->startSection('title','Ventas'); ?>
<?php $__env->startSection('content'); ?>
<div class="sec-hdr">
  <div class="sec-title">💰 Registro de Ventas</div>
  <div class="sec-sub">Carrito multi-producto · trg_descontar_stock · trg_ingreso_por_venta</div>
</div>

<div class="fcard">
  <div class="fcard-title">➕ Registrar Nueva Venta</div>
  <?php $__errorArgs = ['items'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="invalid-feedback" style="margin-bottom:10px"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>

  <form method="POST" action="<?php echo e(route('ventas.store')); ?>" id="form-venta">
    <?php echo csrf_field(); ?>
    <div class="fgrid c3">
      <div class="fg">
        <label>Empleado responsable</label>
        <select name="id_empleado" required>
          <option value="">— Seleccionar —</option>
          <?php $__currentLoopData = $empleados; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $e): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
          <option value="<?php echo e($e->id_usuario); ?>"><?php echo e($e->nombre); ?> (<?php echo e($e->cargo); ?>)</option>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </select>
      </div>
      <div class="fg">
        <label>Fecha</label>
        <input type="date" name="fecha" value="<?php echo e(now()->format('Y-m-d')); ?>" required>
      </div>
      <div class="fg">
        <label>Medio de pago</label>
        <select name="medio_pago" required>
          <option value="efectivo">💵 Efectivo</option>
          <option value="transferencia_nequi">📱 Transferencia Nequi</option>
          <option value="transferencia_daviplata">📱 Transferencia Daviplata</option>
          <option value="transferencia_bancaria">🏦 Transferencia bancaria</option>
          <option value="tarjeta_debito">💳 Tarjeta débito</option>
          <option value="tarjeta_credito">💳 Tarjeta crédito</option>
        </select>
      </div>
    </div>

    <div class="fgrid c3" style="margin-top:14px;align-items:end">
      <div class="fg">
        <label>Producto</label>
        <select id="cart-producto">
          <option value="">— Seleccionar —</option>
          <?php $__currentLoopData = ['postre' => '🍰 Postres', 'bebida' => '🥤 Bebidas', 'otro' => '📦 Otros']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tipo => $etiqueta): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <?php $grupo = $productos->where('tipo', $tipo); ?>
            <?php if($grupo->count()): ?>
            <optgroup label="<?php echo e($etiqueta); ?>">
              <?php $__currentLoopData = $grupo; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
              <option value="<?php echo e($p->id_producto); ?>" data-precio="<?php echo e($p->precio); ?>" data-stock="<?php echo e($p->stock); ?>" data-nombre="<?php echo e($p->descripcion); ?>">
                <?php echo e($p->descripcion); ?> · Stock: <?php echo e($p->stock); ?> · $<?php echo e(number_format($p->precio,0,'.','.')); ?>

              </option>
              <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </optgroup>
            <?php endif; ?>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </select>
      </div>
      <div class="fg">
        <label>Cantidad</label>
        <input type="number" id="cart-cantidad" min="1" value="1">
      </div>
      <div class="fg">
        <button type="button" class="btn-p" id="btn-agregar">Agregar al carrito</button>
      </div>
    </div>

    <table class="tbl" style="margin-top:14px">
      <thead><tr><th>Producto</th><th>Cantidad</th><th>Subtotal</th><th></th></tr></thead>
      <tbody id="cart-body">
        <tr id="cart-empty"><td colspan="4" style="text-align:center;color:#8a6a50;padding:16px">Carrito vacío</td></tr>
      </tbody>
      <tfoot>
        <tr><td colspan="2" style="text-align:right;font-weight:600">Total</td>
            <td id="cart-total" style="font-weight:600;color:#2d7a4f">$0</td><td></td></tr>
      </tfoot>
    </table>

    <div id="cart-inputs"></div>
    <div style="margin-top:14px">
      <button type="submit" class="btn-p" id="btn-registrar" disabled>Registrar Venta</button>
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
      <tr><th>#</th><th>Fecha</th><th>Empleado</th><th>Cargo</th><th>Productos</th><th>Medio de pago</th><th>Total</th><th>Acción</th></tr>
    </thead>
    <tbody>
      <?php
        $mediosPago = [
          'efectivo' => '💵 Efectivo',
          'transferencia_nequi' => '📱 Nequi',
          'transferencia_daviplata' => '📱 Daviplata',
          'transferencia_bancaria' => '🏦 Transferencia',
          'tarjeta_debito' => '💳 T. débito',
          'tarjeta_credito' => '💳 T. crédito',
        ];
      ?>
      <?php $__empty_1 = true; $__currentLoopData = $ventas; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $v): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
      <tr>
        <td><?php echo e($v->id_venta); ?></td>
        <td><?php echo e($v->fecha); ?></td>
        <td><?php echo e($v->empleado); ?></td>
        <td><span class="chip"><?php echo e($v->cargo); ?></span></td>
        <td><?php echo e($v->productos); ?></td>
        <td><?php echo e($mediosPago[$v->medio_pago] ?? $v->medio_pago); ?></td>
        <td style="color:#2d7a4f;font-weight:600">$<?php echo e(number_format($v->total,0,'.','.')); ?></td>
        <td>
          <form method="POST" action="<?php echo e(route('ventas.destroy', $v->id_venta)); ?>" style="display:inline" onsubmit="return confirm('¿Eliminar venta?')">
            <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
            <button type="submit" class="btn-d">Eliminar</button>
          </form>
        </td>
      </tr>
      <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
      <tr><td colspan="8" style="text-align:center;color:#8a6a50;padding:30px">Sin ventas registradas</td></tr>
      <?php endif; ?>
    </tbody>
  </table>
</div>

<script>
(function () {
  const cart = []; // { id_producto, nombre, cantidad, precio, subtotal }

  const selProducto = document.getElementById('cart-producto');
  const inpCantidad = document.getElementById('cart-cantidad');
  const btnAgregar  = document.getElementById('btn-agregar');
  const cartBody    = document.getElementById('cart-body');
  const cartTotal   = document.getElementById('cart-total');
  const cartInputs  = document.getElementById('cart-inputs');
  const btnRegistrar= document.getElementById('btn-registrar');

  function money(n) {
    return '$' + Math.round(n).toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.');
  }

  function render() {
    cartBody.innerHTML = '';
    cartInputs.innerHTML = '';
    if (cart.length === 0) {
      cartBody.innerHTML = '<tr><td colspan="4" style="text-align:center;color:#8a6a50;padding:16px">Carrito vacío</td></tr>';
      btnRegistrar.disabled = true;
      cartTotal.textContent = '$0';
      return;
    }
    let total = 0;
    cart.forEach((item, i) => {
      total += item.subtotal;
      const tr = document.createElement('tr');
      tr.innerHTML = `
        <td>${item.nombre}</td>
        <td>${item.cantidad}</td>
        <td>${money(item.subtotal)}</td>
        <td><button type="button" class="btn-d" data-i="${i}">Quitar</button></td>`;
      cartBody.appendChild(tr);

      cartInputs.innerHTML += `
        <input type="hidden" name="items[${i}][id_producto]" value="${item.id_producto}">
        <input type="hidden" name="items[${i}][cantidad]" value="${item.cantidad}">`;
    });
    cartTotal.textContent = money(total);
    btnRegistrar.disabled = false;
  }

  btnAgregar.addEventListener('click', function () {
    const opt = selProducto.options[selProducto.selectedIndex];
    if (!selProducto.value) return;

    const cantidad = parseInt(inpCantidad.value, 10) || 0;
    const stock    = parseInt(opt.dataset.stock, 10);
    if (cantidad < 1) return;

    // Sumar lo que ya está en el carrito de ese mismo producto para no pasarse del stock
    const yaEnCarrito = cart
      .filter(i => i.id_producto === opt.value)
      .reduce((s, i) => s + i.cantidad, 0);

    if (yaEnCarrito + cantidad > stock) {
      alert(`Stock insuficiente. Disponible: ${stock}, ya tienes ${yaEnCarrito} en el carrito.`);
      return;
    }

    const precio = parseFloat(opt.dataset.precio);
    cart.push({
      id_producto: opt.value,
      nombre: opt.dataset.nombre,
      cantidad: cantidad,
      precio: precio,
      subtotal: precio * cantidad,
    });

    inpCantidad.value = 1;
    selProducto.value = '';
    render();
  });

  cartBody.addEventListener('click', function (e) {
    if (e.target.dataset.i !== undefined) {
      cart.splice(parseInt(e.target.dataset.i, 10), 1);
      render();
    }
  });

  render();
})();
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\postres\resources\views/compras/ventas.blade.php ENDPATH**/ ?>