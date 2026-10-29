<?php
use App\Helpers\Database;
use App\Helpers\FormatHelper;
use App\Models\Configuracion;

$conf = Configuracion::get();
$db = Database::getConnection();

// Buscar notificaciones relevantes: stock bajo y pedidos pendientes
$stockAlerts = $db->query("SELECT nombre, stock, stock_minimo FROM productos WHERE stock <= stock_minimo AND activo = 1 LIMIT 5")->fetchAll();
$pendingOrders = $db->query("SELECT numero_pedido, total, created_at FROM pedidos WHERE estado = 'pendiente' ORDER BY id DESC LIMIT 5")->fetchAll();
$totalNotifications = count($stockAlerts) + count($pendingOrders);
?>
<header class="app-topbar">
    <div class="topbar-left">
        <!-- Botón responsive para móviles -->
        <button class="sidebar-toggle-btn" id="sidebarToggle" type="button" aria-label="Abrir Menú">
            <i class="bi bi-list"></i>
        </button>

        <!-- Buscador global -->
        <form class="global-search-box" action="index.php" method="GET">
            <input type="hidden" name="route" value="pedidos">
            <i class="bi bi-search search-icon"></i>
            <input type="text"
                   name="search"
                   id="globalSearchInput"
                   class="global-search-input"
                   placeholder="Buscar pedidos, productos, clientes..."
                   autocomplete="off">
            <span class="search-shortcut-pill">⌘ K</span>
        </form>
    </div>

    <div class="topbar-right">
        <!-- Fecha y Hora Actual -->
        <div class="topbar-datetime d-none d-md-flex">
            <i class="bi bi-calendar3 text-primary"></i>
            <span id="liveClock"><?= date('D, d M Y | h:i A') ?></span>
        </div>

        <!-- Notificaciones -->
        <div class="dropdown">
            <button class="notification-btn" data-bs-toggle="dropdown" aria-expanded="false" title="Notificaciones">
                <i class="bi bi-bell"></i>
                <?php if ($totalNotifications > 0): ?>
                    <span class="notification-badge"><?= min($totalNotifications, 9) ?></span>
                <?php endif; ?>
            </button>
            <div class="dropdown-menu dropdown-menu-end shadow-lg border-0 p-0" style="width: 320px;">
                <div class="p-3 border-bottom d-flex align-items-center justify-content-between bg-light">
                    <h6 class="mb-0 fw-bold"><i class="bi bi-bell-fill text-primary me-2"></i>Notificaciones</h6>
                    <span class="badge bg-primary rounded-pill"><?= $totalNotifications ?> nuevas</span>
                </div>
                <div class="list-group list-group-flush" style="max-height: 280px; overflow-y: auto;">
                    <?php if (empty($stockAlerts) && empty($pendingOrders)): ?>
                        <div class="p-3 text-center text-muted small">No hay notificaciones pendientes.</div>
                    <?php else: ?>
                        <?php foreach ($pendingOrders as $po): ?>
                            <a href="index.php?route=pedidos" class="list-group-item list-group-item-action p-2.5">
                                <div class="d-flex align-items-center gap-2">
                                    <div class="bg-warning-subtle text-warning p-1.5 rounded-circle">
                                        <i class="bi bi-clock-fill"></i>
                                    </div>
                                    <div class="flex-grow-1 overflow-hidden">
                                        <div class="small fw-bold text-dark">Nuevo pedido pendiente: <?= htmlspecialchars($po['numero_pedido']) ?></div>
                                        <div class="text-muted" style="font-size: 11.5px;">Monto: <?= FormatHelper::money($po['total']) ?></div>
                                    </div>
                                </div>
                            </a>
                        <?php endforeach; ?>

                        <?php foreach ($stockAlerts as $sa): ?>
                            <a href="index.php?route=productos&stock_bajo=1" class="list-group-item list-group-item-action p-2.5">
                                <div class="d-flex align-items-center gap-2">
                                    <div class="bg-danger-subtle text-danger p-1.5 rounded-circle">
                                        <i class="bi bi-exclamation-triangle-fill"></i>
                                    </div>
                                    <div class="flex-grow-1 overflow-hidden">
                                        <div class="small fw-bold text-danger">Stock bajo: <?= htmlspecialchars($sa['nombre']) ?></div>
                                        <div class="text-muted" style="font-size: 11.5px;">Disponible: <?= $sa['stock'] ?> uds (Mín: <?= $sa['stock_minimo'] ?>)</div>
                                    </div>
                                </div>
                            </a>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
                <div class="p-2 text-center border-top bg-light">
                    <a href="index.php?route=pedidos" class="text-decoration-none small fw-bold text-primary">Ver todas las alertas</a>
                </div>
            </div>
        </div>

        <!-- Tienda Activa -->
        <a href="index.php?route=configuracion" class="store-selector-pill d-none d-sm-flex text-decoration-none">
            <div class="store-logo-thumb">
                <i class="bi bi-shop-window"></i>
            </div>
            <div>
                <div class="fw-bold small lh-1 text-dark"><?= htmlspecialchars($conf['nombre_comercial']) ?></div>
                <div class="text-muted" style="font-size: 10.5px;">Restaurante</div>
            </div>
            <i class="bi bi-chevron-down text-muted ms-1" style="font-size: 11px;"></i>
        </a>
    </div>
</header>
