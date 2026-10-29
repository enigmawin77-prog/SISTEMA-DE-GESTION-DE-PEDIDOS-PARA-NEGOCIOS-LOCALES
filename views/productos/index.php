<?php
$pageTitle = "Gestión de Productos";
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
                <h2 class="h4 fw-bold text-dark mb-1">Catálogo de Productos</h2>
                <p class="text-muted small mb-0">Administra los productos del negocio, precios de venta, costos e inventario.</p>
            </div>
            <div>
                <button type="button" class="btn btn-primary rounded-pill px-3.5 py-2 fw-bold shadow-sm" data-bs-toggle="modal" data-bs-target="#modalNuevoProducto">
                    <i class="bi bi-plus-circle me-1.5"></i> Nuevo Producto
                </button>
            </div>
        </div>

        <!-- Filtros -->
        <div class="card-enterprise mb-4">
            <div class="card-body-clean">
                <form action="index.php" method="GET" class="row g-2.5 align-items-end">
                    <input type="hidden" name="route" value="productos">

                    <div class="col-12 col-md-4">
                        <label class="form-label small fw-bold text-secondary mb-1">Buscar</label>
                        <div class="input-group input-group-sm">
                            <span class="input-group-text bg-light text-muted border-end-0"><i class="bi bi-search"></i></span>
                            <input type="text" name="search" class="form-control border-start-0 ps-0" placeholder="Nombre o código del producto..." value="<?= htmlspecialchars($filters['search'] ?? '') ?>">
                        </div>
                    </div>

                    <div class="col-6 col-md-3">
                        <label class="form-label small fw-bold text-secondary mb-1">Categoría</label>
                        <select name="categoria_id" class="form-select form-select-sm">
                            <option value="">Todas las categorías</option>
                            <?php foreach ($categorias as $cat): ?>
                                <option value="<?= $cat['id'] ?>" <?= ($filters['categoria_id'] ?? '') == $cat['id'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($cat['nombre']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="col-6 col-md-2">
                        <label class="form-label small fw-bold text-secondary mb-1">Estado</label>
                        <select name="activo" class="form-select form-select-sm">
                            <option value="">Todos</option>
                            <option value="1" <?= ($filters['activo'] ?? '') === '1' ? 'selected' : '' ?>>Activos</option>
                            <option value="0" <?= ($filters['activo'] ?? '') === '0' ? 'selected' : '' ?>>Inactivos</option>
                        </select>
                    </div>

                    <div class="col-12 col-md-3 d-flex gap-2">
                        <button type="submit" class="btn btn-primary btn-sm rounded-pill flex-grow-1 fw-bold">
                            <i class="bi bi-funnel"></i> Filtrar
                        </button>
                        <a href="index.php?route=productos" class="btn btn-outline-secondary btn-sm rounded-pill" title="Limpiar">
                            <i class="bi bi-arrow-counterclockwise"></i>
                        </a>
                    </div>
                </form>
            </div>
        </div>

        <!-- Tabla de Productos -->
        <div class="card-enterprise">
            <div class="table-responsive">
                <table class="table table-modern align-middle mb-0">
                    <thead>
                        <tr>
                            <th>Producto</th>
                            <th>Categoría</th>
                            <th class="text-end">Precio Venta</th>
                            <th class="text-end">Costo</th>
                            <th class="text-center">Stock</th>
                            <th class="text-center">Estado</th>
                            <th class="text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($productos)): ?>
                            <tr>
                                <td colspan="7" class="text-center py-5 text-muted">
                                    <i class="bi bi-box fs-2 d-block mb-2 text-secondary"></i>
                                    No se encontraron productos registrados.
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($productos as $prod): ?>
                                <?php 
                                    $pImg = 'public/uploads/productos/' . ($prod['imagen'] ?: 'burger.jpg');
                                    if (!file_exists($pImg)) $pImg = 'public/uploads/productos/burger.jpg';
                                    $isLow = $prod['stock'] <= $prod['stock_minimo'];
                                ?>
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center gap-2.5">
                                            <img src="<?= $pImg ?>" class="rounded-3 shadow-2xs" style="width: 44px; height: 44px; object-fit: cover;" onerror="this.src='public/uploads/productos/burger.jpg'">
                                            <div>
                                                <div class="fw-bold text-dark"><?= htmlspecialchars($prod['nombre']) ?></div>
                                                <div class="text-muted" style="font-size: 11px;">Cód: <span class="badge bg-light text-secondary border"><?= htmlspecialchars($prod['codigo']) ?></span></div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge" style="background-color: <?= htmlspecialchars($prod['categoria_color'] ?: '#0d6efd') ?>20; color: <?= htmlspecialchars($prod['categoria_color'] ?: '#0d6efd') ?>; font-weight: 600;">
                                            <?= htmlspecialchars($prod['categoria_nombre']) ?>
                                        </span>
                                    </td>
                                    <td class="text-end fw-bold text-dark fs-6"><?= FormatHelper::money($prod['precio_venta']) ?></td>
                                    <td class="text-end text-muted small"><?= FormatHelper::money($prod['costo_compra']) ?></td>
                                    <td class="text-center">
                                        <span class="badge <?= $prod['stock'] == 0 ? 'bg-danger' : ($isLow ? 'bg-warning text-dark' : 'bg-success') ?> rounded-pill px-2.5 py-1">
                                            <?= $prod['stock'] ?> uds
                                        </span>
                                        <?php if ($isLow && $prod['stock'] > 0): ?>
                                            <div class="text-warning-emphasis fw-bold mt-0.5" style="font-size: 10px;">¡Stock bajo!</div>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-center">
                                        <?php if ($prod['activo']): ?>
                                            <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill">Activo</span>
                                        <?php else: ?>
                                            <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle rounded-pill">Inactivo</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-center">
                                        <div class="btn-group btn-group-sm">
                                            <button type="button" class="btn btn-outline-secondary" onclick='openEditProductModal(<?= json_encode($prod) ?>)' title="Editar">
                                                <i class="bi bi-pencil"></i>
                                            </button>
                                            <form action="index.php?route=productos_toggle" method="POST" class="d-inline">
                                                <?= CsrfHelper::field() ?>
                                                <input type="hidden" name="id" value="<?= $prod['id'] ?>">
                                                <button type="submit" class="btn <?= $prod['activo'] ? 'btn-outline-danger' : 'btn-outline-success' ?>" title="<?= $prod['activo'] ? 'Desactivar' : 'Activar' ?>">
                                                    <i class="bi <?= $prod['activo'] ? 'bi-eye-slash' : 'bi-check-lg' ?>"></i>
                                                </button>
                                            </form>
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
                    <span class="small text-muted">Mostrando <?= count($productos) ?> de <?= $total ?> productos</span>
                    <nav>
                        <ul class="pagination pagination-sm mb-0">
                            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                                <li class="page-item <?= $i == $page ? 'active' : '' ?>">
                                    <a class="page-link" href="index.php?route=productos&page=<?= $i ?>&search=<?= urlencode($filters['search']) ?>&categoria_id=<?= urlencode($filters['categoria_id']) ?>&activo=<?= urlencode($filters['activo']) ?>">
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

<!-- Modal: Nuevo Producto -->
<div class="modal fade" id="modalNuevoProducto" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header bg-light">
                <h6 class="modal-title fw-bold text-dark"><i class="bi bi-plus-circle text-primary me-2"></i>Registrar Nuevo Producto</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="index.php?route=productos_store" method="POST" enctype="multipart/form-data">
                <?= CsrfHelper::field() ?>
                <div class="modal-body p-4">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label small fw-bold">Código Único <span class="text-danger">*</span></label>
                            <input type="text" name="codigo" class="form-control form-control-sm text-uppercase" placeholder="Ej: HAM-010" required>
                        </div>
                        <div class="col-md-8">
                            <label class="form-label small fw-bold">Nombre del Producto <span class="text-danger">*</span></label>
                            <input type="text" name="nombre" class="form-control form-control-sm" placeholder="Ej: Hamburguesa BBQ Crispy" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Categoría <span class="text-danger">*</span></label>
                            <select name="categoria_id" class="form-select form-select-sm" required>
                                <option value="">Seleccione una categoría...</option>
                                <?php foreach ($categorias as $cat): ?>
                                    <option value="<?= $cat['id'] ?>"><?= htmlspecialchars($cat['nombre']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small fw-bold">Precio de Venta <span class="text-danger">*</span></label>
                            <div class="input-group input-group-sm">
                                <span class="input-group-text bg-light">S/</span>
                                <input type="number" step="0.10" min="0.10" name="precio_venta" class="form-control form-control-sm text-end" placeholder="0.00" required>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small fw-bold">Costo de Compra</label>
                            <div class="input-group input-group-sm">
                                <span class="input-group-text bg-light">S/</span>
                                <input type="number" step="0.10" min="0" name="costo_compra" class="form-control form-control-sm text-end" placeholder="0.00">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small fw-bold">Stock Actual <span class="text-danger">*</span></label>
                            <input type="number" min="0" name="stock" class="form-control form-control-sm text-center" value="10" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small fw-bold">Stock Mínimo</label>
                            <input type="number" min="1" name="stock_minimo" class="form-control form-control-sm text-center" value="5">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Imagen del Producto (JPG, PNG, WEBP)</label>
                            <input type="file" name="imagen" class="form-control form-control-sm" accept="image/jpeg,image/png,image/webp">
                        </div>
                        <div class="col-12">
                            <label class="form-label small fw-bold">Descripción</label>
                            <textarea name="descripcion" class="form-control form-control-sm" rows="2" placeholder="Ingredientes, presentación o detalles del producto..."></textarea>
                        </div>
                        <div class="col-12">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="activo" id="chkNuevoActivo" checked>
                                <label class="form-check-label small fw-bold" for="chkNuevoActivo">Producto activo para la venta</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer p-3 bg-light">
                    <button type="button" class="btn btn-secondary btn-sm rounded-pill px-3" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary btn-sm rounded-pill px-4 fw-bold">Guardar Producto</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal: Editar Producto -->
<div class="modal fade" id="modalEditarProducto" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header bg-light">
                <h6 class="modal-title fw-bold text-dark"><i class="bi bi-pencil-square text-primary me-2"></i>Editar Producto</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="index.php?route=productos_update" method="POST" enctype="multipart/form-data">
                <?= CsrfHelper::field() ?>
                <input type="hidden" name="id" id="edit_id">

                <div class="modal-body p-4">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label small fw-bold">Código Único <span class="text-danger">*</span></label>
                            <input type="text" name="codigo" id="edit_codigo" class="form-control form-control-sm text-uppercase" required>
                        </div>
                        <div class="col-md-8">
                            <label class="form-label small fw-bold">Nombre del Producto <span class="text-danger">*</span></label>
                            <input type="text" name="nombre" id="edit_nombre" class="form-control form-control-sm" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Categoría <span class="text-danger">*</span></label>
                            <select name="categoria_id" id="edit_categoria_id" class="form-select form-select-sm" required>
                                <?php foreach ($categorias as $cat): ?>
                                    <option value="<?= $cat['id'] ?>"><?= htmlspecialchars($cat['nombre']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small fw-bold">Precio de Venta <span class="text-danger">*</span></label>
                            <div class="input-group input-group-sm">
                                <span class="input-group-text bg-light">S/</span>
                                <input type="number" step="0.10" min="0.10" name="precio_venta" id="edit_precio_venta" class="form-control form-control-sm text-end" required>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small fw-bold">Costo de Compra</label>
                            <div class="input-group input-group-sm">
                                <span class="input-group-text bg-light">S/</span>
                                <input type="number" step="0.10" min="0" name="costo_compra" id="edit_costo_compra" class="form-control form-control-sm text-end">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small fw-bold">Stock Actual <span class="text-danger">*</span></label>
                            <input type="number" min="0" name="stock" id="edit_stock" class="form-control form-control-sm text-center" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small fw-bold">Stock Mínimo</label>
                            <input type="number" min="1" name="stock_minimo" id="edit_stock_minimo" class="form-control form-control-sm text-center">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Reemplazar Imagen</label>
                            <input type="file" name="imagen" class="form-control form-control-sm" accept="image/jpeg,image/png,image/webp">
                        </div>
                        <div class="col-12">
                            <label class="form-label small fw-bold">Descripción</label>
                            <textarea name="descripcion" id="edit_descripcion" class="form-control form-control-sm" rows="2"></textarea>
                        </div>
                        <div class="col-12">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="activo" id="edit_activo">
                                <label class="form-check-label small fw-bold" for="edit_activo">Producto activo para la venta</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer p-3 bg-light">
                    <button type="button" class="btn btn-secondary btn-sm rounded-pill px-3" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary btn-sm rounded-pill px-4 fw-bold">Actualizar Producto</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function openEditProductModal(p) {
    document.getElementById('edit_id').value = p.id;
    document.getElementById('edit_codigo').value = p.codigo;
    document.getElementById('edit_nombre').value = p.nombre;
    document.getElementById('edit_categoria_id').value = p.categoria_id;
    document.getElementById('edit_precio_venta').value = p.precio_venta;
    document.getElementById('edit_costo_compra').value = p.costo_compra;
    document.getElementById('edit_stock').value = p.stock;
    document.getElementById('edit_stock_minimo').value = p.stock_minimo;
    document.getElementById('edit_descripcion').value = p.descripcion || '';
    document.getElementById('edit_activo').checked = p.activo == 1;

    const modal = new bootstrap.Modal(document.getElementById('modalEditarProducto'));
    modal.show();
}
</script>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
