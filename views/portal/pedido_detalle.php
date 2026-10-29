<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Pedido #<?= htmlspecialchars($pedido['numero_pedido']) ?> — Portal</title>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
<link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
<style>
:root{--primary:#FF6B35;--dark:#1a1a2e;}
*{margin:0;padding:0;box-sizing:border-box;}
body{font-family:'Outfit',sans-serif;background:#f5f5f7;color:#2d2d2d;}
.portal-nav{position:sticky;top:0;z-index:100;background:rgba(255,255,255,.92);backdrop-filter:blur(14px);border-bottom:1px solid #e8e8e8;box-shadow:0 2px 12px rgba(0,0,0,.05);}
.portal-nav-inner{max-width:800px;margin:0 auto;padding:.9rem 1.5rem;display:flex;align-items:center;justify-content:space-between;}
.portal-brand{display:flex;align-items:center;gap:.5rem;font-size:1.2rem;font-weight:700;color:var(--primary);text-decoration:none;}
.back-btn{display:flex;align-items:center;gap:.4rem;color:#6c757d;text-decoration:none;font-size:.88rem;font-weight:600;padding:.4rem .9rem;border-radius:10px;transition:.2s;}
.back-btn:hover{background:#f0f0f0;color:var(--primary);}

.wrap{max-width:800px;margin:2rem auto;padding:0 1rem;}
.card{background:#fff;border-radius:20px;padding:1.8rem;box-shadow:0 4px 20px rgba(0,0,0,.07);margin-bottom:1.2rem;}

/* Éxito banner */
.success-banner{background:linear-gradient(135deg,#2EC4B6,#1a9e94);color:#fff;border-radius:20px;padding:2rem;text-align:center;margin-bottom:1.5rem;}
.success-banner i{font-size:3rem;display:block;margin-bottom:.5rem;}
.success-banner h2{font-size:1.5rem;font-weight:800;}
.success-banner p{font-size:.95rem;opacity:.85;margin-top:.3rem;}

/* Estado tracker */
.estado-steps{display:flex;justify-content:space-between;position:relative;margin:1.2rem 0;}
.estado-steps::before{content:'';position:absolute;top:20px;left:20px;right:20px;height:3px;background:#e0e0e0;z-index:0;}
.step{display:flex;flex-direction:column;align-items:center;gap:.4rem;flex:1;position:relative;z-index:1;}
.step-circle{width:40px;height:40px;border-radius:50%;background:#e0e0e0;display:flex;align-items:center;justify-content:center;font-size:1rem;font-weight:700;transition:.3s;}
.step.done .step-circle{background:var(--primary);color:#fff;}
.step.active .step-circle{background:var(--dark);color:#fff;box-shadow:0 0 0 4px #FF6B3540;}
.step-label{font-size:.7rem;font-weight:600;color:#aaa;text-align:center;}
.step.done .step-label,.step.active .step-label{color:var(--dark);}

/* Info rows */
.info-grid{display:grid;grid-template-columns:1fr 1fr;gap:.8rem;margin-bottom:.5rem;}
.info-item{background:#f8f9fa;border-radius:12px;padding:.9rem 1rem;}
.info-item-label{font-size:.75rem;font-weight:700;color:#aaa;text-transform:uppercase;letter-spacing:.5px;margin-bottom:.2rem;}
.info-item-value{font-size:.95rem;font-weight:600;color:var(--dark);}

/* Items */
.order-item{display:flex;gap:.8rem;padding:.8rem 0;border-bottom:1px solid #f0f0f0;align-items:center;}
.order-item:last-child{border-bottom:none;}
.order-item-img{width:50px;height:50px;background:#f8f8f8;border-radius:12px;display:flex;align-items:center;justify-content:center;font-size:1.4rem;flex-shrink:0;}
.order-item-name{flex:1;font-weight:600;font-size:.93rem;}
.order-item-qty{font-size:.82rem;color:#6c757d;}
.order-item-price{font-weight:700;color:var(--primary);}

/* Totales */
.total-row{display:flex;justify-content:space-between;padding:.45rem 0;font-size:.93rem;}
.total-row.big{font-weight:800;font-size:1.1rem;border-top:2px solid #eee;margin-top:.4rem;padding-top:.8rem;}

.btn-action{display:inline-flex;align-items:center;gap:.4rem;padding:.65rem 1.2rem;border-radius:12px;font-weight:700;font-size:.9rem;text-decoration:none;transition:.2s;}
.btn-menu{background:var(--primary);color:#fff;}
.btn-menu:hover{background:#e55a26;}
.btn-pedidos{background:#f0f0f0;color:var(--dark);}
.btn-pedidos:hover{background:#e0e0e0;}
.actions{display:flex;gap:.8rem;flex-wrap:wrap;}

.badge-estado{display:inline-flex;align-items:center;gap:.3rem;padding:.35rem .9rem;border-radius:50px;font-size:.85rem;font-weight:700;}
.badge-pendiente{background:#fff3cd;color:#856404;}
.badge-confirmado{background:#cce5ff;color:#004085;}
.badge-en_preparacion{background:#e2d9f3;color:#6f42c1;}
.badge-listo{background:#d1f0ea;color:#155724;}
.badge-en_camino{background:#fff3cd;color:#FF6B35;}
.badge-entregado{background:#d4edda;color:#155724;}
.badge-cancelado{background:#f8d7da;color:#721c24;}

@media(max-width:600px){.info-grid{grid-template-columns:1fr;}.estado-steps{gap:0;}.step-label{font-size:.62rem;}}
</style>
</head>
<body>

<nav class="portal-nav">
  <div class="portal-nav-inner">
    <a href="index.php?route=portal_menu" class="portal-brand">🍽️ <?= htmlspecialchars($conf['nombre_comercial'] ?? 'SaborLocal') ?></a>
    <a href="index.php?route=portal_mis_pedidos" class="back-btn"><i class="bi bi-arrow-left"></i> Mis Pedidos</a>
  </div>
</nav>

<?php
$moneda = $conf['moneda_simbolo'] ?? 'S/';
$isNuevo = $nuevo ?? false;

$estadoLabels = [
  'pendiente'      => ['label'=>'Pendiente',      'icon'=>'⏳','class'=>'badge-pendiente'],
  'confirmado'     => ['label'=>'Confirmado',     'icon'=>'✅','class'=>'badge-confirmado'],
  'en_preparacion' => ['label'=>'En preparación', 'icon'=>'👨‍🍳','class'=>'badge-en_preparacion'],
  'listo'          => ['label'=>'Listo',           'icon'=>'🎉','class'=>'badge-listo'],
  'en_camino'      => ['label'=>'En camino',       'icon'=>'🛵','class'=>'badge-en_camino'],
  'entregado'      => ['label'=>'Entregado',       'icon'=>'📦','class'=>'badge-entregado'],
  'cancelado'      => ['label'=>'Cancelado',       'icon'=>'❌','class'=>'badge-cancelado'],
];

$flujo = ['pendiente','confirmado','en_preparacion','listo','en_camino','entregado'];
$est = $estadoLabels[$pedido['estado']] ?? ['label'=>$pedido['estado'],'icon'=>'❓','class'=>'badge-pendiente'];
$estadoActualIdx = array_search($pedido['estado'], $flujo);
?>

<div class="wrap">
  <?php if ($isNuevo): ?>
  <div class="success-banner">
    <i class="bi bi-check-circle-fill"></i>
    <h2>¡Pedido recibido!</h2>
    <p>Tu pedido #<?= htmlspecialchars($pedido['numero_pedido']) ?> fue registrado exitosamente.</p>
  </div>
  <?php endif; ?>

  <!-- ESTADO -->
  <div class="card">
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:1.2rem;flex-wrap:wrap;gap:.5rem;">
      <div>
        <div style="font-size:.8rem;color:#aaa;font-weight:700;letter-spacing:.5px;text-transform:uppercase;">Pedido</div>
        <div style="font-size:1.2rem;font-weight:800;">#<?= htmlspecialchars($pedido['numero_pedido']) ?></div>
      </div>
      <span class="badge-estado <?= $est['class'] ?>"><?= $est['icon'] ?> <?= $est['label'] ?></span>
    </div>

    <?php if ($pedido['estado'] !== 'cancelado'): ?>
    <div class="estado-steps">
      <?php
      $pasos = [
        ['icono'=>'📝','label'=>'Pedido'],
        ['icono'=>'✅','label'=>'Confirmado'],
        ['icono'=>'👨‍🍳','label'=>'Prep.'],
        ['icono'=>'🎉','label'=>'Listo'],
        ['icono'=>'🛵','label'=>'En camino'],
        ['icono'=>'📦','label'=>'Entregado'],
      ];
      foreach ($pasos as $i => $paso):
        $done   = $estadoActualIdx > $i;
        $active = $estadoActualIdx === $i;
      ?>
      <div class="step <?= $done ? 'done' : ($active ? 'active' : '') ?>">
        <div class="step-circle"><?= $paso['icono'] ?></div>
        <div class="step-label"><?= $paso['label'] ?></div>
      </div>
      <?php endforeach; ?>
    </div>
    <?php endif; ?>
  </div>

  <!-- INFO -->
  <div class="card">
    <div style="font-weight:700;margin-bottom:.9rem;font-size:1rem;"><i class="bi bi-info-circle"></i> Información del pedido</div>
    <div class="info-grid">
      <div class="info-item">
        <div class="info-item-label">Fecha</div>
        <div class="info-item-value"><?= date('d/m/Y H:i', strtotime($pedido['fecha_pedido'])) ?></div>
      </div>
      <div class="info-item">
        <div class="info-item-label">Modalidad</div>
        <div class="info-item-value"><?= $pedido['modalidad'] === 'domicilio' ? '🏠 Delivery' : '🏪 Recojo en local' ?></div>
      </div>
      <?php if ($pedido['direccion_entrega']): ?>
      <div class="info-item" style="grid-column:1/-1;">
        <div class="info-item-label">Dirección de entrega</div>
        <div class="info-item-value"><?= htmlspecialchars($pedido['direccion_entrega']) ?></div>
      </div>
      <?php endif; ?>
      <?php if ($pedido['notas']): ?>
      <div class="info-item" style="grid-column:1/-1;">
        <div class="info-item-label">Notas</div>
        <div class="info-item-value"><?= htmlspecialchars($pedido['notas']) ?></div>
      </div>
      <?php endif; ?>
    </div>
  </div>

  <!-- ITEMS -->
  <div class="card">
    <div style="font-weight:700;margin-bottom:.5rem;font-size:1rem;"><i class="bi bi-bag"></i> Productos</div>
    <?php foreach ($items as $item): ?>
    <div class="order-item">
      <div class="order-item-img">🍽️</div>
      <div class="order-item-name">
        <?= htmlspecialchars($item['producto_nombre']) ?>
        <div class="order-item-qty">x <?= $item['cantidad'] ?></div>
      </div>
      <div class="order-item-price"><?= $moneda ?> <?= number_format($item['subtotal'], 2) ?></div>
    </div>
    <?php endforeach; ?>

    <div style="margin-top:.8rem;">
      <div class="total-row"><span>Subtotal</span><span><?= $moneda ?> <?= number_format($pedido['subtotal'], 2) ?></span></div>
      <?php if ($pedido['costo_envio'] > 0): ?>
      <div class="total-row"><span>Envío</span><span><?= $moneda ?> <?= number_format($pedido['costo_envio'], 2) ?></span></div>
      <?php endif; ?>
      <?php if ($pedido['descuento'] > 0): ?>
      <div class="total-row"><span>Descuento</span><span>-<?= $moneda ?> <?= number_format($pedido['descuento'], 2) ?></span></div>
      <?php endif; ?>
      <div class="total-row big"><span>Total</span><span><?= $moneda ?> <?= number_format($pedido['total'], 2) ?></span></div>
    </div>
  </div>

  <!-- ACCIONES -->
  <div class="actions" style="margin-bottom:2rem;">
    <a href="index.php?route=portal_menu" class="btn-action btn-menu"><i class="bi bi-bag-plus"></i> Hacer otro pedido</a>
    <a href="index.php?route=portal_mis_pedidos" class="btn-action btn-pedidos"><i class="bi bi-receipt"></i> Mis pedidos</a>
  </div>
</div>

</body>
</html>
