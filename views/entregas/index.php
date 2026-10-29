<?php
$pageTitle = "Gestión de Entregas";
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

        <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
            <div>
                <h2 class="h4 fw-bold text-dark mb-1">Control de Entregas y Delivery</h2>
                <p class="text-muted small mb-0">Asignación de motorizados, seguimiento de rutas, despachos y confirmación de entregas.</p>
            </div>
        </div>

        <!-- Filtros -->
        <div class="card-enterprise mb-4">
            <div class="card-body-clean">
                <form action="index.php" method="GET" class="row g-2.5 align-items-end">
                    <input type="hidden" name="route" value="entregas">

                    <div class="col-12 col-md-6">
                        <label class="form-label small fw-bold text-secondary mb-1">Buscar</label>
                        <div class="input-group input-group-sm">
                            <span class="input-group-text bg-light text-muted border-end-0"><i class="bi bi-search"></i></span>
                            <input type="text" name="search" class="form-control border-start-0 ps-0" placeholder="# Pedido, repartidor, dirección, cliente..." value="<?= htmlspecialchars($filters['search'] ?? '') ?>">
                        </div>
                    </div>

                    <div class="col-6 col-md-3">
                        <label class="form-label small fw-bold text-secondary mb-1">Estado de Entrega</label>
                        <select name="estado" class="form-select form-select-sm">
                            <option value="">Todos los estados</option>
                            <option value="pendiente" <?= ($filters['estado'] ?? '') === 'pendiente' ? 'selected' : '' ?>>Pendiente</option>
                            <option value="en_camino" <?= ($filters['estado'] ?? '') === 'en_camino' ? 'selected' : '' ?>>En camino</option>
                            <option value="entregado" <?= ($filters['estado'] ?? '') === 'entregado' ? 'selected' : '' ?>>Entregado</option>
                            <option value="fallido" <?= ($filters['estado'] ?? '') === 'fallido' ? 'selected' : '' ?>>Fallido</option>
                        </select>
                    </div>

                    <div class="col-6 col-md-3 d-flex gap-2">
                        <button type="submit" class="btn btn-primary btn-sm rounded-pill flex-grow-1 fw-bold">
                            <i class="bi bi-funnel"></i> Filtrar
                        </button>
                        <a href="index.php?route=entregas" class="btn btn-outline-secondary btn-sm rounded-pill" title="Limpiar">
                            <i class="bi bi-arrow-counterclockwise"></i>
                        </a>
                    </div>
                </form>
            </div>
        </div>

        <!-- Tabla de Entregas -->
        <div class="card-enterprise">
            <div class="table-responsive">
                <table class="table table-modern align-middle mb-0">
                    <thead>
                        <tr>
                            <th># Pedido</th>
                            <th>Cliente</th>
                            <th>Dirección de Destino</th>
                            <th>Repartidor</th>
                            <th class="text-center">Estado Entrega</th>
                            <th>Tiempos</th>
                            <th class="text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($entregas)): ?>
                            <tr>
                                <td colspan="7" class="text-center py-5 text-muted">
                                    <i class="bi bi-bicycle fs-2 d-block mb-2 text-secondary"></i>
                                    No se encontraron entregas programadas.
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($entregas as $e): ?>
                                <tr>
                                    <td>
                                        <a href="index.php?route=pedidos_detalle&id=<?= $e['pedido_id'] ?>" class="fw-bold text-decoration-none text-dark">
                                            <?= htmlspecialchars($e['numero_pedido']) ?>
                                        </a>
                                        <div><?= FormatHelper::statusBadge($e['pedido_estado']) ?></div>
                                    </td>
                                    <td>
                                        <div class="fw-bold text-dark"><?= htmlspecialchars($e['cliente_nombre']) ?></div>
                                        <div class="text-muted" style="font-size: 11.5px;"><i class="bi bi-telephone me-1"></i><?= htmlspecialchars($e['cliente_telefono'] ?: 'Sin teléfono') ?></div>
                                    </td>
                                    <td style="max-width: 250px;">
                                        <div class="small fw-semibold text-dark text-truncate"><?= htmlspecialchars($e['direccion']) ?></div>
                                        <?php if (!empty($e['referencia'])): ?>
                                            <div class="text-muted" style="font-size: 11px;">Ref: <?= htmlspecialchars($e['referencia']) ?></div>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if (!empty($e['repartidor_nombre'])): ?>
                                            <div class="fw-semibold text-dark small"><i class="bi bi-person-badge me-1"></i><?= htmlspecialchars($e['repartidor_nombre']) ?></div>
                                            <div class="text-muted" style="font-size: 11px;"><?= htmlspecialchars($e['repartidor_telefono'] ?: '') ?></div>
                                        <?php else: ?>
                                            <span class="badge bg-warning-subtle text-warning-emphasis border">Sin asignar</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-center">
                                        <?php 
                                            $stClasses = [
                                                'pendiente' => 'bg-warning-subtle text-warning border-warning-subtle',
                                                'en_camino' => 'bg-primary-subtle text-primary border-primary-subtle',
                                                'entregado' => 'bg-success-subtle text-success border-success-subtle',
                                                'fallido'   => 'bg-danger-subtle text-danger border-danger-subtle'
                                            ];
                                            $stClass = $stClasses[$e['estado']] ?? 'bg-light text-dark';
                                        ?>
                                        <span class="badge <?= $stClass ?> border rounded-pill px-2.5 py-1 text-uppercase" style="font-size: 11px;">
                                            <?= htmlspecialchars(str_replace('_', ' ', $e['estado'])) ?>
                                        </span>
                                    </td>
                                    <td class="small">
                                        <?php if (!empty($e['fecha_salida'])): ?>
                                            <div class="text-muted" style="font-size: 11px;">Salida: <?= FormatHelper::timeOnly($e['fecha_salida']) ?></div>
                                        <?php endif; ?>
                                        <?php if (!empty($e['fecha_entrega'])): ?>
                                            <div class="text-success fw-bold" style="font-size: 11px;">Llegada: <?= FormatHelper::timeOnly($e['fecha_entrega']) ?></div>
                                        <?php endif; ?>
                                        <?php if (empty($e['fecha_salida']) && empty($e['fecha_entrega'])): ?>
                                            <span class="text-muted" style="font-size: 11px;">En espera</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-center">
                                        <div class="btn-group btn-group-sm">
                                            <button type="button" class="btn btn-outline-primary" onclick='openEditDeliveryModal(<?= json_encode($e) ?>)' title="Actualizar Reparto">
                                                <i class="bi bi-pencil"></i>
                                            </button>
                                            <a href="index.php?route=pedidos_detalle&id=<?= $e['pedido_id'] ?>" class="btn btn-outline-secondary" title="Ver Pedido">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                        </div>
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
                    <span class="small text-muted">Mostrando <?= count($entregas) ?> de <?= $total ?> entregas</span>
                    <nav>
                        <ul class="pagination pagination-sm mb-0">
                            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                                <li class="page-item <?= $i == $page ? 'active' : '' ?>">
                                    <a class="page-link" href="index.php?route=entregas&page=<?= $i ?>&search=<?= urlencode($filters['search']) ?>&estado=<?= urlencode($filters['estado']) ?>">
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

<!-- Modal: Editar Entrega -->
<div class="modal fade" id="modalEditarEntrega" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header bg-light">
                <h6 class="modal-title fw-bold text-dark"><i class="bi bi-bicycle text-primary me-2"></i>Actualizar Entrega de Pedido</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="index.php?route=entregas_update" method="POST">
                <?= CsrfHelper::field() ?>
                <input type="hidden" name="id" id="edit_ent_id">

                <div class="modal-body p-3.5">
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Estado del Despacho <span class="text-danger">*</span></label>
                        <select name="estado" id="edit_ent_estado" class="form-select form-select-sm" required>
                            <option value="pendiente">Pendiente (En cocina / espera)</option>
                            <option value="en_camino">En camino (Repartidor en ruta)</option>
                            <option value="entregado">Entregado al cliente (Conforme)</option>
                            <option value="fallido">Fallido (Dirección errónea o ausente)</option>
                        </select>
                    </div>

                    <div class="row g-2 mb-3">
                        <div class="col-7">
                            <label class="form-label small fw-bold">Nombre del Repartidor</label>
                            <input type="text" name="repartidor_nombre" id="edit_ent_repartidor" class="form-control form-control-sm" placeholder="Ej: Mario Benítez">
                        </div>
                        <div class="col-5">
                            <label class="form-label small fw-bold">Teléfono Móvil</label>
                            <input type="text" name="repartidor_telefono" id="edit_ent_telefono" class="form-control form-control-sm" placeholder="991122334">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold">Dirección de Destino <span class="text-danger">*</span></label>
                        <input type="text" name="direccion" id="edit_ent_direccion" class="form-control form-control-sm" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold">Referencia de Llegada</label>
                        <input type="text" name="referencia" id="edit_ent_referencia" class="form-control form-control-sm">
                    </div>

                    <div class="mb-2">
                        <label class="form-label small fw-bold">Observaciones del Despacho</label>
                        <textarea name="observaciones" id="edit_ent_observaciones" class="form-control form-control-sm" rows="2" placeholder="Notas de entrega, propina, incidencias..."></textarea>
                    </div>
                </div>

                <div class="modal-footer p-2.5 bg-light">
                    <button type="button" class="btn btn-secondary btn-sm rounded-pill px-3" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary btn-sm rounded-pill px-4 fw-bold">Guardar Cambios</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function openEditDeliveryModal(e) {
    document.getElementById('edit_ent_id').value = e.id;
    document.getElementById('edit_ent_estado').value = e.estado;
    document.getElementById('edit_ent_repartidor').value = e.repartidor_nombre || '';
    document.getElementById('edit_ent_telefono').value = e.repartidor_telefono || '';
    document.getElementById('edit_ent_direccion').value = e.direccion || '';
    document.getElementById('edit_ent_referencia').value = e.referencia || '';
    document.getElementById('edit_ent_observaciones').value = e.observaciones || '';

    const modal = new bootstrap.Modal(document.getElementById('modalEditarEntrega'));
    modal.show();
}
</script>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
