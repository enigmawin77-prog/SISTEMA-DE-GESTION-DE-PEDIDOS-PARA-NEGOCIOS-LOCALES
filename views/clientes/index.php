<?php
$pageTitle = "Gestión de Clientes";
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
                <h2 class="h4 fw-bold text-dark mb-1">Directorio de Clientes</h2>
                <p class="text-muted small mb-0">Base de datos de clientes, historial de consumo, fidelización y direcciones de entrega.</p>
            </div>
            <div>
                <button type="button" class="btn btn-primary rounded-pill px-3.5 py-2 fw-bold shadow-sm" data-bs-toggle="modal" data-bs-target="#modalNuevoClienteDirectorio">
                    <i class="bi bi-person-plus me-1.5"></i> Nuevo Cliente
                </button>
            </div>
        </div>

        <!-- Filtros -->
        <div class="card-enterprise mb-4">
            <div class="card-body-clean">
                <form action="index.php" method="GET" class="row g-2.5 align-items-end">
                    <input type="hidden" name="route" value="clientes">

                    <div class="col-12 col-md-8">
                        <label class="form-label small fw-bold text-secondary mb-1">Buscar Cliente</label>
                        <div class="input-group input-group-sm">
                            <span class="input-group-text bg-light text-muted border-end-0"><i class="bi bi-search"></i></span>
                            <input type="text" name="search" class="form-control border-start-0 ps-0" placeholder="Nombre, documento DNI/RUC, teléfono o dirección..." value="<?= htmlspecialchars($filters['search'] ?? '') ?>">
                        </div>
                    </div>

                    <div class="col-6 col-md-2">
                        <label class="form-label small fw-bold text-secondary mb-1">Estado</label>
                        <select name="activo" class="form-select form-select-sm">
                            <option value="">Todos</option>
                            <option value="1" <?= ($filters['activo'] ?? '') === '1' ? 'selected' : '' ?>>Activos</option>
                            <option value="0" <?= ($filters['activo'] ?? '') === '0' ? 'selected' : '' ?>>Inactivos</option>
                        </select>
                    </div>

                    <div class="col-6 col-md-2 d-flex gap-2">
                        <button type="submit" class="btn btn-primary btn-sm rounded-pill flex-grow-1 fw-bold">
                            <i class="bi bi-funnel"></i> Filtrar
                        </button>
                        <a href="index.php?route=clientes" class="btn btn-outline-secondary btn-sm rounded-pill" title="Limpiar">
                            <i class="bi bi-arrow-counterclockwise"></i>
                        </a>
                    </div>
                </form>
            </div>
        </div>

        <!-- Tabla de Clientes -->
        <div class="card-enterprise">
            <div class="table-responsive">
                <table class="table table-modern align-middle mb-0">
                    <thead>
                        <tr>
                            <th>Cliente</th>
                            <th>Documento</th>
                            <th>Teléfono</th>
                            <th>Dirección Habitual</th>
                            <th class="text-center">Total Pedidos</th>
                            <th class="text-end">Total Compras</th>
                            <th class="text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($clientes)): ?>
                            <tr>
                                <td colspan="7" class="text-center py-5 text-muted">
                                    <i class="bi bi-people fs-2 d-block mb-2 text-secondary"></i>
                                    No se encontraron clientes registrados.
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($clientes as $cli): ?>
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center gap-2.5">
                                            <div class="rounded-circle bg-primary-subtle text-primary fw-bold d-flex align-items-center justify-content-center" style="width: 38px; height: 38px; font-size: 14px;">
                                                <?= strtoupper(substr($cli['nombre'], 0, 1)) ?>
                                            </div>
                                            <div>
                                                <a href="index.php?route=clientes_detalle&id=<?= $cli['id'] ?>" class="fw-bold text-decoration-none text-dark">
                                                    <?= htmlspecialchars($cli['nombre']) ?>
                                                </a>
                                                <?php if (!empty($cli['email'])): ?>
                                                    <div class="text-muted" style="font-size: 11px;"><?= htmlspecialchars($cli['email']) ?></div>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <?php if (!empty($cli['numero_documento'])): ?>
                                            <span class="badge bg-light text-dark border"><?= $cli['tipo_documento'] ?>: <?= htmlspecialchars($cli['numero_documento']) ?></span>
                                        <?php else: ?>
                                            <span class="text-muted small">-</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <span class="small fw-semibold text-dark"><?= htmlspecialchars($cli['telefono'] ?: '-') ?></span>
                                    </td>
                                    <td class="small text-secondary" style="max-width: 200px;">
                                        <div class="text-truncate"><?= htmlspecialchars($cli['direccion'] ?: 'No especificada') ?></div>
                                    </td>
                                    <td class="text-center fw-bold">
                                        <span class="badge bg-light text-dark border"><?= $cli['total_pedidos'] ?></span>
                                    </td>
                                    <td class="text-end fw-bold text-success fs-6">
                                        <?= FormatHelper::money($cli['total_compras']) ?>
                                    </td>
                                    <td class="text-center">
                                        <div class="btn-group btn-group-sm">
                                            <a href="index.php?route=clientes_detalle&id=<?= $cli['id'] ?>" class="btn btn-outline-secondary" title="Ver Historial">
                                                <i class="bi bi-clock-history"></i>
                                            </a>
                                            <button type="button" class="btn btn-outline-primary" onclick='openEditClientModal(<?= json_encode($cli) ?>)' title="Editar Datos">
                                                <i class="bi bi-pencil"></i>
                                            </button>
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
                    <span class="small text-muted">Mostrando <?= count($clientes) ?> de <?= $total ?> clientes</span>
                    <nav>
                        <ul class="pagination pagination-sm mb-0">
                            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                                <li class="page-item <?= $i == $page ? 'active' : '' ?>">
                                    <a class="page-link" href="index.php?route=clientes&page=<?= $i ?>&search=<?= urlencode($filters['search']) ?>&activo=<?= urlencode($filters['activo']) ?>">
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

<!-- Modal: Nuevo Cliente -->
<div class="modal fade" id="modalNuevoClienteDirectorio" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header bg-light">
                <h6 class="modal-title fw-bold text-dark"><i class="bi bi-person-plus text-primary me-2"></i>Registrar Nuevo Cliente</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="index.php?route=clientes_store" method="POST">
                <?= CsrfHelper::field() ?>
                <div class="modal-body p-3.5">
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Nombre Completo <span class="text-danger">*</span></label>
                        <input type="text" name="nombre" class="form-control form-control-sm" placeholder="Ej: Patricia Morales" required>
                    </div>
                    <div class="row g-2 mb-3">
                        <div class="col-4">
                            <label class="form-label small fw-bold">Tipo Doc.</label>
                            <select name="tipo_documento" class="form-select form-select-sm">
                                <option value="DNI">DNI</option>
                                <option value="RUC">RUC</option>
                                <option value="CE">C.E.</option>
                                <option value="OTRO">Otro</option>
                            </select>
                        </div>
                        <div class="col-8">
                            <label class="form-label small fw-bold">Número de Documento</label>
                            <input type="text" name="numero_documento" class="form-control form-control-sm" placeholder="8 o 11 dígitos">
                        </div>
                    </div>
                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <label class="form-label small fw-bold">Teléfono / WhatsApp</label>
                            <input type="text" name="telefono" class="form-control form-control-sm" placeholder="987654321">
                        </div>
                        <div class="col-6">
                            <label class="form-label small fw-bold">Correo Electrónico</label>
                            <input type="email" name="email" class="form-control form-control-sm" placeholder="cliente@correo.com">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Dirección Habitual de Entrega</label>
                        <input type="text" name="direccion" class="form-control form-control-sm" placeholder="Av. Principal 123, Dpto 201">
                    </div>
                    <div class="mb-2">
                        <label class="form-label small fw-bold">Referencia o Notas</label>
                        <textarea name="referencia" class="form-control form-control-sm" rows="2" placeholder="Frente al parque, portón verde, etc."></textarea>
                    </div>
                </div>
                <div class="modal-footer p-2.5 bg-light">
                    <button type="button" class="btn btn-secondary btn-sm rounded-pill px-3" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary btn-sm rounded-pill px-4 fw-bold">Guardar Cliente</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal: Editar Cliente -->
<div class="modal fade" id="modalEditarCliente" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header bg-light">
                <h6 class="modal-title fw-bold text-dark"><i class="bi bi-pencil-square text-primary me-2"></i>Editar Cliente</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="index.php?route=clientes_update" method="POST">
                <?= CsrfHelper::field() ?>
                <input type="hidden" name="id" id="edit_cli_id">

                <div class="modal-body p-3.5">
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Nombre Completo <span class="text-danger">*</span></label>
                        <input type="text" name="nombre" id="edit_cli_nombre" class="form-control form-control-sm" required>
                    </div>
                    <div class="row g-2 mb-3">
                        <div class="col-4">
                            <label class="form-label small fw-bold">Tipo Doc.</label>
                            <select name="tipo_documento" id="edit_cli_tipo_documento" class="form-select form-select-sm">
                                <option value="DNI">DNI</option>
                                <option value="RUC">RUC</option>
                                <option value="CE">C.E.</option>
                                <option value="OTRO">Otro</option>
                            </select>
                        </div>
                        <div class="col-8">
                            <label class="form-label small fw-bold">Número de Documento</label>
                            <input type="text" name="numero_documento" id="edit_cli_numero_documento" class="form-control form-control-sm">
                        </div>
                    </div>
                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <label class="form-label small fw-bold">Teléfono / WhatsApp</label>
                            <input type="text" name="telefono" id="edit_cli_telefono" class="form-control form-control-sm">
                        </div>
                        <div class="col-6">
                            <label class="form-label small fw-bold">Correo Electrónico</label>
                            <input type="email" name="email" id="edit_cli_email" class="form-control form-control-sm">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Dirección Habitual de Entrega</label>
                        <input type="text" name="direccion" id="edit_cli_direccion" class="form-control form-control-sm">
                    </div>
                    <div class="mb-2">
                        <label class="form-label small fw-bold">Referencia o Notas</label>
                        <textarea name="referencia" id="edit_cli_referencia" class="form-control form-control-sm" rows="2"></textarea>
                    </div>
                </div>
                <div class="modal-footer p-2.5 bg-light">
                    <button type="button" class="btn btn-secondary btn-sm rounded-pill px-3" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary btn-sm rounded-pill px-4 fw-bold">Actualizar Cliente</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function openEditClientModal(c) {
    document.getElementById('edit_cli_id').value = c.id;
    document.getElementById('edit_cli_nombre').value = c.nombre;
    document.getElementById('edit_cli_tipo_documento').value = c.tipo_documento || 'DNI';
    document.getElementById('edit_cli_numero_documento').value = c.numero_documento || '';
    document.getElementById('edit_cli_telefono').value = c.telefono || '';
    document.getElementById('edit_cli_email').value = c.email || '';
    document.getElementById('edit_cli_direccion').value = c.direccion || '';
    document.getElementById('edit_cli_referencia').value = c.referencia || '';

    const modal = new bootstrap.Modal(document.getElementById('modalEditarCliente'));
    modal.show();
}
</script>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
