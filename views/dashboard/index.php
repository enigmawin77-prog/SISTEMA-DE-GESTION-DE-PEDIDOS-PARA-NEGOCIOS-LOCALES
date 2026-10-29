<?php
$pageTitle = "Dashboard";
require __DIR__ . '/../layouts/header.php';
require __DIR__ . '/../layouts/sidebar.php';

use App\Helpers\FormatHelper;
use App\Helpers\FlashHelper;
?>

<main class="app-main">
    <?php require __DIR__ . '/../layouts/navbar.php'; ?>

    <div class="app-content">
        <!-- Notificaciones Flash -->
        <?= FlashHelper::render() ?>

        <!-- Bienvenida y Encabezado Principal -->
        <div class="welcome-header">
            <div>
                <h2 class="welcome-title">¡Bienvenido, <?= htmlspecialchars(explode(' ', $user['nombre'] ?? 'Administrador')[0]) ?>!</h2>
                <p class="welcome-subtitle">Aquí tienes un resumen de la actividad de tu negocio hoy.</p>
            </div>
            <div class="d-none d-lg-flex">
                <div class="welcome-quote-badge">
                    <span>Grandes pedidos, grandes historias</span>
                    <span class="fs-5">👨‍🍳</span>
                </div>
            </div>
        </div>

        <!-- ====================================================================
             FILA 1 DE INDICADORES (ESTADOS DE PEDIDOS)
        ==================================================================== -->
        <div class="row g-3 mb-3">
            <!-- Total de pedidos -->
            <div class="col-6 col-md-4 col-xl">
                <div class="stat-card-clean">
                    <div class="stat-card-top">
                        <div class="stat-icon-circle stat-icon-blue">
                            <i class="bi bi-cart3"></i>
                        </div>
                        <div>
                            <p class="stat-card-title">Total de pedidos</p>
                            <h3 class="stat-card-value"><?= $metrics['total_pedidos'] ?></h3>
                        </div>
                    </div>
                    <div class="stat-card-trend trend-up">
                        <i class="bi bi-arrow-up-short"></i> 12% <span class="text-muted fw-normal">vs. ayer</span>
                    </div>
                </div>
            </div>

            <!-- Pendientes -->
            <div class="col-6 col-md-4 col-xl">
                <div class="stat-card-clean">
                    <div class="stat-card-top">
                        <div class="stat-icon-circle stat-icon-orange">
                            <i class="bi bi-clock-history"></i>
                        </div>
                        <div>
                            <p class="stat-card-title">Pendientes</p>
                            <h3 class="stat-card-value"><?= $metrics['pendientes'] ?></h3>
                        </div>
                    </div>
                    <div class="stat-card-trend trend-up text-warning">
                        <i class="bi bi-arrow-up-short"></i> 50% <span class="text-muted fw-normal">vs. ayer</span>
                    </div>
                </div>
            </div>

            <!-- En preparación -->
            <div class="col-6 col-md-4 col-xl">
                <div class="stat-card-clean">
                    <div class="stat-card-top">
                        <div class="stat-icon-circle stat-icon-purple">
                            <i class="bi bi-egg-fried"></i>
                        </div>
                        <div>
                            <p class="stat-card-title">En preparación</p>
                            <h3 class="stat-card-value"><?= $metrics['en_preparacion'] ?></h3>
                        </div>
                    </div>
                    <div class="stat-card-trend trend-up text-purple" style="color: #8b5cf6;">
                        <i class="bi bi-arrow-up-short"></i> 25% <span class="text-muted fw-normal">vs. ayer</span>
                    </div>
                </div>
            </div>

            <!-- Entregados -->
            <div class="col-6 col-md-6 col-xl">
                <div class="stat-card-clean">
                    <div class="stat-card-top">
                        <div class="stat-icon-circle stat-icon-green">
                            <i class="bi bi-check-circle"></i>
                        </div>
                        <div>
                            <p class="stat-card-title">Entregados</p>
                            <h3 class="stat-card-value"><?= $metrics['entregados'] ?></h3>
                        </div>
                    </div>
                    <div class="stat-card-trend trend-up">
                        <i class="bi bi-arrow-up-short"></i> 80% <span class="text-muted fw-normal">vs. ayer</span>
                    </div>
                </div>
            </div>

            <!-- Cancelados -->
            <div class="col-12 col-md-6 col-xl">
                <div class="stat-card-clean">
                    <div class="stat-card-top">
                        <div class="stat-icon-circle stat-icon-red">
                            <i class="bi bi-x-circle"></i>
                        </div>
                        <div>
                            <p class="stat-card-title">Cancelados</p>
                            <h3 class="stat-card-value"><?= $metrics['cancelados'] ?></h3>
                        </div>
                    </div>
                    <div class="stat-card-trend trend-down">
                        <i class="bi bi-arrow-down-short"></i> 50% <span class="text-muted fw-normal">vs. ayer</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- ====================================================================
             FILA 2 DE INDICADORES (VENTAS, PRODUCTOS Y STOCK)
        ==================================================================== -->
        <div class="row g-3 mb-4">
            <!-- Ventas del día -->
            <div class="col-12 col-sm-6 col-xl-3">
                <div class="stat-card-clean">
                    <div class="stat-card-top">
                        <div class="stat-icon-circle stat-icon-green">
                            <i class="bi bi-currency-dollar"></i>
                        </div>
                        <div>
                            <p class="stat-card-title">Ventas del día</p>
                            <h3 class="stat-card-value"><?= FormatHelper::money($metrics['ventas_dia']) ?></h3>
                        </div>
                    </div>
                    <div class="stat-card-trend trend-up">
                        <i class="bi bi-arrow-up-short"></i> 18% <span class="text-muted fw-normal">vs. ayer</span>
                    </div>
                </div>
            </div>

            <!-- Ventas del mes -->
            <div class="col-12 col-sm-6 col-xl-3">
                <div class="stat-card-clean">
                    <div class="stat-card-top">
                        <div class="stat-icon-circle stat-icon-purple">
                            <i class="bi bi-calendar4-week"></i>
                        </div>
                        <div>
                            <p class="stat-card-title">Ventas del mes</p>
                            <h3 class="stat-card-value"><?= FormatHelper::money($metrics['ventas_mes']) ?></h3>
                        </div>
                    </div>
                    <div class="stat-card-trend trend-up" style="color: #8b5cf6;">
                        <i class="bi bi-arrow-up-short"></i> 22% <span class="text-muted fw-normal">vs. mes anterior</span>
                    </div>
                </div>
            </div>

            <!-- Productos registrados -->
            <div class="col-12 col-sm-6 col-xl-3">
                <div class="stat-card-clean">
                    <div class="stat-card-top">
                        <div class="stat-icon-circle stat-icon-blue">
                            <i class="bi bi-box-seam"></i>
                        </div>
                        <div>
                            <p class="stat-card-title">Productos registrados</p>
                            <h3 class="stat-card-value"><?= $metrics['total_productos'] ?></h3>
                        </div>
                    </div>
                    <div class="stat-card-trend trend-up">
                        <i class="bi bi-arrow-up-short"></i> 6% <span class="text-muted fw-normal">vs. mes anterior</span>
                    </div>
                </div>
            </div>

            <!-- Stock bajo -->
            <div class="col-12 col-sm-6 col-xl-3">
                <div class="stat-card-clean">
                    <div class="stat-card-top">
                        <div class="stat-icon-circle stat-icon-red">
                            <i class="bi bi-exclamation-triangle"></i>
                        </div>
                        <div>
                            <p class="stat-card-title">Stock bajo</p>
                            <h3 class="stat-card-value text-danger"><?= $metrics['stock_bajo'] ?></h3>
                        </div>
                    </div>
                    <div>
                        <a href="index.php?route=productos&stock_bajo=1" class="text-danger small fw-bold text-decoration-none">
                            Ver productos <i class="bi bi-arrow-right"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- ====================================================================
             SECCIÓN DE GRÁFICOS (VENTAS POR DÍA + PEDIDOS POR ESTADO)
        ==================================================================== -->
        <div class="row g-4 mb-4">
            <!-- Gráfico de Ventas por Día -->
            <div class="col-12 col-xl-7">
                <div class="card-enterprise h-100">
                    <div class="card-header-clean">
                        <div class="card-title-clean">
                            <i class="bi bi-graph-up text-primary"></i> Ventas por día
                        </div>
                        <div class="dropdown">
                            <button class="btn btn-sm btn-outline-secondary dropdown-toggle rounded-pill px-3" type="button" data-bs-toggle="dropdown">
                                Últimos 7 días
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end shadow-sm">
                                <li><a class="dropdown-item active" href="#">Últimos 7 días</a></li>
                                <li><a class="dropdown-item" href="index.php?route=reportes">Ver reporte completo</a></li>
                            </ul>
                        </div>
                    </div>
                    <div class="card-body-clean">
                        <div style="height: 280px; position: relative;">
                            <canvas id="salesLineChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Gráfico de Pedidos por Estado -->
            <div class="col-12 col-xl-5">
                <div class="card-enterprise h-100">
                    <div class="card-header-clean">
                        <div class="card-title-clean">
                            <i class="bi bi-pie-chart text-primary"></i> Pedidos por estado
                        </div>
                        <span class="badge bg-light text-dark border rounded-pill px-2.5 py-1">Actualizado</span>
                    </div>
                    <div class="card-body-clean d-flex flex-column justify-content-center">
                        <div class="row align-items-center">
                            <div class="col-7 text-center position-relative">
                                <div style="height: 220px; width: 220px; margin: 0 auto;">
                                    <canvas id="ordersDoughnutChart"></canvas>
                                </div>
                                <div class="position-absolute top-50 start-50 translate-middle pointer-events-none text-center" style="margin-top: -5px;">
                                    <div class="fs-4 fw-bold text-dark lh-1"><?= $metrics['total_pedidos'] ?></div>
                                    <div class="text-muted" style="font-size: 11px;">Total</div>
                                </div>
                            </div>
                            <div class="col-5">
                                <ul class="list-unstyled mb-0" style="font-size: 12px;">
                                    <li class="mb-2 d-flex align-items-center justify-content-between">
                                        <span><i class="bi bi-circle-fill text-warning me-1.5" style="font-size: 9px;"></i> Pendiente</span>
                                        <span class="fw-bold"><?= $metrics['pendientes'] ?></span>
                                    </li>
                                    <li class="mb-2 d-flex align-items-center justify-content-between">
                                        <span><i class="bi bi-circle-fill text-warning-emphasis me-1.5" style="font-size: 9px; color: #d97706;"></i> Confirmado</span>
                                        <span class="fw-bold"><?= $metrics['confirmados'] ?></span>
                                    </li>
                                    <li class="mb-2 d-flex align-items-center justify-content-between">
                                        <span><i class="bi bi-circle-fill me-1.5" style="font-size: 9px; color: #8b5cf6;"></i> En preparación</span>
                                        <span class="fw-bold"><?= $metrics['en_preparacion'] ?></span>
                                    </li>
                                    <li class="mb-2 d-flex align-items-center justify-content-between">
                                        <span><i class="bi bi-circle-fill text-info me-1.5" style="font-size: 9px;"></i> Listo para entregar</span>
                                        <span class="fw-bold"><?= $metrics['listos'] ?></span>
                                    </li>
                                    <li class="mb-2 d-flex align-items-center justify-content-between">
                                        <span><i class="bi bi-circle-fill text-primary me-1.5" style="font-size: 9px;"></i> En camino</span>
                                        <span class="fw-bold"><?= $metrics['en_camino'] ?></span>
                                    </li>
                                    <li class="mb-2 d-flex align-items-center justify-content-between">
                                        <span><i class="bi bi-circle-fill text-success me-1.5" style="font-size: 9px;"></i> Entregado</span>
                                        <span class="fw-bold"><?= $metrics['entregados'] ?></span>
                                    </li>
                                    <li class="d-flex align-items-center justify-content-between">
                                        <span><i class="bi bi-circle-fill text-danger me-1.5" style="font-size: 9px;"></i> Cancelado</span>
                                        <span class="fw-bold"><?= $metrics['cancelados'] ?></span>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- ====================================================================
             FILA INFERIOR: PEDIDOS RECIENTES + ACCIONES RÁPIDAS + LATERAL DERECHO
        ==================================================================== -->
        <div class="row g-4">
            <!-- Columna Izquierda: Pedidos Recientes + Acciones Rápidas -->
            <div class="col-12 col-xl-8">
                <!-- Tabla de Pedidos Recientes -->
                <div class="card-enterprise mb-4">
                    <div class="card-header-clean">
                        <div class="card-title-clean">
                            <i class="bi bi-receipt text-primary"></i> Pedidos recientes
                        </div>
                        <a href="index.php?route=pedidos" class="text-primary small fw-bold text-decoration-none">
                            Ver todos <i class="bi bi-arrow-right"></i>
                        </a>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-modern align-middle mb-0">
                            <thead>
                                <tr>
                                    <th># Pedido</th>
                                    <th>Cliente</th>
                                    <th>Total</th>
                                    <th>Estado</th>
                                    <th>Modalidad</th>
                                    <th>Fecha y Hora</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($recentOrders)): ?>
                                    <tr>
                                        <td colspan="6" class="text-center py-4 text-muted">No hay pedidos registrados aún.</td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach (array_slice($recentOrders, 0, 5) as $ord): ?>
                                        <tr>
                                            <td class="fw-bold">
                                                <a href="index.php?route=pedidos_detalle&id=<?= $ord['id'] ?>" class="text-decoration-none text-dark">
                                                    <?= htmlspecialchars($ord['numero_pedido']) ?>
                                                </a>
                                            </td>
                                            <td>
                                                <div class="fw-semibold text-dark"><?= htmlspecialchars($ord['cliente_nombre']) ?></div>
                                            </td>
                                            <td class="fw-bold text-dark"><?= FormatHelper::money($ord['total']) ?></td>
                                            <td><?= FormatHelper::statusBadge($ord['estado']) ?></td>
                                            <td><?= FormatHelper::modalityBadge($ord['modalidad']) ?></td>
                                            <td class="text-muted" style="font-size: 12.5px;"><?= FormatHelper::date($ord['fecha_pedido'], 'd/m/Y H:i') ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Tarjetas de Acciones Rápidas -->
                <div class="card-enterprise">
                    <div class="card-header-clean">
                        <div class="card-title-clean">
                            <i class="bi bi-lightning-charge-fill text-warning"></i> Acciones rápidas
                        </div>
                    </div>
                    <div class="card-body-clean">
                        <div class="row g-3">
                            <div class="col-6 col-sm-3">
                                <a href="index.php?route=pedidos_crear" class="btn-quick-action btn-qa-blue">
                                    <i class="bi bi-cart-plus-fill fs-5"></i>
                                    <span>Nuevo pedido</span>
                                </a>
                            </div>
                            <div class="col-6 col-sm-3">
                                <a href="index.php?route=productos" class="btn-quick-action btn-qa-green">
                                    <i class="bi bi-box-seam-fill fs-5"></i>
                                    <span>Registrar producto</span>
                                </a>
                            </div>
                            <div class="col-6 col-sm-3">
                                <a href="index.php?route=clientes" class="btn-quick-action btn-qa-orange">
                                    <i class="bi bi-person-plus-fill fs-5"></i>
                                    <span>Agregar cliente</span>
                                </a>
                            </div>
                            <div class="col-6 col-sm-3">
                                <a href="index.php?route=reportes" class="btn-quick-action btn-qa-purple">
                                    <i class="bi bi-bar-chart-fill fs-5"></i>
                                    <span>Generar reporte</span>
                                </a>
                            </div>
                        </div>

                        <!-- Banner Promocional -->
                        <div class="promo-banner-card">
                            <img src="public/uploads/productos/burger.jpg" alt="Hamburguesa" class="promo-banner-img" onerror="this.src='https://images.unsplash.com/photo-1568901346375-23c9450c58cd?w=160&q=80'">
                            <div class="flex-grow-1">
                                <h5 class="fw-bold mb-1 fs-6 text-dark">Haz crecer tu negocio</h5>
                                <p class="text-muted small mb-0">Gestiona tus pedidos de forma fácil, rápida y segura en tiempo real.</p>
                            </div>
                            <a href="index.php?route=pedidos_crear" class="btn btn-sm btn-primary rounded-pill px-3">
                                Ver más <i class="bi bi-arrow-right"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Columna Derecha: Últimos Pedidos + Productos Más Vendidos -->
            <div class="col-12 col-xl-4">
                <!-- Tarjeta: Últimos Pedidos -->
                <div class="card-enterprise mb-4">
                    <div class="card-header-clean">
                        <div class="card-title-clean">
                            <i class="bi bi-clock-history text-primary"></i> Últimos pedidos
                        </div>
                    </div>
                    <div class="card-body-clean py-1">
                        <?php if (empty($recentOrders)): ?>
                            <p class="text-muted small py-3 mb-0">No hay pedidos recientes.</p>
                        <?php else: ?>
                            <?php foreach (array_slice($recentOrders, 0, 5) as $idx => $ord): ?>
                                <div class="side-order-item">
                                    <div class="d-flex align-items-center gap-2.5">
                                        <?php 
                                            // Imagen contextual según índice
                                            $thumbImg = 'public/uploads/productos/burger.jpg';
                                            if ($idx % 3 === 1) $thumbImg = 'public/uploads/productos/pizza.jpg';
                                            if ($idx % 3 === 2) $thumbImg = 'public/uploads/productos/fries.jpg';
                                        ?>
                                        <img src="<?= $thumbImg ?>" class="side-product-thumb" alt="Miniatura">
                                        <div>
                                            <a href="index.php?route=pedidos_detalle&id=<?= $ord['id'] ?>" class="side-order-number">
                                                <?= htmlspecialchars($ord['numero_pedido']) ?>
                                            </a>
                                            <p class="side-order-client"><?= htmlspecialchars($ord['cliente_nombre']) ?></p>
                                            <div class="side-order-price"><?= FormatHelper::money($ord['total']) ?></div>
                                        </div>
                                    </div>
                                    <div class="text-end">
                                        <div><?= FormatHelper::statusBadge($ord['estado']) ?></div>
                                        <div class="text-muted" style="font-size: 11px; margin-top: 4px;"><?= FormatHelper::timeAgo($ord['fecha_pedido']) ?></div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                            <div class="text-center py-2 border-top mt-2">
                                <a href="index.php?route=pedidos" class="text-primary small fw-bold text-decoration-none">
                                    Ver todos los pedidos <i class="bi bi-arrow-right"></i>
                                </a>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Tarjeta: Productos Más Vendidos -->
                <div class="card-enterprise">
                    <div class="card-header-clean">
                        <div class="card-title-clean">
                            <i class="bi bi-award-fill text-warning"></i> Productos más vendidos
                        </div>
                    </div>
                    <div class="card-body-clean py-1">
                        <?php if (empty($topProducts)): ?>
                            <p class="text-muted small py-3 mb-0">No hay datos de ventas aún.</p>
                        <?php else: ?>
                            <?php foreach ($topProducts as $rank => $prod): ?>
                                <?php 
                                    $rankNum = $rank + 1;
                                    $rankClass = 'rank-other';
                                    if ($rankNum === 1) $rankClass = 'rank-1';
                                    elseif ($rankNum === 2) $rankClass = 'rank-2';
                                    elseif ($rankNum === 3) $rankClass = 'rank-3';

                                    $prodImg = 'public/uploads/productos/' . ($prod['imagen'] ?: 'burger.jpg');
                                    if (!file_exists($prodImg)) $prodImg = 'public/uploads/productos/burger.jpg';
                                ?>
                                <div class="side-order-item">
                                    <div class="d-flex align-items-center gap-2.5">
                                        <span class="rank-badge <?= $rankClass ?>"><?= $rankNum ?></span>
                                        <img src="<?= $prodImg ?>" class="side-product-thumb" alt="<?= htmlspecialchars($prod['nombre']) ?>" onerror="this.src='public/uploads/productos/burger.jpg'">
                                        <div>
                                            <div class="fw-bold text-dark" style="font-size: 13px;"><?= htmlspecialchars($prod['nombre']) ?></div>
                                            <div class="text-muted" style="font-size: 11.5px;"><?= FormatHelper::money($prod['precio_venta']) ?></div>
                                        </div>
                                    </div>
                                    <div class="text-end">
                                        <span class="badge bg-light text-dark border fw-bold"><?= $prod['total_vendido'] ?> uds</span>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

    </div> <!-- Fin app-content -->
</main>

<!-- Inyección de datos para Chart.js -->
<script>
window.chartData = {
    sales: <?= json_encode($chartSales) ?>,
    metrics: <?= json_encode($metrics) ?>
};
</script>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
