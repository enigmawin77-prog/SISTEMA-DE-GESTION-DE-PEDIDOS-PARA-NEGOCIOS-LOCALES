<?php
// views/portal/partials/navbar.php
use App\Helpers\PortalAuthHelper;
PortalAuthHelper::init();
$carritoCount = PortalAuthHelper::carritoCount();
$loggedIn = PortalAuthHelper::check();
$conf = $conf ?? [];
?>
<nav class="portal-nav">
    <div class="portal-nav-inner">
        <a href="index.php?route=portal_menu" class="portal-brand">
            <span class="portal-brand-icon">🍽️</span>
            <span><?= htmlspecialchars($conf['nombre_comercial'] ?? 'SaborLocal') ?></span>
        </a>

        <div class="portal-nav-actions">
            <a href="index.php?route=portal_menu" class="portal-nav-link">
                <i class="bi bi-grid-3x3-gap"></i> Menú
            </a>
            <?php if ($loggedIn): ?>
                <a href="index.php?route=portal_mis_pedidos" class="portal-nav-link">
                    <i class="bi bi-receipt"></i> Mis Pedidos
                </a>
                <a href="index.php?route=portal_logout" class="portal-nav-link">
                    <i class="bi bi-box-arrow-right"></i> Salir
                </a>
            <?php else: ?>
                <a href="index.php?route=portal_login" class="portal-nav-link">
                    <i class="bi bi-person"></i> Ingresar
                </a>
            <?php endif; ?>
            <button class="portal-cart-btn" id="btnAbrirCarrito" aria-label="Ver carrito">
                <i class="bi bi-bag"></i>
                <span class="cart-badge" id="cartBadge"><?= $carritoCount ?></span>
            </button>
        </div>
    </div>
</nav>
