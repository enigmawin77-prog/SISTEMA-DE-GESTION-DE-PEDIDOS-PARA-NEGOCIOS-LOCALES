<?php
$pageTitle = "Gestión de Categorías";
require __DIR__ . '/../layouts/header.php';
require __DIR__ . '/../layouts/sidebar.php';

use App\Helpers\FlashHelper;
use App\Helpers\CsrfHelper;
?>

<main class="app-main">
    <?php require __DIR__ . '/../layouts/navbar.php'; ?>

    <div class="app-content">
        <?= FlashHelper::render() ?>

        <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
            <div>
                <h2 class="h4 fw-bold text-dark mb-1">Categorías de Menú</h2>
                <p class="text-muted small mb-0">Organiza los productos en familias, asigna colores representativos e iconos.</p>
            </div>
            <div>
                <button type="button" class="btn btn-primary rounded-pill px-3.5 py-2 fw-bold shadow-sm" data-bs-toggle="modal" data-bs-target="#modalNuevaCategoria">
                    <i class="bi bi-plus-circle me-1.5"></i> Nueva Categoría
                </button>
            </div>
        </div>

        <div class="row g-3">
            <?php foreach ($categorias as $cat): ?>
                <div class="col-12 col-sm-6 col-lg-4 col-xl-3">
                    <div class="card-enterprise h-100 p-3.5 position-relative">
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <div class="rounded-3 d-flex align-items-center justify-content-center"
                                 style="width: 46px; height: 46px; background-color: <?= htmlspecialchars($cat['color']) ?>18; color: <?= htmlspecialchars($cat['color']) ?>; font-size: 22px;">
                                <i class="bi <?= htmlspecialchars($cat['icono'] ?: 'bi-tag') ?>"></i>
                            </div>
                            <div class="dropdown">
                                <button class="btn btn-light btn-sm rounded-circle p-1" data-bs-toggle="dropdown">
                                    <i class="bi bi-three-dots-vertical text-muted"></i>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0">
                                    <li>
                                        <button class="dropdown-item py-1.5" onclick='openEditCategoryModal(<?= json_encode($cat) ?>)'>
                                            <i class="bi bi-pencil me-2 text-primary"></i> Editar
                                        </button>
                                    </li>
                                    <li>
                                        <form action="index.php?route=categorias_toggle" method="POST">
                                            <?= CsrfHelper::field() ?>
                                            <input type="hidden" name="id" value="<?= $cat['id'] ?>">
                                            <button type="submit" class="dropdown-item py-1.5 <?= $cat['activo'] ? 'text-danger' : 'text-success' ?>">
                                                <i class="bi <?= $cat['activo'] ? 'bi-eye-slash' : 'bi-eye' ?> me-2"></i>
                                                <?= $cat['activo'] ? 'Desactivar' : 'Activar' ?>
                                            </button>
                                        </form>
                                    </li>
                                </ul>
                            </div>
                        </div>

                        <h5 class="fw-bold text-dark mb-1 fs-6"><?= htmlspecialchars($cat['nombre']) ?></h5>
                        <p class="text-muted small mb-3 flex-grow-1" style="min-height: 38px;">
                            <?= htmlspecialchars($cat['descripcion'] ?: 'Sin descripción adicional.') ?>
                        </p>

                        <div class="d-flex align-items-center justify-content-between pt-2 border-top">
                            <span class="badge bg-light text-dark border rounded-pill px-2.5 py-1">
                                <i class="bi bi-box-seam me-1"></i> <?= $cat['total_productos'] ?> productos
                            </span>
                            <span class="badge <?= $cat['activo'] ? 'bg-success-subtle text-success border border-success-subtle' : 'bg-secondary-subtle text-secondary' ?> rounded-pill">
                                <?= $cat['activo'] ? 'Activo' : 'Inactivo' ?>
                            </span>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

    </div>
</main>

<!-- Modal: Nueva Categoría -->
<div class="modal fade" id="modalNuevaCategoria" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header bg-light">
                <h6 class="modal-title fw-bold text-dark"><i class="bi bi-plus-circle text-primary me-2"></i>Nueva Categoría</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="index.php?route=categorias_store" method="POST">
                <?= CsrfHelper::field() ?>
                <div class="modal-body p-3.5">
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Nombre de Categoría <span class="text-danger">*</span></label>
                        <input type="text" name="nombre" class="form-control form-control-sm" placeholder="Ej: Sandwiches y Wraps" required>
                    </div>
                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <label class="form-label small fw-bold">Icono (Bootstrap Icons)</label>
                            <input type="text" name="icono" class="form-control form-control-sm" value="bi-tag" placeholder="bi-egg-fried">
                        </div>
                        <div class="col-6">
                            <label class="form-label small fw-bold">Color Representativo</label>
                            <input type="color" name="color" class="form-control form-control-color w-100" value="#2563eb">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Descripción</label>
                        <textarea name="descripcion" class="form-control form-control-sm" rows="2" placeholder="Breve detalle de los productos agrupados..."></textarea>
                    </div>
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" name="activo" id="chkCatActivo" checked>
                        <label class="form-check-label small fw-bold" for="chkCatActivo">Categoría visible en la carta</label>
                    </div>
                </div>
                <div class="modal-footer p-2.5 bg-light">
                    <button type="button" class="btn btn-secondary btn-sm rounded-pill px-3" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary btn-sm rounded-pill px-4 fw-bold">Guardar Categoría</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal: Editar Categoría -->
<div class="modal fade" id="modalEditarCategoria" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header bg-light">
                <h6 class="modal-title fw-bold text-dark"><i class="bi bi-pencil-square text-primary me-2"></i>Editar Categoría</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="index.php?route=categorias_update" method="POST">
                <?= CsrfHelper::field() ?>
                <input type="hidden" name="id" id="edit_cat_id">
                <div class="modal-body p-3.5">
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Nombre de Categoría <span class="text-danger">*</span></label>
                        <input type="text" name="nombre" id="edit_cat_nombre" class="form-control form-control-sm" required>
                    </div>
                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <label class="form-label small fw-bold">Icono (Bootstrap Icons)</label>
                            <input type="text" name="icono" id="edit_cat_icono" class="form-control form-control-sm">
                        </div>
                        <div class="col-6">
                            <label class="form-label small fw-bold">Color Representativo</label>
                            <input type="color" name="color" id="edit_cat_color" class="form-control form-control-color w-100">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Descripción</label>
                        <textarea name="descripcion" id="edit_cat_descripcion" class="form-control form-control-sm" rows="2"></textarea>
                    </div>
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" name="activo" id="edit_cat_activo">
                        <label class="form-check-label small fw-bold" for="edit_cat_activo">Categoría activa</label>
                    </div>
                </div>
                <div class="modal-footer p-2.5 bg-light">
                    <button type="button" class="btn btn-secondary btn-sm rounded-pill px-3" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary btn-sm rounded-pill px-4 fw-bold">Actualizar Categoría</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function openEditCategoryModal(c) {
    document.getElementById('edit_cat_id').value = c.id;
    document.getElementById('edit_cat_nombre').value = c.nombre;
    document.getElementById('edit_cat_icono').value = c.icono || 'bi-tag';
    document.getElementById('edit_cat_color').value = c.color || '#0d6efd';
    document.getElementById('edit_cat_descripcion').value = c.descripcion || '';
    document.getElementById('edit_cat_activo').checked = c.activo == 1;

    const modal = new bootstrap.Modal(document.getElementById('modalEditarCategoria'));
    modal.show();
}
</script>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
