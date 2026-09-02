<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1.0">
<title>Postres Laura — Ingresar</title>
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700;900&family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">
<link rel="stylesheet" href="{{ asset('css/app.css') }}">
<style>
body{background:linear-gradient(135deg,#1a0a02 0%,#3d1f0a 45%,#7a3e1a 100%);min-height:100vh;display:flex;align-items:center;justify-content:center;}
.auth-wrap{display:flex;min-height:100vh;width:100%;}
.auth-brand{flex:1;display:flex;flex-direction:column;justify-content:center;align-items:center;padding:60px 50px;}
.brand-logo{font-family:'Playfair Display',serif;font-size:3.8rem;font-weight:900;color:white;line-height:1;text-align:center;margin-bottom:10px;}
.brand-logo em{color:#e8a84c;font-style:normal;}
.brand-sub{font-size:.78rem;color:rgba(255,255,255,.5);letter-spacing:3px;text-transform:uppercase;margin-bottom:50px;}
.pastry-float{display:grid;grid-template-columns:repeat(3,90px);gap:14px;}
.pf{background:rgba(255,255,255,.07);border:1px solid rgba(255,255,255,.12);border-radius:16px;padding:18px 10px;text-align:center;animation:cardFloat 4s ease-in-out infinite;}
.pf:nth-child(2){animation-delay:.6s;}.pf:nth-child(3){animation-delay:1.2s;}
.pf:nth-child(4){animation-delay:1.8s;}.pf:nth-child(5){animation-delay:2.4s;}.pf:nth-child(6){animation-delay:3s;}
.pf span{font-size:1.8rem;display:block;margin-bottom:6px;}
.pf p{font-size:.65rem;color:rgba(255,255,255,.6);}
@keyframes cardFloat{0%,100%{transform:translateY(0);}50%{transform:translateY(-7px);}}
.auth-card{width:460px;background:#fdf8f2;display:flex;flex-direction:column;justify-content:center;padding:50px 46px;box-shadow:-20px 0 60px rgba(0,0,0,.25);}
.auth-tabs{display:flex;margin-bottom:32px;border-radius:12px;overflow:hidden;border:2px solid #e8d8c8;}
.auth-tab{flex:1;padding:11px;text-align:center;font-size:.82rem;font-weight:600;color:#8a6a50;background:white;transition:all .25s;text-decoration:none;display:block;}
.auth-tab.on{background:#3d1f0a;color:white;}
.auth-head h2{font-family:'Playfair Display',serif;font-size:1.8rem;font-weight:700;color:#3d1f0a;}
.auth-head p{font-size:.83rem;color:#8a6a50;margin-top:4px;margin-bottom:24px;}
.hint-box{background:rgba(200,119,58,.07);border:1.5px solid rgba(200,119,58,.25);border-radius:10px;padding:11px 14px;font-size:.73rem;color:#8a6a50;line-height:1.7;margin-bottom:18px;}
.hint-box strong{color:#c8773a;}
.btn-auth{width:100%;padding:13px;background:linear-gradient(135deg,#3d1f0a,#c8773a);color:white;border:none;border-radius:10px;font-family:'DM Sans',sans-serif;font-size:.92rem;font-weight:600;cursor:pointer;margin-top:4px;transition:transform .2s;}
.btn-auth:hover{transform:translateY(-2px);}
@media(max-width:800px){.auth-brand{display:none;}.auth-card{width:100%;}}
</style>
</head>
<body>
<div class="auth-wrap">
  <div class="auth-brand">
    <div class="brand-logo">🍰 Postres<br><em>Laura</em></div>
    <p class="brand-sub">Sistema de Gestión · SENA 2026</p>
    <div class="pastry-float">
      <div class="pf"><span>🎂</span><p>Tortas</p></div>
      <div class="pf"><span>🧁</span><p>Cupcakes</p></div>
      <div class="pf"><span>🍰</span><p>Postres</p></div>
      <div class="pf"><span>🍪</span><p>Galletas</p></div>
      <div class="pf"><span>🥐</span><p>Hojaldres</p></div>
      <div class="pf"><span>🍫</span><p>Chocolate</p></div>
    </div>
  </div>

  <div class="auth-card">
    <div class="auth-tabs">
      <a href="{{ route('login') }}"    class="auth-tab on">Iniciar sesión</a>
      <a href="{{ route('register') }}" class="auth-tab">Crear cuenta</a>
    </div>

    @if(session('success'))
      <div class="alert alert-success" style="margin-bottom:16px;">✔ {{ session('success') }}</div>
    @endif

    <div class="auth-head">
      <h2>¡Bienvenido! 👋</h2>
      <p>Accede con tu correo y contraseña</p>
    </div>

    <form method="POST" action="{{ route('login.post') }}">
      @csrf
      <div class="fg" style="margin-bottom:16px">
        <label>Correo electrónico</label>
        <input type="email" name="correo" value="{{ old('correo') }}" placeholder="correo@email.com" class="{{ $errors->has('correo') ? 'is-invalid':'' }}">
        @error('correo')<div class="invalid-feedback">{{ $message }}</div>@enderror
      </div>
      <div class="fg" style="margin-bottom:16px">
        <label>Contraseña</label>
        <input type="password" name="pass" placeholder="••••••••">
      </div>
      <div class="hint-box">
        <strong>Usuarios de prueba:</strong><br>
        👩‍💼 laura@postres.com / laura123 → Emprendedor<br>
        👨‍🍳 david@postres.com / david456 → Empleado<br>
        🛍️ maria@email.com / maria789 → Cliente
      </div>
      <button type="submit" class="btn-auth">Ingresar →</button>
    </form>
  </div>
</div>
</body>
</html>
