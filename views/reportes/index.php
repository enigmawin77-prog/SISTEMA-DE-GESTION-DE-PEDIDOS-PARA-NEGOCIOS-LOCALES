<?php
$pageTitle = "Reportes y Estadísticas";
require __DIR__ . '/../layouts/header.php';
require __DIR__ . '/../layouts/sidebar.php';

use App\Helpers\FormatHelper;
use App\Helpers\FlashHelper;

$avgTicket = $reporte['total_pedidos'] > 0 ? ($reporte['total_ventas'] / $reporte['total_pedidos']) : 0;
?>

<main class="app-main">
    <?php require __DIR__ . '/../layouts/navbar.php'; ?>

    <div class="app-content">
        <?= FlashHelper::render() ?>

        <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
            <div>
                <h2 class="h4 fw-bold text-dark mb-1">Reportes y Analítica del Negocio</h2>
                <p class="text-muted small mb-0">Balance de ventas, pedidos por período, productos con mayor rotación e inventario crítico.</p>
            </div>
            <div class="d-flex gap-2">
                <!-- Botón de Exportación a CSV / Excel -->
                <a href="index.php?route=reportes_exportar&fecha_desde=<?= urlencode($filters['fecha_desde']) ?>&fecha_hasta=<?= urlencode($filters['fecha_hasta']) ?>&estado=<?= urlencode($filters['estado']) ?>&cliente_id=<?= urlencode($filters['cliente_id']) ?>&modalidad=<?= urlencode($filters['modalidad']) ?>" class="btn btn-outline-success rounded-pill px-3 py-1.5 fw-bold shadow-2xs">
                    <i class="bi bi-file-earmark-excel me-1.5"></i> Exportar a Excel (CSV)
                </a>
                <button type="button" class="btn btn-outline-dark rounded-pill px-3 py-1.5 fw-bold shadow-2xs" onclick="window.print()">
                    <i class="bi bi-printer me-1.5"></i> Imprimir Reporte
                </button>
            </div>
        </div>

        <!-- Filtros de Reporte -->
        <div class="card-enterprise mb-4">
            <div class="card-body-clean">
                <form action="index.php" method="GET" class="row g-2.5 align-items-end">
                    <input type="hidden" name="route" value="reportes">

                    <div class="col-6 col-md-2">
                        <label class="form-label small fw-bold text-secondary mb-1">Fecha Desde</label>
                        <input type="date" name="fecha_desde" class="form-control form-control-sm" value="<?= htmlspecialchars($filters['fecha_desde']) ?>">
                    </div>

                    <div class="col-6 col-md-2">
                        <label class="form-label small fw-bold text-secondary mb-1">Fecha Hasta</label>
                        <input type="date" name="fecha_hasta" class="form-control form-control-sm" value="<?= htmlspecialchars($filters['fecha_hasta']) ?>">
                    </div>

                    <div class="col-6 col-md-2">
                        <label class="form-label small fw-bold text-secondary mb-1">Estado</label>
                        <select name="estado" class="form-select form-select-sm">
                            <option value="">Todos los estados</option>
                            <option value="entregado" <?= $filters['estado'] === 'entregado' ? 'selected' : '' ?>>Solo Entregados</option>
                            <option value="pendiente" <?= $filters['estado'] === 'pendiente' ? 'selected' : '' ?>>Pendientes</option>
                            <option value="confirmado" <?= $filters['estado'] === 'confirmado' ? 'selected' : '' ?>>Confirmados</option>
                            <option value="en_preparacion" <?= $filters['estado'] === 'en_preparacion' ? 'selected' : '' ?>>En preparación</option>
                            <option value="cancelado" <?= $filters['estado'] === 'cancelado' ? 'selected' : '' ?>>Cancelados</option>
                        </select>
                    </div>

                    <div class="col-6 col-md-3">
                        <label class="form-label small fw-bold text-secondary mb-1">Filtrar por Cliente</label>
                        <select name="cliente_id" class="form-select form-select-sm">
                            <option value="">Todos los clientes</option>
                            <?php foreach ($clientes as $cli): ?>
                                <option value="<?= $cli['id'] ?>" <?= $filters['cliente_id'] == $cli['id'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($cli['nombre']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="col-12 col-md-3 d-flex gap-2">
                        <button type="submit" class="btn btn-primary btn-sm rounded-pill flex-grow-1 fw-bold">
                            <i class="bi bi-funnel"></i> Aplicar Filtros
                        </button>
                        <a href="index.php?route=reportes" class="btn btn-outline-secondary btn-sm rounded-pill" title="Restablecer">
                            <i class="bi bi-arrow-counterclockwise"></i>
                        </a>
                    </div>
                </form>
            </div>
        </div>

        <!-- Tarjetas de KPIs del Período -->
        <div class="row g-3 mb-4">
            <div class="col-12 col-md-4">
                <div class="stat-card-clean">
                    <div class="stat-card-top">
                        <div class="stat-icon-circle stat-icon-green">
                            <i class="bi bi-cash-stack"></i>
                        </div>
                        <div>
                            <p class="stat-card-title">Ventas del Período</p>
                            <h3 class="stat-card-value text-success"><?= FormatHelper::money($reporte['total_ventas']) ?></h3>
                        </div>
                    </div>
                    <div class="small text-muted">Pedidos finalizados y activos</div>
                </div>
            </div>

            <div class="col-12 col-md-4">
                <div class="stat-card-clean">
                    <div class="stat-card-top">
                        <div class="stat-icon-circle stat-icon-blue">
                            <i class="bi bi-receipt"></i>
                        </div>
                        <div>
                            <p class="stat-card-title">Total de Pedidos</p>
                            <h3 class="stat-card-value text-primary"><?= $reporte['total_pedidos'] ?></h3>
                        </div>
                    </div>
                    <div class="small text-muted"><?= $reporte['total_items'] ?> productos despachados</div>
                </div>
            </div>

            <div class="col-12 col-md-4">
                <div class="stat-card-clean">
                    <div class="stat-card-top">
                        <div class="stat-icon-circle stat-icon-purple">
                            <i class="bi bi-calculator"></i>
                        </div>
                        <div>
                            <p class="stat-card-title">Ticket Promedio</p>
                            <h3 class="stat-card-value text-purple" style="color: #8b5cf6;"><?= FormatHelper::money($avgTicket) ?></h3>
                        </div>
                    </div>
                    <div class="small text-muted">Monto promedio por compra</div>
                </div>
            </div>
        </div>

        <div class="row g-4">
            <!-- Tabla de Pedidos del Período -->
            <div class="col-12 col-xl-8">
                <div class="card-enterprise">
                    <div class="card-header-clean">
                        <div class="card-title-clean">
                            <i class="bi bi-table text-primary"></i> Detalle de Pedidos en el Período
                        </div>
                        <span class="badge bg-light text-dark border"><?= count($reporte['orders']) ?> pedidos</span>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-modern align-middle mb-0">
                            <thead>
                                <tr>
                                    <th># Pedido</th>
                                    <th>Fecha</th>
                                    <th>Cliente</th>
                                    <th>Modalidad</th>
                                    <th>Estado</th>
                                    <th class="text-end">Total</th>
                                    <th class="text-center">Acción</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($reporte['orders'])): ?>
                                    <tr>
                                        <td colspan="7" class="text-center py-5 text-muted">No se registran pedidos en el rango de fechas seleccionado.</td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($reporte['orders'] as $ord): ?>
                                        <tr>
                                            <td class="fw-bold"><?= htmlspecialchars($ord['numero_pedido']) ?></td>
                                            <td class="small"><?= FormatHelper::date($ord['fecha_pedido']) ?></td>
                                            <td class="fw-semibold text-dark"><?= htmlspecialchars($ord['cliente_nombre']) ?></td>
                                            <td><?= FormatHelper::modalityBadge($ord['modalidad']) ?></td>
                                            <td><?= FormatHelper::statusBadge($ord['estado']) ?></td>
                                            <td class="text-end fw-bold fs-6 text-dark"><?= FormatHelper::money($ord['total']) ?></td>
                                            <td class="text-center">
                                                <a href="index.php?route=pedidos_detalle&id=<?= $ord['id'] ?>" class="btn btn-outline-secondary btn-sm" title="Ver Detalle">
                                                    <i class="bi bi-eye"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Columna Derecha: Más Vendidos y Stock Crítico -->
            <div class="col-12 col-xl-4">
                <!-- Productos más vendidos -->
                <div class="card-enterprise mb-4">
                    <div class="card-header-clean">
                        <div class="card-title-clean">
                            <i class="bi bi-trophy-fill text-warning"></i> Productos Más Vendidos
                        </div>
                    </div>
                    <div class="card-body-clean py-1">
                        <?php foreach ($topProducts as $idx => $prod): ?>
                            <div class="d-flex align-items-center justify-content-between py-2 border-bottom">
                                <div class="d-flex align-items-center gap-2">
                                    <span class="badge bg-light text-dark border"><?= $idx + 1 ?></span>
                                    <div>
                                        <div class="fw-bold text-dark small"><?= htmlspecialchars($prod['nombre']) ?></div>
                                        <div class="text-muted" style="font-size: 11px;"><?= FormatHelper::money($prod['precio_venta']) ?></div>
                                    </div>
                                </div>
                                <div class="text-end">
                                    <div class="fw-bold text-primary small"><?= $prod['total_vendido'] ?> uds</div>
                                    <div class="text-muted" style="font-size: 10.5px;"><?= FormatHelper::money($prod['ingresos_generados']) ?></div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- Alerta de Stock Bajo -->
                <div class="card-enterprise">
                    <div class="card-header-clean">
                        <div class="card-title-clean text-danger">
                            <i class="bi bi-exclamation-octagon-fill text-danger"></i> Stock Bajo / Crítico
                        </div>
                    </div>
                    <div class="card-body-clean py-1">
                        <?php if (empty($lowStock)): ?>
                            <p class="text-muted small py-3 mb-0 text-center">Todos los productos cuentan con stock suficiente.</p>
                        <?php else: ?>
                            <?php foreach ($lowStock as $ls): ?>
                                <div class="d-flex align-items-center justify-content-between py-2 border-bottom">
                                    <div>
                                        <div class="fw-bold text-dark small"><?= htmlspecialchars($ls['nombre']) ?></div>
                                        <div class="text-muted" style="font-size: 11px;">Mínimo requerido: <?= $ls['stock_minimo'] ?> uds</div>
                                    </div>
                                    <div>
                                        <span class="badge bg-danger text-white rounded-pill px-2.5 py-1">
                                            <?= $ls['stock'] ?> uds
                                        </span>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

    </div>
</main>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
