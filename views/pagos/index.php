<?php
$pageTitle = "Gestión de Pagos";
require __DIR__ . '/../layouts/header.php';
require __DIR__ . '/../layouts/sidebar.php';

use App\Helpers\FormatHelper;
use App\Helpers\FlashHelper;
?>

<main class="app-main">
    <?php require __DIR__ . '/../layouts/navbar.php'; ?>

    <div class="app-content">
        <?= FlashHelper::render() ?>

        <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
            <div>
                <h2 class="h4 fw-bold text-dark mb-1">Registro de Pagos e Ingresos</h2>
                <p class="text-muted small mb-0">Auditoría de cobros, métodos de pago electrónicos (Yape, Plin, Transferencias) y efectivo.</p>
            </div>
            <div>
                <a href="index.php?route=pedidos" class="btn btn-primary rounded-pill px-3.5 py-2 fw-bold shadow-sm">
                    <i class="bi bi-receipt me-1.5"></i> Ver Pedidos Pendientes de Pago
                </a>
            </div>
        </div>

        <!-- Filtros -->
        <div class="card-enterprise mb-4">
            <div class="card-body-clean">
                <form action="index.php" method="GET" class="row g-2.5 align-items-end">
                    <input type="hidden" name="route" value="pagos">

                    <div class="col-12 col-md-3">
                        <label class="form-label small fw-bold text-secondary mb-1">Buscar</label>
                        <div class="input-group input-group-sm">
                            <span class="input-group-text bg-light text-muted border-end-0"><i class="bi bi-search"></i></span>
                            <input type="text" name="search" class="form-control border-start-0 ps-0" placeholder="# Pedido, referencia, cliente..." value="<?= htmlspecialchars($filters['search'] ?? '') ?>">
                        </div>
                    </div>

                    <div class="col-6 col-md-3">
                        <label class="form-label small fw-bold text-secondary mb-1">Método de Pago</label>
                        <select name="metodo_pago" class="form-select form-select-sm">
                            <option value="">Todos los métodos</option>
                            <option value="efectivo" <?= ($filters['metodo_pago'] ?? '') === 'efectivo' ? 'selected' : '' ?>>Efectivo</option>
                            <option value="yape" <?= ($filters['metodo_pago'] ?? '') === 'yape' ? 'selected' : '' ?>>Yape</option>
                            <option value="plin" <?= ($filters['metodo_pago'] ?? '') === 'plin' ? 'selected' : '' ?>>Plin</option>
                            <option value="transferencia" <?= ($filters['metodo_pago'] ?? '') === 'transferencia' ? 'selected' : '' ?>>Transferencia Bancaria</option>
                            <option value="tarjeta" <?= ($filters['metodo_pago'] ?? '') === 'tarjeta' ? 'selected' : '' ?>>Tarjeta</option>
                            <option value="otro" <?= ($filters['metodo_pago'] ?? '') === 'otro' ? 'selected' : '' ?>>Otro</option>
                        </select>
                    </div>

                    <div class="col-6 col-md-2">
                        <label class="form-label small fw-bold text-secondary mb-1">Desde</label>
                        <input type="date" name="fecha_desde" class="form-control form-control-sm" value="<?= htmlspecialchars($filters['fecha_desde'] ?? '') ?>">
                    </div>

                    <div class="col-6 col-md-2">
                        <label class="form-label small fw-bold text-secondary mb-1">Hasta</label>
                        <input type="date" name="fecha_hasta" class="form-control form-control-sm" value="<?= htmlspecialchars($filters['fecha_hasta'] ?? '') ?>">
                    </div>

                    <div class="col-6 col-md-2 d-flex gap-2">
                        <button type="submit" class="btn btn-primary btn-sm rounded-pill flex-grow-1 fw-bold">
                            <i class="bi bi-funnel"></i> Filtrar
                        </button>
                        <a href="index.php?route=pagos" class="btn btn-outline-secondary btn-sm rounded-pill" title="Limpiar">
                            <i class="bi bi-arrow-counterclockwise"></i>
                        </a>
                    </div>
                </form>
            </div>
        </div>

        <!-- Tabla de Pagos -->
        <div class="card-enterprise">
            <div class="table-responsive">
                <table class="table table-modern align-middle mb-0">
                    <thead>
                        <tr>
                            <th>Fecha y Hora</th>
                            <th># Pedido</th>
                            <th>Cliente</th>
                            <th>Método de Pago</th>
                            <th>Referencia</th>
                            <th class="text-end">Monto Recibido</th>
                            <th>Registrado por</th>
                            <th class="text-center">Acción</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($pagos)): ?>
                            <tr>
                                <td colspan="8" class="text-center py-5 text-muted">
                                    <i class="bi bi-cash-coin fs-2 d-block mb-2 text-secondary"></i>
                                    No se encontraron transacciones de pago registradas.
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($pagos as $p): ?>
                                <tr>
                                    <td>
                                        <div class="fw-semibold text-dark" style="font-size: 13px;"><?= FormatHelper::date($p['fecha_pago']) ?></div>
                                    </td>
                                    <td>
                                        <a href="index.php?route=pedidos_detalle&id=<?= $p['pedido_id'] ?>" class="fw-bold text-decoration-none text-dark">
                                            <?= htmlspecialchars($p['numero_pedido']) ?>
                                        </a>
                                        <div><?= FormatHelper::paymentBadge($p['pedido_estado_pago']) ?></div>
                                    </td>
                                    <td>
                                        <div class="fw-bold text-dark"><?= htmlspecialchars($p['cliente_nombre']) ?></div>
                                    </td>
                                    <td>
                                        <span class="badge bg-light text-dark border text-uppercase px-2.5 py-1">
                                            <i class="bi bi-wallet2 me-1"></i><?= htmlspecialchars($p['metodo_pago']) ?>
                                        </span>
                                    </td>
                                    <td class="small text-muted font-monospace">
                                        <?= htmlspecialchars($p['referencia'] ?: '-') ?>
                                    </td>
                                    <td class="text-end fw-bold text-success fs-6">
                                        <?= FormatHelper::money($p['monto']) ?>
                                    </td>
                                    <td>
                                        <span class="small text-secondary"><?= htmlspecialchars($p['usuario_nombre']) ?></span>
                                    </td>
                                    <td class="text-center">
                                        <a href="index.php?route=pedidos_detalle&id=<?= $p['pedido_id'] ?>" class="btn btn-outline-secondary btn-sm" title="Ver Pedido">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <!-- Paginación -->
            <?php if ($totalPages > 1): ?>
                <div class="p-3 border-top d-flex align-items-center justify-content-between">
                    <span class="small text-muted">Mostrando <?= count($pagos) ?> de <?= $total ?> pagos</span>
                    <nav>
                        <ul class="pagination pagination-sm mb-0">
                            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                                <li class="page-item <?= $i == $page ? 'active' : '' ?>">
                                    <a class="page-link" href="index.php?route=pagos&page=<?= $i ?>&search=<?= urlencode($filters['search']) ?>&metodo_pago=<?= urlencode($filters['metodo_pago']) ?>&fecha_desde=<?= urlencode($filters['fecha_desde']) ?>&fecha_hasta=<?= urlencode($filters['fecha_hasta']) ?>">
                                        <?= $i ?>
                                    </a>
                                </li>
                            <?php endfor; ?>
                        </ul>
                    </nav>
                </div>
            <?php endif; ?>
        </div>

    </div>
</main>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
