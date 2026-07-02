<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1.0">
<title>Postres Laura — Crear cuenta</title>
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700;900&family=DM+Sans:wght@400;500;600&display=swap" rel="stylesheet">
<link rel="stylesheet" href="<?php echo e(asset('css/app.css')); ?>">
<style>
body{background:linear-gradient(135deg,#1a0a02 0%,#3d1f0a 45%,#7a3e1a 100%);min-height:100vh;display:flex;}
.auth-wrap{display:flex;width:100%;}
.auth-brand{flex:1;display:flex;flex-direction:column;justify-content:center;align-items:center;padding:60px 50px;}
.brand-logo{font-family:'Playfair Display',serif;font-size:3.8rem;font-weight:900;color:white;line-height:1;text-align:center;margin-bottom:10px;}
.brand-logo em{color:#e8a84c;font-style:normal;}
.brand-sub{font-size:.78rem;color:rgba(255,255,255,.5);letter-spacing:3px;text-transform:uppercase;margin-bottom:50px;}
.auth-card{width:480px;background:#fdf8f2;display:flex;flex-direction:column;justify-content:center;padding:50px 46px;box-shadow:-20px 0 60px rgba(0,0,0,.25);overflow-y:auto;}
.auth-tabs{display:flex;margin-bottom:28px;border-radius:12px;overflow:hidden;border:2px solid #e8d8c8;}
.auth-tab{flex:1;padding:11px;text-align:center;font-size:.82rem;font-weight:600;color:#8a6a50;background:white;transition:all .25s;text-decoration:none;display:block;}
.auth-tab.on{background:#3d1f0a;color:white;}
.auth-head h2{font-family:'Playfair Display',serif;font-size:1.8rem;font-weight:700;color:#3d1f0a;}
.auth-head p{font-size:.83rem;color:#8a6a50;margin-top:4px;margin-bottom:22px;}
.btn-auth{width:100%;padding:13px;background:linear-gradient(135deg,#3d1f0a,#c8773a);color:white;border:none;border-radius:10px;font-family:'DM Sans',sans-serif;font-size:.92rem;font-weight:600;cursor:pointer;margin-top:8px;transition:transform .2s;}
.btn-auth:hover{transform:translateY(-2px);}
@media(max-width:800px){.auth-brand{display:none;}.auth-card{width:100%;}}
</style>
</head>
<body>
<div class="auth-wrap">
  <div class="auth-brand">
    <div class="brand-logo">🍰 Postres<br><em>Laura</em></div>
    <p class="brand-sub">Sistema de Gestión · SENA 2026</p>
    <p style="color:rgba(255,255,255,.6);font-size:.9rem;text-align:center;max-width:280px;line-height:1.7">Únete a nuestra plataforma y accede al catálogo de postres artesanales 🎂</p>
  </div>

  <div class="auth-card">
    <div class="auth-tabs">
      <a href="<?php echo e(route('login')); ?>"    class="auth-tab">Iniciar sesión</a>
      <a href="<?php echo e(route('register')); ?>" class="auth-tab on">Crear cuenta</a>
    </div>

    <div class="auth-head">
      <h2>Crear cuenta 🎉</h2>
      <p>Regístrate como nuevo cliente del sistema</p>
    </div>

    <form method="POST" action="<?php echo e(route('register.post')); ?>">
      <?php echo csrf_field(); ?>
      <div class="fg" style="margin-bottom:14px">
        <label>Nombre completo</label>
        <input type="text" name="nombre" value="<?php echo e(old('nombre')); ?>" placeholder="Ej: Ana García" class="<?php echo e($errors->has('nombre') ? 'is-invalid':''); ?>">
        <?php $__errorArgs = ['nombre'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="invalid-feedback"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
      </div>
      <div class="fg" style="margin-bottom:14px">
        <label>Correo electrónico</label>
        <input type="email" name="correo" value="<?php echo e(old('correo')); ?>" placeholder="tu@correo.com" class="<?php echo e($errors->has('correo') ? 'is-invalid':''); ?>">
        <?php $__errorArgs = ['correo'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="invalid-feedback"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
      </div>
      <div class="fg" style="margin-bottom:14px">
        <label>Edad</label>
        <input type="number" name="edad" value="<?php echo e(old('edad')); ?>" placeholder="Ej: 25" min="16" max="99" class="<?php echo e($errors->has('edad') ? 'is-invalid':''); ?>">
        <?php $__errorArgs = ['edad'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="invalid-feedback"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
      </div>
      <div class="fg" style="margin-bottom:14px">
        <label>Contraseña (mín. 6 caracteres)</label>
        <input type="password" name="pass" placeholder="••••••••" class="<?php echo e($errors->has('pass') ? 'is-invalid':''); ?>">
        <?php $__errorArgs = ['pass'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="invalid-feedback"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
      </div>
      <div class="fg" style="margin-bottom:16px">
        <label>Confirmar contraseña</label>
        <input type="password" name="pass_confirmation" placeholder="Repite tu contraseña">
      </div>
      <div class="hint-box" style="margin-bottom:14px">ℹ️ Los nuevos usuarios se registran como <strong>Clientes</strong>. El emprendedor puede cambiar roles desde administración.</div>
      <button type="submit" class="btn-auth">Crear cuenta</button>
    </form>
  </div>
</div>
</body>
</html>
<?php /**PATH C:\xampp\htdocs\postres\resources\views/auth/register.blade.php ENDPATH**/ ?>