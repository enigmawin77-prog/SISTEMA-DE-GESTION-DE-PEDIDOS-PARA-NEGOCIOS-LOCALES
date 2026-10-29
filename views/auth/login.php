<?php
use App\Helpers\CsrfHelper;
use App\Helpers\FlashHelper;
use App\Models\Configuracion;

$conf = Configuracion::get();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión | SaborLocal - Gestión de Pedidos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="public/assets/css/style.css">
    <style>
        body {
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .login-card {
            width: 100%;
            max-width: 440px;
            background: #ffffff;
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.25);
            padding: 36px 32px;
            border: 1px solid rgba(255, 255, 255, 0.1);
        }
        .login-logo-box {
            width: 56px;
            height: 56px;
            background: linear-gradient(135deg, #2563eb, #1d4ed8);
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #ffffff;
            font-size: 28px;
            margin: 0 auto 16px auto;
            box-shadow: 0 8px 16px rgba(37, 99, 235, 0.3);
        }
        .form-control {
            border-radius: 10px;
            padding: 11px 14px;
            font-size: 14px;
            border: 1px solid #cbd5e1;
        }
        .form-control:focus {
            border-color: #2563eb;
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.15);
        }
        .btn-login {
            background: linear-gradient(135deg, #2563eb, #1d4ed8);
            border: none;
            padding: 12px;
            font-weight: 700;
            border-radius: 12px;
            font-size: 15px;
            transition: all 0.2s ease;
        }
        .btn-login:hover {
            transform: translateY(-1px);
            box-shadow: 0 8px 20px rgba(37, 99, 235, 0.35);
        }
        .demo-credential-card {
            background-color: #f8fafc;
            border: 1px dashed #cbd5e1;
            border-radius: 12px;
            padding: 14px;
            margin-top: 24px;
        }
    </style>
</head>
<body>

<div class="login-card">
    <div class="text-center mb-4">
        <div class="login-logo-box">
            <i class="bi bi-egg-fried"></i>
        </div>
        <h2 class="h4 fw-bold text-dark mb-1">SaborLocal</h2>
        <p class="text-muted small mb-0"><?= htmlspecialchars($conf['nombre_comercial']) ?></p>
        <p class="text-secondary small">Gestión de Pedidos para Negocios Locales</p>
    </div>

    <!-- Mensajes Flash de error o confirmación -->
    <?= FlashHelper::render() ?>

    <form action="index.php?route=login" method="POST">
        <?= CsrfHelper::field() ?>

        <div class="mb-3">
            <label for="email" class="form-label small fw-bold text-dark">Correo Electrónico</label>
            <div class="input-group">
                <span class="input-group-text bg-light text-muted border-end-0 rounded-start-3"><i class="bi bi-envelope"></i></span>
                <input type="email"
                       class="form-control border-start-0 ps-0"
                       id="email"
                       name="email"
                       placeholder="ejemplo@negocio.pe"
                       value="<?= htmlspecialchars($_POST['email'] ?? 'admin@saborlocal.pe') ?>"
                       required
                       autofocus>
            </div>
        </div>

        <div class="mb-3">
            <div class="d-flex justify-content-between align-items-center mb-1">
                <label for="password" class="form-label small fw-bold text-dark mb-0">Contraseña</label>
            </div>
            <div class="input-group">
                <span class="input-group-text bg-light text-muted border-end-0 rounded-start-3"><i class="bi bi-lock"></i></span>
                <input type="password"
                       class="form-control border-start-0 ps-0"
                       id="password"
                       name="password"
                       placeholder="••••••••"
                       value="admin123"
                       required>
                <button type="button" class="btn btn-light border border-start-0 text-muted" id="btnTogglePass" onclick="togglePasswordVisibility()">
                    <i class="bi bi-eye"></i>
                </button>
            </div>
        </div>

        <div class="d-grid mt-4">
            <button type="submit" class="btn btn-primary btn-login text-white">
                <i class="bi bi-box-arrow-in-right me-1"></i> Iniciar Sesión
            </button>
        </div>
    </form>

    <!-- Credenciales de Demostración Rápidas -->
    <div class="demo-credential-card">
        <div class="small fw-bold text-dark mb-2 text-center">
            <i class="bi bi-key-fill text-warning me-1"></i> Accesos Rápidos de Demostración
        </div>
        <div class="d-flex gap-2">
            <button type="button" class="btn btn-outline-primary btn-sm flex-fill" onclick="fillCredentials('admin@saborlocal.pe', 'admin123')">
                <i class="bi bi-shield-check"></i> Administrador
            </button>
            <button type="button" class="btn btn-outline-secondary btn-sm flex-fill" onclick="fillCredentials('empleado@saborlocal.pe', 'empleado123')">
                <i class="bi bi-person"></i> Empleado
            </button>
        </div>
    </div>
</div>

<script>
function fillCredentials(email, password) {
    document.getElementById('email').value = email;
    document.getElementById('password').value = password;
}

function togglePasswordVisibility() {
    const input = document.getElementById('password');
    const icon = document.querySelector('#btnTogglePass i');
    if (input.type === 'password') {
        input.type = 'text';
        icon.classList.remove('bi-eye');
        icon.classList.add('bi-eye-slash');
    } else {
        input.type = 'password';
        icon.classList.remove('bi-eye-slash');
        icon.classList.add('bi-eye');
    }
}
</script>
</body>
</html>
