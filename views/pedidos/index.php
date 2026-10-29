<?php
$pageTitle = "Gestión de Pedidos";
require __DIR__ . '/../layouts/header.php';
require __DIR__ . '/../layouts/sidebar.php';

use App\Helpers\FormatHelper;
use App\Helpers\FlashHelper;
use App\Helpers\CsrfHelper;
?>

<main class="app-main">
    <?php require __DIR__ . '/../layouts/navbar.php'; ?>

    <div class="app-content">
        <?= FlashHelper::render() ?>

        <!-- Encabezado de la Sección -->
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
            <div>
                <h2 class="h4 fw-bold text-dark mb-1">Gestión de Pedidos</h2>
                <p class="text-muted small mb-0">Control integral de pedidos, estados de preparación, pagos y entregas.</p>
            </div>
            <div>
                <a href="index.php?route=pedidos_crear" class="btn btn-primary rounded-pill px-3.5 py-2 fw-bold shadow-sm">
                    <i class="bi bi-plus-circle me-1.5"></i> Nuevo Pedido
                </a>
            </div>
        </div>

        <!-- Filtros de Búsqueda -->
        <div class="card-enterprise mb-4">
            <div class="card-body-clean">
                <form action="index.php" method="GET" class="row g-2.5 align-items-end">
                    <input type="hidden" name="route" value="pedidos">

                    <div class="col-12 col-md-3">
                        <label class="form-label small fw-bold text-secondary mb-1">Buscar</label>
                        <div class="input-group input-group-sm">
                            <span class="input-group-text bg-light text-muted border-end-0"><i class="bi bi-search"></i></span>
                            <input type="text" name="search" class="form-control border-start-0 ps-0" placeholder="# Pedido, cliente, teléfono..." value="<?= htmlspecialchars($filters['search'] ?? '') ?>">
                        </div>
                    </div>

                    <div class="col-6 col-md-2">
                        <label class="form-label small fw-bold text-secondary mb-1">Estado</label>
                        <select name="estado" class="form-select form-select-sm">
                            <option value="">Todos los estados</option>
                            <option value="pendiente" <?= ($filters['estado'] ?? '') === 'pendiente' ? 'selected' : '' ?>>Pendiente</option>
                            <option value="confirmado" <?= ($filters['estado'] ?? '') === 'confirmado' ? 'selected' : '' ?>>Confirmado</option>
                            <option value="en_preparacion" <?= ($filters['estado'] ?? '') === 'en_preparacion' ? 'selected' : '' ?>>En preparación</option>
                            <option value="listo" <?= ($filters['estado'] ?? '') === 'listo' ? 'selected' : '' ?>>Listo para entregar</option>
                            <option value="en_camino" <?= ($filters['estado'] ?? '') === 'en_camino' ? 'selected' : '' ?>>En camino</option>
                            <option value="entregado" <?= ($filters['estado'] ?? '') === 'entregado' ? 'selected' : '' ?>>Entregado</option>
                            <option value="cancelado" <?= ($filters['estado'] ?? '') === 'cancelado' ? 'selected' : '' ?>>Cancelado</option>
                        </select>
                    </div>

                    <div class="col-6 col-md-2">
                        <label class="form-label small fw-bold text-secondary mb-1">Modalidad</label>
                        <select name="modalidad" class="form-select form-select-sm">
                            <option value="">Todas</option>
                            <option value="domicilio" <?= ($filters['modalidad'] ?? '') === 'domicilio' ? 'selected' : '' ?>>Domicilio</option>
                            <option value="recojo" <?= ($filters['modalidad'] ?? '') === 'recojo' ? 'selected' : '' ?>>Recojo</option>
                        </select>
                    </div>

                    <div class="col-6 col-md-2">
                        <label class="form-label small fw-bold text-secondary mb-1">Estado de Pago</label>
                        <select name="estado_pago" class="form-select form-select-sm">
                            <option value="">Todos</option>
                            <option value="pagado" <?= ($filters['estado_pago'] ?? '') === 'pagado' ? 'selected' : '' ?>>Pagado</option>
                            <option value="parcial" <?= ($filters['estado_pago'] ?? '') === 'parcial' ? 'selected' : '' ?>>Parcial</option>
                            <option value="pendiente" <?= ($filters['estado_pago'] ?? '') === 'pendiente' ? 'selected' : '' ?>>Pendiente</option>
                        </select>
                    </div>

                    <div class="col-6 col-md-3 d-flex gap-2">
                        <button type="submit" class="btn btn-primary btn-sm rounded-pill flex-grow-1 fw-bold">
                            <i class="bi bi-funnel"></i> Filtrar
                        </button>
                        <a href="index.php?route=pedidos" class="btn btn-outline-secondary btn-sm rounded-pill" title="Limpiar filtros">
                            <i class="bi bi-arrow-counterclockwise"></i>
                        </a>
                    </div>
                </form>
            </div>
        </div>

        <!-- Tabla Principal de Pedidos -->
        <div class="card-enterprise">
            <div class="table-responsive">
                <table class="table table-modern align-middle mb-0">
                    <thead>
                        <tr>
                            <th># Pedido</th>
                            <th>Fecha</th>
                            <th>Cliente</th>
                            <th>Modalidad</th>
                            <th>Estado Pedido</th>
                            <th>Estado Pago</th>
                            <th class="text-end">Total</th>
                            <th class="text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($pedidos)): ?>
                            <tr>
                                <td colspan="8" class="text-center py-5 text-muted">
                                    <i class="bi bi-inbox fs-2 d-block mb-2 text-secondary"></i>
                                    No se encontraron pedidos con los criterios seleccionados.
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($pedidos as $p): ?>
                                <tr>
                                    <td>
                                        <a href="index.php?route=pedidos_detalle&id=<?= $p['id'] ?>" class="fw-bold text-decoration-none text-dark">
                                            <?= htmlspecialchars($p['numero_pedido']) ?>
                                        </a>
                                        <div class="text-muted" style="font-size: 11px;">
                                            <i class="bi bi-basket me-1"></i><?= $p['total_items'] ?> items
                                        </div>
                                    </td>
                                    <td>
                                        <div class="fw-semibold text-dark" style="font-size: 13px;"><?= FormatHelper::date($p['fecha_pedido'], 'd/m/Y') ?></div>
                                        <div class="text-muted" style="font-size: 11px;"><?= FormatHelper::timeOnly($p['fecha_pedido']) ?></div>
                                    </td>
                                    <td>
                                        <div class="fw-bold text-dark"><?= htmlspecialchars($p['cliente_nombre']) ?></div>
                                        <div class="text-muted" style="font-size: 11.5px;"><?= htmlspecialchars($p['cliente_telefono'] ?: 'Sin teléfono') ?></div>
                                    </td>
                                    <td><?= FormatHelper::modalityBadge($p['modalidad']) ?></td>
                                    <td><?= FormatHelper::statusBadge($p['estado']) ?></td>
                                    <td>
                                        <div><?= FormatHelper::paymentBadge($p['estado_pago']) ?></div>
                                        <?php if ((float)$p['saldo_pendiente'] > 0): ?>
                                            <div class="text-danger fw-semibold" style="font-size: 11px;">Resta: <?= FormatHelper::money($p['saldo_pendiente']) ?></div>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-end">
                                        <div class="fw-bold fs-6 text-dark"><?= FormatHelper::money($p['total']) ?></div>
                                    </td>
                                    <td class="text-center">
                                        <div class="btn-group btn-group-sm">
                                            <a href="index.php?route=pedidos_detalle&id=<?= $p['id'] ?>" class="btn btn-outline-secondary" title="Ver Detalle">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                            <a href="index.php?route=pedidos_comprobante&id=<?= $p['id'] ?>" target="_blank" class="btn btn-outline-secondary" title="Imprimir Comprobante">
                                                <i class="bi bi-printer"></i>
                                            </a>
                                            <?php if (!in_array($p['estado'], ['entregado', 'cancelado'])): ?>
                                                <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#modalEstado<?= $p['id'] ?>" title="Cambiar Estado">
                                                    <i class="bi bi-arrow-repeat"></i>
                                                </button>
                                            <?php endif; ?>
                                        </div>

                                        <!-- Modal Rápido de Cambio de Estado -->
                                        <?php if (!in_array($p['estado'], ['entregado', 'cancelado'])): ?>
                                            <div class="modal fade text-start" id="modalEstado<?= $p['id'] ?>" tabindex="-1" aria-hidden="true">
                                                <div class="modal-dialog modal-dialog-centered modal-sm">
                                                    <div class="modal-content border-0 shadow">
                                                        <div class="modal-header">
                                                            <h6 class="modal-title fw-bold">Actualizar <?= htmlspecialchars($p['numero_pedido']) ?></h6>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                        </div>
                                                        <form action="index.php?route=pedidos_cambiar_estado" method="POST">
                                                            <?= CsrfHelper::field() ?>
                                                            <input type="hidden" name="pedido_id" value="<?= $p['id'] ?>">
                                                            <div class="modal-body">
                                                                <label class="form-label small fw-bold">Nuevo Estado:</label>
                                                                <select name="nuevo_estado" class="form-select form-select-sm mb-3" required>
                                                                    <option value="confirmado" <?= $p['estado'] === 'confirmado' ? 'selected' : '' ?>>Confirmado</option>
                                                                    <option value="en_preparacion" <?= $p['estado'] === 'en_preparacion' ? 'selected' : '' ?>>En preparación</option>
                                                                    <option value="listo" <?= $p['estado'] === 'listo' ? 'selected' : '' ?>>Listo para entregar</option>
                                                                    <option value="en_camino" <?= $p['estado'] === 'en_camino' ? 'selected' : '' ?>>En camino</option>
                                                                    <option value="entregado">Entregado</option>
                                                                    <option value="cancelado" class="text-danger fw-bold">Cancelar Pedido</option>
                                                                </select>

                                                                <label class="form-label small fw-bold">Motivo u observación:</label>
                                                                <input type="text" name="motivo" class="form-control form-control-sm" placeholder="Ej: Cocina completó el pedido">
                                                            </div>
                                                            <div class="modal-footer p-2">
                                                                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cerrar</button>
                                                                <button type="submit" class="btn btn-primary btn-sm">Guardar Estado</button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php endif; ?>
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
                    <span class="small text-muted">Mostrando <?= count($pedidos) ?> de <?= $total ?> pedidos</span>
                    <nav>
                        <ul class="pagination pagination-sm mb-0">
                            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                                <li class="page-item <?= $i == $page ? 'active' : '' ?>">
                                    <a class="page-link" href="index.php?route=pedidos&page=<?= $i ?>&search=<?= urlencode($filters['search']) ?>&estado=<?= urlencode($filters['estado']) ?>&modalidad=<?= urlencode($filters['modalidad']) ?>&estado_pago=<?= urlencode($filters['estado_pago']) ?>">
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
