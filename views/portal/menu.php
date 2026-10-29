<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= htmlspecialchars($conf['nombre_comercial'] ?? 'SaborLocal') ?> — Nuestro Menú</title>
<meta name="description" content="Explora nuestro menú y haz tu pedido en línea.">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
<style>
:root{
  --primary:#FF6B35;--primary-dark:#e55a26;--primary-light:#fff3ee;
  --accent:#2EC4B6;--dark:#1a1a2e;--surface:#fff;--surface2:#f8f9fa;
  --text:#2d2d2d;--text-muted:#6c757d;--radius:16px;--shadow:0 4px 24px rgba(0,0,0,.08);
}
*{margin:0;padding:0;box-sizing:border-box;}
body{font-family:'Outfit',sans-serif;background:#f5f5f7;color:var(--text);min-height:100vh;}

/* NAV */
.portal-nav{position:sticky;top:0;z-index:1000;background:rgba(255,255,255,.92);backdrop-filter:blur(14px);border-bottom:1px solid #e8e8e8;box-shadow:0 2px 12px rgba(0,0,0,.05);}
.portal-nav-inner{max-width:1300px;margin:0 auto;padding:.9rem 1.5rem;display:flex;align-items:center;justify-content:space-between;}
.portal-brand{display:flex;align-items:center;gap:.5rem;font-size:1.3rem;font-weight:700;color:var(--primary);text-decoration:none;}
.portal-brand-icon{font-size:1.5rem;}
.portal-nav-actions{display:flex;align-items:center;gap:.5rem;}
.portal-nav-link{display:flex;align-items:center;gap:.35rem;padding:.45rem .85rem;border-radius:10px;text-decoration:none;color:var(--text);font-size:.9rem;font-weight:500;transition:.2s;}
.portal-nav-link:hover{background:var(--primary-light);color:var(--primary);}
.portal-cart-btn{position:relative;background:var(--primary);color:#fff;border:none;border-radius:50px;padding:.5rem 1.1rem;font-size:1rem;cursor:pointer;display:flex;align-items:center;gap:.4rem;transition:.2s;font-family:inherit;font-weight:600;}
.portal-cart-btn:hover{background:var(--primary-dark);transform:translateY(-1px);}
.cart-badge{position:absolute;top:-6px;right:-6px;background:#FF0055;color:#fff;border-radius:50%;width:20px;height:20px;font-size:.7rem;display:flex;align-items:center;justify-content:center;font-weight:700;}

/* HERO */
.portal-hero{background:linear-gradient(135deg,var(--dark) 0%,#16213e 50%,#0f3460 100%);padding:3.5rem 1.5rem 2.5rem;text-align:center;position:relative;overflow:hidden;}
.portal-hero::before{content:'';position:absolute;inset:0;background:url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.03'%3E%3Ccircle cx='30' cy='30' r='10'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");}
.hero-title{font-size:2.4rem;font-weight:800;color:#fff;margin-bottom:.5rem;}
.hero-sub{color:rgba(255,255,255,.65);font-size:1.05rem;margin-bottom:1.5rem;}
.hero-search{max-width:460px;margin:0 auto;position:relative;}
.hero-search input{width:100%;padding:.8rem 1.2rem .8rem 3rem;border-radius:50px;border:none;font-size:.97rem;font-family:inherit;outline:none;box-shadow:0 4px 20px rgba(0,0,0,.2);}
.hero-search i{position:absolute;left:1rem;top:50%;transform:translateY(-50%);color:var(--text-muted);font-size:1.1rem;}

/* FILTROS CATEGORIAS */
.filter-bar{max-width:1300px;margin:1.5rem auto 0;padding:0 1.5rem;display:flex;gap:.6rem;flex-wrap:wrap;}
.filter-btn{border:2px solid #e0e0e0;background:#fff;padding:.4rem 1.1rem;border-radius:50px;font-family:inherit;font-size:.88rem;font-weight:600;cursor:pointer;transition:.2s;color:var(--text);}
.filter-btn:hover,.filter-btn.active{border-color:var(--primary);background:var(--primary);color:#fff;}

/* GRID PRODUCTOS */
.products-section{max-width:1300px;margin:0 auto;padding:1.5rem;}
.section-title{font-size:1.15rem;font-weight:700;color:var(--dark);margin:1.5rem 0 1rem;display:flex;align-items:center;gap:.5rem;}
.section-title::after{content:'';flex:1;height:1px;background:#e0e0e0;margin-left:.5rem;}
.products-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(230px,1fr));gap:1.2rem;}
.product-card{background:#fff;border-radius:var(--radius);overflow:hidden;box-shadow:var(--shadow);transition:.25s;cursor:pointer;}
.product-card:hover{transform:translateY(-4px);box-shadow:0 12px 32px rgba(0,0,0,.13);}
.product-img{width:100%;height:170px;object-fit:cover;background:linear-gradient(135deg,#f0f0f0,#e0e0e0);}
.product-img-placeholder{width:100%;height:170px;background:linear-gradient(135deg,#ff6b3520,#2ec4b620);display:flex;align-items:center;justify-content:center;font-size:3rem;}
.product-body{padding:1rem;}
.product-name{font-weight:700;font-size:1rem;margin-bottom:.25rem;color:var(--dark);}
.product-desc{font-size:.82rem;color:var(--text-muted);line-height:1.4;margin-bottom:.75rem;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;}
.product-footer{display:flex;align-items:center;justify-content:space-between;}
.product-price{font-size:1.15rem;font-weight:800;color:var(--primary);}
.btn-add{background:var(--primary);color:#fff;border:none;border-radius:10px;padding:.45rem .9rem;font-family:inherit;font-size:.85rem;font-weight:600;cursor:pointer;display:flex;align-items:center;gap:.3rem;transition:.2s;}
.btn-add:hover{background:var(--primary-dark);}
.product-stock-low{font-size:.75rem;color:#e67e22;font-weight:600;}

/* CARRITO DRAWER */
.cart-overlay{position:fixed;inset:0;background:rgba(0,0,0,.4);z-index:1100;opacity:0;pointer-events:none;transition:.3s;}
.cart-overlay.open{opacity:1;pointer-events:all;}
.cart-drawer{position:fixed;right:0;top:0;height:100vh;width:380px;max-width:95vw;background:#fff;z-index:1101;transform:translateX(100%);transition:.3s cubic-bezier(.4,0,.2,1);display:flex;flex-direction:column;box-shadow:-8px 0 32px rgba(0,0,0,.15);}
.cart-drawer.open{transform:translateX(0);}
.cart-header{padding:1.2rem 1.5rem;border-bottom:1px solid #eee;display:flex;align-items:center;justify-content:space-between;}
.cart-header h5{font-weight:700;font-size:1.1rem;}
.cart-close{background:none;border:none;font-size:1.4rem;cursor:pointer;color:var(--text-muted);transition:.2s;}
.cart-close:hover{color:var(--primary);}
.cart-items{flex:1;overflow-y:auto;padding:1rem 1.5rem;}
.cart-empty{text-align:center;padding:3rem 1rem;color:var(--text-muted);}
.cart-empty i{font-size:3rem;display:block;margin-bottom:.7rem;color:#ddd;}
.cart-item{display:flex;gap:.8rem;padding:.8rem 0;border-bottom:1px solid #f0f0f0;align-items:flex-start;}
.cart-item-img{width:56px;height:56px;object-fit:cover;border-radius:10px;background:#f0f0f0;flex-shrink:0;}
.cart-item-info{flex:1;}
.cart-item-name{font-weight:600;font-size:.9rem;margin-bottom:.3rem;}
.cart-item-price{color:var(--primary);font-weight:700;font-size:.9rem;}
.cart-qty{display:flex;align-items:center;gap:.5rem;margin-top:.4rem;}
.cart-qty button{width:26px;height:26px;border-radius:50%;border:1.5px solid #e0e0e0;background:#fff;cursor:pointer;font-size:.9rem;display:flex;align-items:center;justify-content:center;transition:.2s;}
.cart-qty button:hover{border-color:var(--primary);color:var(--primary);}
.cart-qty span{min-width:28px;text-align:center;font-weight:700;font-size:.9rem;}
.cart-item-del{background:none;border:none;cursor:pointer;color:#ccc;font-size:1rem;padding:.2rem;transition:.2s;}
.cart-item-del:hover{color:#e74c3c;}
.cart-footer{padding:1.2rem 1.5rem;border-top:1px solid #eee;}
.cart-total-row{display:flex;justify-content:space-between;margin-bottom:.5rem;font-size:.95rem;}
.cart-total-row.big{font-weight:800;font-size:1.1rem;color:var(--dark);}
.btn-checkout{display:block;width:100%;background:var(--primary);color:#fff;border:none;padding:.85rem;border-radius:12px;font-family:inherit;font-size:1rem;font-weight:700;cursor:pointer;text-align:center;text-decoration:none;transition:.2s;margin-top:.8rem;}
.btn-checkout:hover{background:var(--primary-dark);}

/* TOAST */
.toast-portal{position:fixed;bottom:1.5rem;left:50%;transform:translateX(-50%);background:var(--dark);color:#fff;padding:.7rem 1.5rem;border-radius:50px;font-size:.9rem;font-weight:600;z-index:9999;opacity:0;pointer-events:none;transition:.3s;display:flex;align-items:center;gap:.5rem;}
.toast-portal.show{opacity:1;}
.toast-portal.success{background:#2EC4B6;}
.toast-portal.error{background:#e74c3c;}

/* FLASH */
.portal-flash{max-width:1300px;margin:1rem auto 0;padding:0 1.5rem;}
.alert-portal{padding:.9rem 1.2rem;border-radius:12px;font-weight:600;font-size:.93rem;margin-bottom:.5rem;}
.alert-portal.success{background:#eafaf7;color:#1a7a5e;border-left:4px solid #2EC4B6;}
.alert-portal.error{background:#fff0f0;color:#c0392b;border-left:4px solid #e74c3c;}

@media(max-width:600px){
  .hero-title{font-size:1.7rem;}
  .products-grid{grid-template-columns:repeat(auto-fill,minmax(160px,1fr));}
  .cart-drawer{width:100vw;}
}
.no-results{text-align:center;padding:3rem;color:var(--text-muted);}
.no-results i{font-size:2.5rem;display:block;margin-bottom:.7rem;color:#ddd;}
</style>
</head>
<body>

<?php require __DIR__ . '/partials/navbar.php'; ?>

<!-- FLASH -->
<?php use App\Helpers\PortalAuthHelper; ?>
<?php if (!empty($_SESSION['portal_success'])): ?>
<div class="portal-flash">
  <div class="alert-portal success"><i class="bi bi-check-circle-fill"></i> <?= $_SESSION['portal_success']; unset($_SESSION['portal_success']); ?></div>
</div>
<?php endif; ?>
<?php if (!empty($_SESSION['portal_error'])): ?>
<div class="portal-flash">
  <div class="alert-portal error"><i class="bi bi-exclamation-triangle-fill"></i> <?= $_SESSION['portal_error']; unset($_SESSION['portal_error']); ?></div>
</div>
<?php endif; ?>

<!-- HERO -->
<div class="portal-hero">
  <h1 class="hero-title">🍽️ Nuestro Menú</h1>
  <p class="hero-sub">Pide desde donde estés — rápido, fácil y delicioso</p>
  <div class="hero-search">
    <i class="bi bi-search"></i>
    <input type="text" id="searchInput" placeholder="Buscar platos, bebidas..." autocomplete="off">
  </div>
</div>

<!-- CATEGORÍAS FILTRO -->
<div class="filter-bar">
  <button class="filter-btn active" data-cat="all">🌐 Todos</button>
  <?php foreach ($categorias as $cat): ?>
  <button class="filter-btn" data-cat="<?= $cat['id'] ?>">
    <i class="bi <?= htmlspecialchars($cat['icono'] ?? 'bi-tag') ?>"></i>
    <?= htmlspecialchars($cat['nombre']) ?>
  </button>
  <?php endforeach; ?>
</div>

<!-- PRODUCTOS -->
<div class="products-section" id="productosSection">
<?php
// Agrupar por categoría
$grupos = [];
foreach ($productos as $p) {
    $grupos[$p['categoria_id']]['nombre'] = $p['categoria_nombre'];
    $grupos[$p['categoria_id']]['items'][] = $p;
}
$moneda = $conf['moneda_simbolo'] ?? 'S/';
?>
<?php if (empty($productos)): ?>
  <div class="no-results"><i class="bi bi-bag-x"></i> No hay productos disponibles por el momento.</div>
<?php else: ?>
<?php foreach ($grupos as $catId => $grupo): ?>
  <div class="categoria-grupo" data-group-cat="<?= $catId ?>">
    <div class="section-title"><?= htmlspecialchars($grupo['nombre']) ?></div>
    <div class="products-grid">
    <?php foreach ($grupo['items'] as $prod): ?>
      <div class="product-card" data-cat="<?= $prod['categoria_id'] ?>" data-name="<?= strtolower(htmlspecialchars($prod['nombre'])) ?>">
        <?php if ($prod['imagen'] && file_exists(__DIR__ . '/../../public/uploads/productos/' . $prod['imagen'])): ?>
          <img src="public/uploads/productos/<?= htmlspecialchars($prod['imagen']) ?>" alt="<?= htmlspecialchars($prod['nombre']) ?>" class="product-img">
        <?php else: ?>
          <div class="product-img-placeholder">🍽️</div>
        <?php endif; ?>
        <div class="product-body">
          <div class="product-name"><?= htmlspecialchars($prod['nombre']) ?></div>
          <?php if ($prod['descripcion']): ?>
          <div class="product-desc"><?= htmlspecialchars($prod['descripcion']) ?></div>
          <?php endif; ?>
          <?php if ($prod['stock'] <= 5): ?>
          <div class="product-stock-low">⚡ ¡Solo <?= $prod['stock'] ?> disponibles!</div>
          <?php endif; ?>
          <div class="product-footer">
            <div class="product-price"><?= $moneda ?> <?= number_format($prod['precio_venta'], 2) ?></div>
            <button class="btn-add"
              data-id="<?= $prod['id'] ?>"
              data-nombre="<?= htmlspecialchars($prod['nombre']) ?>"
              onclick="agregarAlCarrito(<?= $prod['id'] ?>, '<?= addslashes($prod['nombre']) ?>')">
              <i class="bi bi-plus-lg"></i> Agregar
            </button>
          </div>
        </div>
      </div>
    <?php endforeach; ?>
    </div>
  </div>
<?php endforeach; ?>
<?php endif; ?>
</div>

<!-- CARRITO OVERLAY + DRAWER -->
<div class="cart-overlay" id="cartOverlay"></div>
<div class="cart-drawer" id="cartDrawer">
  <div class="cart-header">
    <h5><i class="bi bi-bag"></i> Tu Pedido</h5>
    <button class="cart-close" id="btnCerrarCarrito">✕</button>
  </div>
  <div class="cart-items" id="cartItems">
    <div class="cart-empty"><i class="bi bi-bag"></i>Tu carrito está vacío</div>
  </div>
  <div class="cart-footer" id="cartFooter" style="display:none">
    <div class="cart-total-row"><span>Subtotal</span><span id="cartSubtotal">S/ 0.00</span></div>
    <div class="cart-total-row big"><span>Total</span><span id="cartTotal">S/ 0.00</span></div>
    <a href="index.php?route=portal_checkout" class="btn-checkout" id="btnIrCheckout">
      <i class="bi bi-credit-card"></i> Confirmar pedido
    </a>
  </div>
</div>

<!-- TOAST -->
<div class="toast-portal" id="toastPortal"></div>

<script>
const BASE = 'index.php';
const moneda = '<?= $moneda ?>';
const isLoggedIn = <?= PortalAuthHelper::check() ? 'true' : 'false' ?>;

/* ---- Carrito ---- */
function abrirCarrito(){ document.getElementById('cartOverlay').classList.add('open'); document.getElementById('cartDrawer').classList.add('open'); refreshCarrito(); }
function cerrarCarrito(){ document.getElementById('cartOverlay').classList.remove('open'); document.getElementById('cartDrawer').classList.remove('open'); }
document.getElementById('btnAbrirCarrito').addEventListener('click', abrirCarrito);
document.getElementById('btnCerrarCarrito').addEventListener('click', cerrarCarrito);
document.getElementById('cartOverlay').addEventListener('click', cerrarCarrito);

function mostrarToast(msg, tipo='success'){
  const t = document.getElementById('toastPortal');
  t.innerHTML = (tipo==='success'?'✅ ':'❌ ') + msg;
  t.className = 'toast-portal show ' + tipo;
  setTimeout(()=>t.className='toast-portal', 2500);
}

async function agregarAlCarrito(id, nombre){
  const res = await fetch(BASE+'?route=portal_carrito_agregar', {
    method:'POST', headers:{'Content-Type':'application/json'},
    body: JSON.stringify({producto_id: id, cantidad: 1})
  });
  const data = await res.json();
  if(data.ok){
    document.getElementById('cartBadge').textContent = data.count;
    mostrarToast(nombre + ' agregado al carrito');
    refreshCarrito();
  } else {
    mostrarToast(data.msg || 'Error al agregar', 'error');
  }
}

async function refreshCarrito(){
  const res = await fetch(BASE+'?route=portal_carrito_json');
  const data = await res.json();
  document.getElementById('cartBadge').textContent = data.count;

  const container = document.getElementById('cartItems');
  const footer = document.getElementById('cartFooter');

  if(data.items.length === 0){
    container.innerHTML = '<div class="cart-empty"><i class="bi bi-bag"></i>Tu carrito está vacío</div>';
    footer.style.display = 'none';
    return;
  }
  footer.style.display = 'block';
  document.getElementById('cartSubtotal').textContent = moneda + ' ' + data.total;
  document.getElementById('cartTotal').textContent = moneda + ' ' + data.total;

  container.innerHTML = data.items.map(item => `
    <div class="cart-item" id="ci-${item.producto_id}">
      <div class="cart-item-img" style="display:flex;align-items:center;justify-content:center;font-size:1.8rem;background:#f8f8f8;">🍽️</div>
      <div class="cart-item-info">
        <div class="cart-item-name">${item.nombre}</div>
        <div class="cart-item-price">${moneda} ${parseFloat(item.precio).toFixed(2)}</div>
        <div class="cart-qty">
          <button onclick="cambiarCantidad(${item.producto_id}, ${item.cantidad - 1})">−</button>
          <span>${item.cantidad}</span>
          <button onclick="cambiarCantidad(${item.producto_id}, ${item.cantidad + 1})">+</button>
        </div>
      </div>
      <button class="cart-item-del" onclick="eliminarItem(${item.producto_id})" title="Eliminar"><i class="bi bi-trash"></i></button>
    </div>
  `).join('');
}

async function cambiarCantidad(id, cant){
  await fetch(BASE+'?route=portal_carrito_actualizar', {
    method:'POST', headers:{'Content-Type':'application/json'},
    body: JSON.stringify({producto_id: id, cantidad: cant})
  });
  refreshCarrito();
}

async function eliminarItem(id){
  await fetch(BASE+'?route=portal_carrito_eliminar', {
    method:'POST', headers:{'Content-Type':'application/json'},
    body: JSON.stringify({producto_id: id})
  });
  refreshCarrito();
}

/* Checkout redirect si no está logueado */
document.getElementById('btnIrCheckout').addEventListener('click', function(e){
  if(!isLoggedIn){
    e.preventDefault();
    cerrarCarrito();
    setTimeout(()=>{ window.location.href = BASE+'?route=portal_login&next=portal_checkout'; }, 200);
  }
});

/* ---- Filtros ---- */
document.querySelectorAll('.filter-btn').forEach(btn => {
  btn.addEventListener('click', () => {
    document.querySelectorAll('.filter-btn').forEach(b=>b.classList.remove('active'));
    btn.classList.add('active');
    filtrar(btn.dataset.cat);
  });
});

function filtrar(catId){
  document.querySelectorAll('.categoria-grupo').forEach(g => {
    g.style.display = (catId==='all' || g.dataset.groupCat === catId) ? '' : 'none';
  });
  buscarTexto(document.getElementById('searchInput').value, catId);
}

document.getElementById('searchInput').addEventListener('input', function(){
  const catActiva = document.querySelector('.filter-btn.active')?.dataset.cat || 'all';
  buscarTexto(this.value.toLowerCase(), catActiva);
});

function buscarTexto(q, catId){
  let visibles = 0;
  document.querySelectorAll('.product-card').forEach(card => {
    const matchCat = catId==='all' || card.dataset.cat === catId;
    const matchQ = !q || card.dataset.name.includes(q);
    card.style.display = (matchCat && matchQ) ? '' : 'none';
    if(matchCat && matchQ) visibles++;
  });
  // Ocultar grupos vacíos
  document.querySelectorAll('.categoria-grupo').forEach(g => {
    const visiblesGrupo = [...g.querySelectorAll('.product-card')].filter(c=>c.style.display!=='none').length;
    g.style.display = visiblesGrupo ? '' : 'none';
  });
}

// Cargar carrito al inicio
refreshCarrito();
</script>
</body>
</html>
