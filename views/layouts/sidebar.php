<?php
use App\Helpers\Database;
use App\Helpers\AuthHelper;
use App\Models\Configuracion;

$conf = Configuracion::get();
$user = AuthHelper::user();
$route = $_GET['route'] ?? 'dashboard';

// Conteo en vivo de pedidos pendientes para la notificación del menú
$db = Database::getConnection();
$pendingCount = (int)$db->query("SELECT COUNT(*) FROM pedidos WHERE estado = 'pendiente'")->fetchColumn();
?>
<aside class="app-sidebar" id="appSidebar">
    <!-- Header de Marca -->
    <div class="sidebar-header">
        <div class="brand-icon">
            <i class="bi bi-egg-fried"></i>
        </div>
        <div>
            <h1 class="brand-title">SaborLocal</h1>
            <p class="brand-subtitle">Gestión de Pedidos</p>
        </div>
    </div>

    <!-- Menú de Navegación -->
    <nav class="sidebar-menu">
        <a href="index.php?route=dashboard" class="nav-item-link <?= in_array($route, ['', 'dashboard']) ? 'active' : '' ?>">
            <span class="nav-label"><i class="bi bi-grid-fill"></i> Dashboard</span>
        </a>

        <a href="index.php?route=pedidos" class="nav-item-link <?= str_starts_with($route, 'pedidos') ? 'active' : '' ?>">
            <span class="nav-label"><i class="bi bi-receipt"></i> Pedidos</span>
            <?php if ($pendingCount > 0): ?>
                <span class="nav-badge-pill"><?= $pendingCount ?></span>
            <?php endif; ?>
        </a>

        <a href="index.php?route=productos" class="nav-item-link <?= str_starts_with($route, 'productos') ? 'active' : '' ?>">
            <span class="nav-label"><i class="bi bi-box-seam-fill"></i> Productos</span>
        </a>

        <a href="index.php?route=categorias" class="nav-item-link <?= str_starts_with($route, 'categorias') ? 'active' : '' ?>">
            <span class="nav-label"><i class="bi bi-tags-fill"></i> Categorías</span>
        </a>

        <a href="index.php?route=clientes" class="nav-item-link <?= str_starts_with($route, 'clientes') ? 'active' : '' ?>">
            <span class="nav-label"><i class="bi bi-people-fill"></i> Clientes</span>
        </a>

        <a href="index.php?route=pagos" class="nav-item-link <?= str_starts_with($route, 'pagos') ? 'active' : '' ?>">
            <span class="nav-label"><i class="bi bi-credit-card-2-front-fill"></i> Pagos</span>
        </a>

        <a href="index.php?route=entregas" class="nav-item-link <?= str_starts_with($route, 'entregas') ? 'active' : '' ?>">
            <span class="nav-label"><i class="bi bi-bicycle"></i> Entregas</span>
        </a>

        <a href="index.php?route=reportes" class="nav-item-link <?= str_starts_with($route, 'reportes') ? 'active' : '' ?>">
            <span class="nav-label"><i class="bi bi-bar-chart-line-fill"></i> Reportes</span>
        </a>

        <?php if (AuthHelper::isAdmin()): ?>
        <a href="index.php?route=configuracion" class="nav-item-link <?= str_starts_with($route, 'configuracion') ? 'active' : '' ?>">
            <span class="nav-label"><i class="bi bi-gear-fill"></i> Configuración</span>
        </a>
        <?php endif; ?>
    </nav>

    <!-- Footer del Sidebar -->
    <div class="sidebar-footer">
        <div class="sidebar-business-card">
            <div class="business-icon-box">
                <i class="bi bi-shop"></i>
            </div>
            <div class="business-details">
                <p class="business-label">Tu negocio</p>
                <p class="business-name"><?= htmlspecialchars($conf['nombre_comercial']) ?></p>
                <span class="business-status"><i class="bi bi-dot"></i> Activo</span>
            </div>
        </div>

        <div class="dropdown dropup">
            <a href="#" class="sidebar-user-card dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                <div class="user-avatar-img">
                    <?= strtoupper(substr($user['nombre'] ?? 'U', 0, 1)) ?>
                </div>
                <div class="flex-grow-1 overflow-hidden">
                    <p class="user-meta-name text-truncate"><?= htmlspecialchars($user['nombre'] ?? 'Usuario') ?></p>
                    <p class="user-meta-role text-capitalize"><?= htmlspecialchars($user['rol'] ?? 'Empleado') ?></p>
                </div>
            </a>
            <ul class="dropdown-menu dropdown-menu-dark shadow-lg border-0 mb-2">
                <li><a class="dropdown-item py-2" href="index.php?route=perfil"><i class="bi bi-person me-2"></i> Mi Perfil</a></li>
                <?php if (AuthHelper::isAdmin()): ?>
                <li><a class="dropdown-item py-2" href="index.php?route=configuracion"><i class="bi bi-sliders me-2"></i> Ajustes de Tienda</a></li>
                <?php endif; ?>
                <li><hr class="dropdown-divider"></li>
                <li><a class="dropdown-item py-2 text-danger" href="index.php?route=logout"><i class="bi bi-box-arrow-right me-2"></i> Cerrar Sesión</a></li>
            </ul>
        </div>
    </div>
</aside>
