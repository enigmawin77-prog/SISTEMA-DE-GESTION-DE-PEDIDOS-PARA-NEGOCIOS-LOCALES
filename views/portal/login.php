<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Iniciar Sesión — Portal Cliente</title>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
<link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
<style>
*{margin:0;padding:0;box-sizing:border-box;}
body{font-family:'Outfit',sans-serif;min-height:100vh;display:flex;align-items:center;justify-content:center;
  background:linear-gradient(135deg,#1a1a2e 0%,#16213e 50%,#0f3460 100%);}
.auth-card{background:#fff;border-radius:24px;padding:2.5rem;width:100%;max-width:420px;box-shadow:0 24px 64px rgba(0,0,0,.3);}
.auth-logo{text-align:center;margin-bottom:1.8rem;}
.auth-logo span{font-size:2.5rem;}
.auth-logo h2{font-size:1.5rem;font-weight:800;color:#1a1a2e;margin-top:.3rem;}
.auth-logo p{color:#6c757d;font-size:.9rem;}
.form-group{margin-bottom:1.1rem;}
.form-group label{display:block;font-weight:600;font-size:.88rem;color:#2d2d2d;margin-bottom:.4rem;}
.form-control{width:100%;padding:.75rem 1rem;border:2px solid #e8e8e8;border-radius:12px;font-family:inherit;font-size:.95rem;transition:.2s;outline:none;}
.form-control:focus{border-color:#FF6B35;box-shadow:0 0 0 3px #FF6B3520;}
.btn-primary-auth{width:100%;background:#FF6B35;color:#fff;border:none;padding:.85rem;border-radius:12px;font-family:inherit;font-size:1rem;font-weight:700;cursor:pointer;transition:.2s;margin-top:.5rem;}
.btn-primary-auth:hover{background:#e55a26;transform:translateY(-1px);}
.auth-footer{text-align:center;margin-top:1.5rem;font-size:.9rem;color:#6c757d;}
.auth-footer a{color:#FF6B35;font-weight:600;text-decoration:none;}
.auth-footer a:hover{text-decoration:underline;}
.back-link{display:flex;align-items:center;justify-content:center;gap:.4rem;margin-top:.8rem;color:#6c757d;font-size:.85rem;text-decoration:none;}
.back-link:hover{color:#FF6B35;}
.alert-err{background:#fff0f0;color:#c0392b;border-left:4px solid #e74c3c;padding:.8rem 1rem;border-radius:10px;margin-bottom:1rem;font-size:.9rem;font-weight:600;}
.input-icon{position:relative;}
.input-icon i{position:absolute;left:.9rem;top:50%;transform:translateY(-50%);color:#aaa;}
.input-icon .form-control{padding-left:2.5rem;}
</style>
</head>
<body>
<div class="auth-card">
  <div class="auth-logo">
    <span>🍽️</span>
    <h2>¡Hola de nuevo!</h2>
    <p>Ingresa para ver tu menú y hacer pedidos</p>
  </div>

  <?php if (!empty($_SESSION['portal_error'])): ?>
  <div class="alert-err"><i class="bi bi-exclamation-triangle-fill"></i> <?= $_SESSION['portal_error']; unset($_SESSION['portal_error']); ?></div>
  <?php endif; ?>

  <form method="POST" action="index.php?route=portal_login">
    <div class="form-group">
      <label for="email">Correo electrónico</label>
      <div class="input-icon">
        <i class="bi bi-envelope"></i>
        <input type="email" id="email" name="email" class="form-control" placeholder="tu@correo.com" required autocomplete="email">
      </div>
    </div>
    <div class="form-group">
      <label for="password">Contraseña</label>
      <div class="input-icon">
        <i class="bi bi-lock"></i>
        <input type="password" id="password" name="password" class="form-control" placeholder="••••••••" required autocomplete="current-password">
      </div>
    </div>
    <button type="submit" class="btn-primary-auth">
      <i class="bi bi-box-arrow-in-right"></i> Ingresar
    </button>
  </form>

  <div class="auth-footer">
    ¿No tienes cuenta?
    <a href="index.php?route=portal_registro">Regístrate gratis</a>
  </div>
  <a href="index.php?route=portal_menu" class="back-link">
    <i class="bi bi-arrow-left"></i> Volver al menú
  </a>
</div>
</body>
</html>
