<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Mis Pedidos — Portal Cliente</title>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
<link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
<style>
:root{--primary:#FF6B35;--dark:#1a1a2e;}
*{margin:0;padding:0;box-sizing:border-box;}
body{font-family:'Outfit',sans-serif;background:#f5f5f7;color:#2d2d2d;}
.portal-nav{position:sticky;top:0;z-index:100;background:rgba(255,255,255,.92);backdrop-filter:blur(14px);border-bottom:1px solid #e8e8e8;box-shadow:0 2px 12px rgba(0,0,0,.05);}
.portal-nav-inner{max-width:1100px;margin:0 auto;padding:.9rem 1.5rem;display:flex;align-items:center;justify-content:space-between;}
.portal-brand{display:flex;align-items:center;gap:.5rem;font-size:1.2rem;font-weight:700;color:var(--primary);text-decoration:none;}
.nav-links{display:flex;gap:.5rem;}
.nav-link-item{display:flex;align-items:center;gap:.3rem;padding:.4rem .9rem;border-radius:10px;text-decoration:none;color:#2d2d2d;font-size:.88rem;font-weight:500;transition:.2s;}
.nav-link-item:hover{background:#f0f0f0;color:var(--primary);}

.page-header{background:linear-gradient(135deg,var(--dark),#16213e);padding:2rem 1.5rem;text-align:center;color:#fff;}
.page-header h1{font-size:1.8rem;font-weight:800;margin-bottom:.3rem;}
.page-header p{color:rgba(255,255,255,.6);font-size:.95rem;}

.wrap{max-width:900px;margin:2rem auto;padding:0 1rem;}

/* ESTADO BADGES */
.badge-estado{display:inline-flex;align-items:center;gap:.3rem;padding:.3rem .75rem;border-radius:50px;font-size:.78rem;font-weight:700;}
.badge-pendiente{background:#fff3cd;color:#856404;}
.badge-confirmado{background:#cce5ff;color:#004085;}
.badge-en_preparacion{background:#e2d9f3;color:#6f42c1;}
.badge-listo{background:#d1f0ea;color:#155724;}
.badge-en_camino{background:#fff3cd;color:#FF6B35;}
.badge-entregado{background:#d4edda;color:#155724;}
.badge-cancelado{background:#f8d7da;color:#721c24;}

.pedidos-list{display:flex;flex-direction:column;gap:1rem;}
.pedido-card{background:#fff;border-radius:16px;padding:1.3rem 1.5rem;box-shadow:0 4px 16px rgba(0,0,0,.06);display:flex;align-items:center;gap:1rem;transition:.2s;}
.pedido-card:hover{box-shadow:0 8px 28px rgba(0,0,0,.11);transform:translateY(-2px);}
.pedido-icon{width:52px;height:52px;border-radius:14px;background:#fff3ee;display:flex;align-items:center;justify-content:center;font-size:1.5rem;flex-shrink:0;}
.pedido-info{flex:1;}
.pedido-num{font-weight:800;font-size:1rem;color:var(--dark);}
.pedido-meta{font-size:.82rem;color:#6c757d;margin-top:.2rem;}
.pedido-right{text-align:right;}
.pedido-total{font-size:1.1rem;font-weight:800;color:var(--primary);}
.btn-detalle{display:inline-flex;align-items:center;gap:.3rem;padding:.4rem .9rem;border-radius:10px;background:#f5f5f7;color:var(--dark);text-decoration:none;font-size:.82rem;font-weight:600;margin-top:.4rem;transition:.2s;}
.btn-detalle:hover{background:var(--primary);color:#fff;}

.empty-state{text-align:center;padding:4rem 2rem;color:#aaa;}
.empty-state i{font-size:3.5rem;display:block;margin-bottom:1rem;color:#ddd;}
.empty-state p{font-size:1rem;}
.btn-ir-menu{display:inline-flex;align-items:center;gap:.4rem;margin-top:1rem;background:var(--primary);color:#fff;padding:.7rem 1.5rem;border-radius:12px;text-decoration:none;font-weight:700;}

.modalidad-badge{display:inline-flex;align-items:center;gap:.3rem;font-size:.78rem;font-weight:600;color:#6c757d;}
@media(max-width:600px){.pedido-card{flex-wrap:wrap;}.pedido-right{width:100%;text-align:left;}}
</style>
</head>
<body>

<nav class="portal-nav">
  <div class="portal-nav-inner">
    <a href="index.php?route=portal_menu" class="portal-brand">🍽️ <?= htmlspecialchars($conf['nombre_comercial'] ?? 'SaborLocal') ?></a>
    <div class="nav-links">
      <a href="index.php?route=portal_menu" class="nav-link-item"><i class="bi bi-grid-3x3-gap"></i> Menú</a>
      <a href="index.php?route=portal_logout" class="nav-link-item"><i class="bi bi-box-arrow-right"></i> Salir</a>
    </div>
  </div>
</nav>

<div class="page-header">
  <h1>📋 Mis Pedidos</h1>
  <p>Hola, <?= htmlspecialchars(\App\Helpers\PortalAuthHelper::nombre()) ?> — aquí tienes tu historial</p>
</div>

<div class="wrap">
  <?php if (empty($pedidos)): ?>
  <div class="empty-state">
    <i class="bi bi-receipt"></i>
    <p>Aún no has realizado ningún pedido.</p>
    <a href="index.php?route=portal_menu" class="btn-ir-menu"><i class="bi bi-bag-plus"></i> Ver el menú</a>
  </div>
  <?php else: ?>
  <div class="pedidos-list">
    <?php
    $moneda = $conf['moneda_simbolo'] ?? 'S/';
    $estadoLabels = [
      'pendiente'      => ['label'=>'Pendiente',      'class'=>'badge-pendiente',      'icon'=>'⏳'],
      'confirmado'     => ['label'=>'Confirmado',     'class'=>'badge-confirmado',     'icon'=>'✅'],
      'en_preparacion' => ['label'=>'En preparación', 'class'=>'badge-en_preparacion', 'icon'=>'👨‍🍳'],
      'listo'          => ['label'=>'Listo',           'class'=>'badge-listo',          'icon'=>'🎉'],
      'en_camino'      => ['label'=>'En camino',       'class'=>'badge-en_camino',      'icon'=>'🛵'],
      'entregado'      => ['label'=>'Entregado',       'class'=>'badge-entregado',      'icon'=>'📦'],
      'cancelado'      => ['label'=>'Cancelado',       'class'=>'badge-cancelado',      'icon'=>'❌'],
    ];
    foreach ($pedidos as $p):
      $est = $estadoLabels[$p['estado']] ?? ['label'=>$p['estado'],'class'=>'badge-pendiente','icon'=>'❓'];
    ?>
    <div class="pedido-card">
      <div class="pedido-icon"><?= $est['icon'] ?></div>
      <div class="pedido-info">
        <div class="pedido-num">#<?= htmlspecialchars($p['numero_pedido']) ?></div>
        <div class="pedido-meta">
          <?= date('d/m/Y H:i', strtotime($p['fecha_pedido'])) ?> ·
          <span class="modalidad-badge">
            <?= $p['modalidad'] === 'domicilio' ? '🏠 Delivery' : '🏪 Recojo' ?>
          </span>
        </div>
        <div style="margin-top:.4rem;">
          <span class="badge-estado <?= $est['class'] ?>"><?= $est['icon'] ?> <?= $est['label'] ?></span>
        </div>
      </div>
      <div class="pedido-right">
        <div class="pedido-total"><?= $moneda ?> <?= number_format($p['total'], 2) ?></div>
        <a href="index.php?route=portal_pedido_detalle&id=<?= $p['id'] ?>" class="btn-detalle">
          <i class="bi bi-eye"></i> Ver detalle
        </a>
      </div>
    </div>
    <?php endforeach; ?>
  </div>
  <?php endif; ?>
</div>

</body>
</html>
