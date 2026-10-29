<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Confirmar Pedido â€” Portal Cliente</title>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
<link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
<style>
:root{--primary:#FF6B35;--dark:#1a1a2e;}
*{margin:0;padding:0;box-sizing:border-box;}
body{font-family:'Outfit',sans-serif;background:#f5f5f7;color:#2d2d2d;}
.portal-nav{position:sticky;top:0;z-index:100;background:rgba(255,255,255,.92);backdrop-filter:blur(14px);border-bottom:1px solid #e8e8e8;box-shadow:0 2px 12px rgba(0,0,0,.05);}
.portal-nav-inner{max-width:1100px;margin:0 auto;padding:.9rem 1.5rem;display:flex;align-items:center;justify-content:space-between;}
.portal-brand{display:flex;align-items:center;gap:.5rem;font-size:1.2rem;font-weight:700;color:var(--primary);text-decoration:none;}
.back-btn{display:flex;align-items:center;gap:.4rem;color:#6c757d;text-decoration:none;font-size:.9rem;font-weight:600;padding:.4rem .9rem;border-radius:10px;transition:.2s;}
.back-btn:hover{background:#f0f0f0;color:var(--primary);}

.checkout-wrap{max-width:900px;margin:2rem auto;padding:0 1rem;display:grid;grid-template-columns:1fr 360px;gap:1.5rem;}
.checkout-card{background:#fff;border-radius:20px;padding:1.8rem;box-shadow:0 4px 24px rgba(0,0,0,.07);}
.card-title{font-size:1.1rem;font-weight:700;color:var(--dark);margin-bottom:1.2rem;display:flex;align-items:center;gap:.5rem;}
.form-group{margin-bottom:1.1rem;}
.form-group label{display:block;font-weight:600;font-size:.87rem;margin-bottom:.4rem;}
.form-control{width:100%;padding:.72rem 1rem;border:2px solid #e8e8e8;border-radius:12px;font-family:inherit;font-size:.93rem;outline:none;transition:.2s;}
.form-control:focus{border-color:var(--primary);box-shadow:0 0 0 3px #FF6B3520;}
.modalidad-options{display:grid;grid-template-columns:1fr 1fr;gap:.8rem;margin-bottom:1rem;}
.modalidad-opt{cursor:pointer;}
.modalidad-opt input{display:none;}
.modalidad-label{display:flex;flex-direction:column;align-items:center;gap:.4rem;padding:1rem;border:2px solid #e8e8e8;border-radius:14px;transition:.2s;font-weight:600;font-size:.9rem;text-align:center;}
.modalidad-opt input:checked + .modalidad-label{border-color:var(--primary);background:#fff3ee;color:var(--primary);}
.modalidad-label i{font-size:1.8rem;}

/* Resumen */
.summary-item{display:flex;gap:.7rem;padding:.7rem 0;border-bottom:1px solid #f0f0f0;align-items:center;}
.summary-item-img{width:44px;height:44px;background:#f8f8f8;border-radius:10px;display:flex;align-items:center;justify-content:center;font-size:1.3rem;flex-shrink:0;}
.summary-item-name{flex:1;font-weight:600;font-size:.9rem;}
.summary-item-qty{font-size:.82rem;color:#6c757d;}
.summary-item-price{font-weight:700;color:var(--primary);}
.summary-total-row{display:flex;justify-content:space-between;padding:.5rem 0;font-size:.93rem;}
.summary-total-row.big{font-weight:800;font-size:1.1rem;border-top:2px solid #eee;margin-top:.3rem;padding-top:.8rem;}
.btn-place-order{width:100%;background:var(--primary);color:#fff;border:none;padding:.9rem;border-radius:14px;font-family:inherit;font-size:1rem;font-weight:700;cursor:pointer;transition:.2s;margin-top:1rem;display:flex;align-items:center;justify-content:center;gap:.5rem;}
.btn-place-order:hover{background:#e55a26;transform:translateY(-1px);}
.alert-err{background:#fff0f0;color:#c0392b;border-left:4px solid #e74c3c;padding:.8rem 1rem;border-radius:10px;margin-bottom:1rem;font-size:.9rem;}
.envio-row{display:none;}
@media(max-width:700px){.checkout-wrap{grid-template-columns:1fr;}}
@keyframes spin{from{transform:rotate(0deg);}to{transform:rotate(360deg);}}
</style>
</head>
<body>
<nav class="portal-nav">
  <div class="portal-nav-inner">
    <a href="index.php?route=portal_menu" class="portal-brand">ðŸ½ï¸ <?= htmlspecialchars($conf['nombre_comercial'] ?? 'SaborLocal') ?></a>
    <a href="index.php?route=portal_menu" class="back-btn"><i class="bi bi-arrow-left"></i> Seguir comprando</a>
  </div>
</nav>

<?php
use App\Helpers\PortalAuthHelper;
$carrito = PortalAuthHelper::carritoGet();
$moneda  = $conf['moneda_simbolo'] ?? 'S/';
$costoEnvioDefault = (float)($conf['costo_envio_predeterminado'] ?? 5);
$subtotal = PortalAuthHelper::carritoTotal();
?>

<div class="checkout-wrap">
  <!-- Formulario -->
  <div>
    <?php if (!empty($_SESSION['portal_error'])): ?>
    <div class="alert-err"><?= $_SESSION['portal_error']; unset($_SESSION['portal_error']); ?></div>
    <?php endif; ?>

    <div class="checkout-card" style="margin-bottom:1.2rem;">
      <div class="card-title"><i class="bi bi-person-circle"></i> Tus datos</div>
      <div style="background:#f8f9fa;border-radius:12px;padding:1rem;font-size:.93rem;">
        <div><strong><?= htmlspecialchars(PortalAuthHelper::nombre()) ?></strong></div>
        <div style="color:#6c757d;"><?= htmlspecialchars(PortalAuthHelper::email()) ?></div>
        <?php if (!empty($cliente['telefono'])): ?>
        <div style="color:#6c757d;"><?= htmlspecialchars($cliente['telefono']) ?></div>
        <?php endif; ?>
      </div>
    </div>

    <form method="POST" action="index.php?route=portal_checkout_store" class="checkout-card">
      <div class="card-title"><i class="bi bi-truck"></i> Modalidad de entrega</div>

      <div class="modalidad-options">
        <label class="modalidad-opt">
          <input type="radio" name="modalidad" value="domicilio" checked onchange="toggleEnvio(this.value)">
          <div class="modalidad-label">
            <i class="bi bi-house-door"></i>
            Delivery
            <span style="font-size:.78rem;font-weight:400;color:#6c757d;"><?= $moneda ?> <?= number_format($costoEnvioDefault, 2) ?></span>
          </div>
        </label>
        <label class="modalidad-opt">
          <input type="radio" name="modalidad" value="recojo" onchange="toggleEnvio(this.value)">
          <div class="modalidad-label">
            <i class="bi bi-shop"></i>
            Recojo en local
            <span style="font-size:.78rem;font-weight:400;color:#2EC4B6;">Gratis</span>
          </div>
        </label>
      </div>

      <div class="form-group envio-row" id="envioRow" style="display:block;">
        <label>DirecciÃ³n de entrega</label>
        <input type="text" name="direccion_entrega" class="form-control"
          placeholder="Av. Las Flores 123, Piso 2..."
          value="<?= htmlspecialchars($cliente['direccion'] ?? '') ?>">
      </div>

      <div class="form-group">
        <label>Notas para el pedido (opcional)</label>
        <textarea name="notas" class="form-control" rows="2"
          placeholder="Ej: Sin cebolla, extra salsa, tocar el timbre..."><?= htmlspecialchars($_POST['notas'] ?? '') ?></textarea>
      </div>

      <button type="submit" class="btn-place-order" id="btnConfirmar">
        <i class="bi bi-check-circle-fill"></i> Confirmar pedido
      </button>
    </form>
  </div>

  <!-- Resumen -->
  <div class="checkout-card" style="height:fit-content;position:sticky;top:80px;">
    <div class="card-title"><i class="bi bi-receipt"></i> Resumen</div>

    <?php foreach ($carrito as $item): ?>
    <div class="summary-item">
      <div class="summary-item-img">ðŸ½ï¸</div>
      <div class="summary-item-name">
        <?= htmlspecialchars($item['nombre']) ?>
        <div class="summary-item-qty">x <?= $item['cantidad'] ?></div>
      </div>
      <div class="summary-item-price"><?= $moneda ?> <?= number_format($item['subtotal'], 2) ?></div>
    </div>
    <?php endforeach; ?>

    <div style="margin-top:.8rem;">
      <div class="summary-total-row"><span>Subtotal</span><span><?= $moneda ?> <?= number_format($subtotal, 2) ?></span></div>
      <div class="summary-total-row" id="envioResumen"><span>EnvÃ­o</span><span><?= $moneda ?> <?= number_format($costoEnvioDefault, 2) ?></span></div>
      <div class="summary-total-row big" id="totalResumen">
        <span>Total</span>
        <span id="totalVal"><?= $moneda ?> <?= number_format($subtotal + $costoEnvioDefault, 2) ?></span>
      </div>
    </div>
  </div>
</div>

<script>
const subtotal = <?= $subtotal ?>;
const costoEnvio = <?= $costoEnvioDefault ?>;
const moneda = '<?= $moneda ?>';

function toggleEnvio(val) {
  const envioRow = document.getElementById('envioRow');
  const envioRes = document.getElementById('envioResumen');
  const totalVal = document.getElementById('totalVal');
  if(val === 'domicilio'){
    envioRow.style.display = 'block';
    envioRes.style.display = 'flex';
    totalVal.textContent = moneda + ' ' + (subtotal + costoEnvio).toFixed(2);
  } else {
    envioRow.style.display = 'none';
    envioRes.style.display = 'none';
    totalVal.textContent = moneda + ' ' + subtotal.toFixed(2);
  }
}

// Prevenir doble envío sin cancelar la sumisión del formulario
const checkoutForm = document.querySelector('form[action*="portal_checkout_store"]');
if (checkoutForm) {
  let submitted = false;
  checkoutForm.addEventListener('submit', function(e) {
    if (submitted) {
      e.preventDefault();
      return false;
    }
    submitted = true;
    const btn = document.getElementById('btnConfirmar');
    if (btn) {
      btn.style.pointerEvents = 'none';
      btn.style.opacity = '0.85';
      btn.innerHTML = '<i class="bi bi-arrow-repeat" style="animation: spin 1s linear infinite; display: inline-block;"></i> Procesando pedido...';
    }
  });
}
</script>
</body>
</html>
