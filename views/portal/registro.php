<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Crear Cuenta — Portal Cliente</title>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
<link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
<style>
*{margin:0;padding:0;box-sizing:border-box;}
body{font-family:'Outfit',sans-serif;min-height:100vh;display:flex;align-items:center;justify-content:center;padding:2rem 1rem;
  background:linear-gradient(135deg,#1a1a2e 0%,#16213e 50%,#0f3460 100%);}
.auth-card{background:#fff;border-radius:24px;padding:2.5rem;width:100%;max-width:460px;box-shadow:0 24px 64px rgba(0,0,0,.3);}
.auth-logo{text-align:center;margin-bottom:1.8rem;}
.auth-logo span{font-size:2.2rem;}
.auth-logo h2{font-size:1.4rem;font-weight:800;color:#1a1a2e;margin-top:.3rem;}
.auth-logo p{color:#6c757d;font-size:.9rem;}
.row-2{display:grid;grid-template-columns:1fr 1fr;gap:1rem;}
.form-group{margin-bottom:1rem;}
.form-group label{display:block;font-weight:600;font-size:.87rem;color:#2d2d2d;margin-bottom:.35rem;}
.form-control{width:100%;padding:.72rem 1rem;border:2px solid #e8e8e8;border-radius:12px;font-family:inherit;font-size:.93rem;transition:.2s;outline:none;}
.form-control:focus{border-color:#FF6B35;box-shadow:0 0 0 3px #FF6B3520;}
.btn-primary-auth{width:100%;background:#FF6B35;color:#fff;border:none;padding:.85rem;border-radius:12px;font-family:inherit;font-size:1rem;font-weight:700;cursor:pointer;transition:.2s;margin-top:.4rem;}
.btn-primary-auth:hover{background:#e55a26;transform:translateY(-1px);}
.auth-footer{text-align:center;margin-top:1.3rem;font-size:.9rem;color:#6c757d;}
.auth-footer a{color:#FF6B35;font-weight:600;text-decoration:none;}
.back-link{display:flex;align-items:center;justify-content:center;gap:.4rem;margin-top:.7rem;color:#6c757d;font-size:.85rem;text-decoration:none;}
.back-link:hover{color:#FF6B35;}
.alert-err{background:#fff0f0;color:#c0392b;border-left:4px solid #e74c3c;padding:.8rem 1rem;border-radius:10px;margin-bottom:1rem;font-size:.9rem;font-weight:600;}
.hint{font-size:.78rem;color:#aaa;margin-top:.2rem;}
.required{color:#FF6B35;}
</style>
</head>
<body>
<div class="auth-card">
  <div class="auth-logo">
    <span>🎉</span>
    <h2>Crear tu cuenta</h2>
    <p>Regístrate gratis y empieza a pedir</p>
  </div>

  <?php if (!empty($_SESSION['portal_error'])): ?>
  <div class="alert-err"><i class="bi bi-exclamation-triangle-fill"></i>
    <?= $_SESSION['portal_error']; unset($_SESSION['portal_error']); ?>
  </div>
  <?php endif; ?>

  <form method="POST" action="index.php?route=portal_registro">
    <div class="form-group">
      <label>Nombre completo <span class="required">*</span></label>
      <input type="text" name="nombre" class="form-control" placeholder="Ej: Ana García" required maxlength="150" value="<?= htmlspecialchars($_POST['nombre'] ?? '') ?>">
    </div>
    <div class="row-2">
      <div class="form-group">
        <label>Correo electrónico <span class="required">*</span></label>
        <input type="email" name="email" class="form-control" placeholder="tu@correo.com" required value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
      </div>
      <div class="form-group">
        <label>Teléfono</label>
        <input type="tel" name="telefono" class="form-control" placeholder="+51 987..." value="<?= htmlspecialchars($_POST['telefono'] ?? '') ?>">
      </div>
    </div>
    <div class="form-group">
      <label>Dirección de entrega</label>
      <input type="text" name="direccion" class="form-control" placeholder="Av. Las Flores 123..." value="<?= htmlspecialchars($_POST['direccion'] ?? '') ?>">
      <div class="hint">Se usará como dirección predeterminada para tus pedidos.</div>
    </div>
    <div class="row-2">
      <div class="form-group">
        <label>Contraseña <span class="required">*</span></label>
        <input type="password" name="password" class="form-control" placeholder="Mínimo 6 caracteres" required minlength="6">
      </div>
      <div class="form-group">
        <label>Confirmar contraseña <span class="required">*</span></label>
        <input type="password" name="password2" class="form-control" placeholder="Repetir" required>
      </div>
    </div>
    <button type="submit" class="btn-primary-auth">
      <i class="bi bi-person-plus-fill"></i> Crear cuenta
    </button>
  </form>

  <div class="auth-footer">
    ¿Ya tienes cuenta? <a href="index.php?route=portal_login">Inicia sesión</a>
  </div>
  <a href="index.php?route=portal_menu" class="back-link">
    <i class="bi bi-arrow-left"></i> Volver al menú
  </a>
</div>
</body>
</html>
