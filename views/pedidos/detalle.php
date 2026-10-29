<?php
$pageTitle = "Detalle de Pedido " . $pedido['numero_pedido'];
require __DIR__ . '/../layouts/header.php';
require __DIR__ . '/../layouts/sidebar.php';

use App\Helpers\FormatHelper;
use App\Helpers\FlashHelper;
use App\Helpers\CsrfHelper;

$isFinalized = in_array($pedido['estado'], ['entregado', 'cancelado']);
?>

<main class="app-main">
    <?php require __DIR__ . '/../layouts/navbar.php'; ?>

    <div class="app-content">
        <?= FlashHelper::render() ?>

        <!-- Encabezado de la Orden -->
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
            <div>
                <a href="index.php?route=pedidos" class="text-secondary small fw-bold text-decoration-none">
                    <i class="bi bi-arrow-left"></i> Volver al listado
                </a>
                <div class="d-flex align-items-center gap-3 mt-1">
                    <h2 class="h4 fw-bold text-dark mb-0"><?= htmlspecialchars($pedido['numero_pedido']) ?></h2>
                    <div><?= FormatHelper::statusBadge($pedido['estado']) ?></div>
                    <div><?= FormatHelper::paymentBadge($pedido['estado_pago']) ?></div>
                </div>
                <p class="text-muted small mb-0 mt-1">
                    Registrado el <?= FormatHelper::date($pedido['fecha_pedido']) ?> por <strong class="text-dark"><?= htmlspecialchars($pedido['usuario_nombre']) ?></strong>
                </p>
            </div>
            <div class="d-flex gap-2">
                <a href="index.php?route=pedidos_comprobante&id=<?= $pedido['id'] ?>" target="_blank" class="btn btn-outline-dark rounded-pill px-3 py-1.5 fw-bold shadow-2xs">
                    <i class="bi bi-printer me-1"></i> Imprimir Comprobante
                </a>
                <?php if (!$isFinalized && (float)$pedido['saldo_pendiente'] > 0): ?>
                    <button type="button" class="btn btn-success rounded-pill px-3 py-1.5 fw-bold shadow-2xs text-white" data-bs-toggle="modal" data-bs-target="#modalRegistrarPago">
                        <i class="bi bi-cash-stack me-1"></i> Registrar Pago
                    </button>
                <?php endif; ?>
            </div>
        </div>

        <div class="row g-4">
            <!-- ============================================================
                 COLUMNA IZQUIERDA: DETALLES DE ITEMS Y PRODUCTOS
            ============================================================ -->
            <div class="col-12 col-xl-8">
                <!-- Tabla de Productos del Pedido -->
                <div class="card-enterprise mb-4">
                    <div class="card-header-clean">
                        <div class="card-title-clean">
                            <i class="bi bi-basket3-fill text-primary"></i> Productos Solicitados
                        </div>
                        <span class="badge bg-light text-dark border"><?= count($detalles) ?> items</span>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-modern align-middle mb-0">
                            <thead>
                                <tr>
                                    <th>Producto</th>
                                    <th class="text-center">Cant.</th>
                                    <th class="text-end">Precio Unit.</th>
                                    <th class="text-end">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($detalles as $it): ?>
                                    <?php 
                                        $itImg = 'public/uploads/productos/' . ($it['producto_imagen'] ?: 'burger.jpg');
                                        if (!file_exists($itImg)) $itImg = 'public/uploads/productos/burger.jpg';
                                    ?>
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center gap-2.5">
                                                <img src="<?= $itImg ?>" class="rounded-2" style="width: 42px; height: 42px; object-fit: cover;" onerror="this.src='public/uploads/productos/burger.jpg'">
                                                <div>
                                                    <div class="fw-bold text-dark"><?= htmlspecialchars($it['producto_nombre']) ?></div>
                                                    <div class="text-muted" style="font-size: 11.5px;">Cód: <?= htmlspecialchars($it['producto_codigo']) ?></div>
                                                    <?php if (!empty($it['notas'])): ?>
                                                        <div class="badge bg-light text-secondary border mt-1" style="font-size: 11px;">
                                                            <i class="bi bi-pencil-square me-1"></i><?= htmlspecialchars($it['notas']) ?>
                                                        </div>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="text-center fw-bold fs-6"><?= $it['cantidad'] ?></td>
                                        <td class="text-end text-muted"><?= FormatHelper::money($it['precio_unitario']) ?></td>
                                        <td class="text-end fw-bold text-dark"><?= FormatHelper::money($it['subtotal']) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="3" class="text-end text-muted small fw-bold">Subtotal:</td>
                                    <td class="text-end fw-bold"><?= FormatHelper::money($pedido['subtotal']) ?></td>
                                </tr>
                                <?php if ((float)$pedido['descuento'] > 0): ?>
                                <tr>
                                    <td colspan="3" class="text-end text-success small fw-bold">Descuento aplicado:</td>
                                    <td class="text-end text-success fw-bold">- <?= FormatHelper::money($pedido['descuento']) ?></td>
                                </tr>
                                <?php endif; ?>
                                <?php if ((float)$pedido['costo_envio'] > 0): ?>
                                <tr>
                                    <td colspan="3" class="text-end text-muted small fw-bold">Costo de envío:</td>
                                    <td class="text-end fw-bold">+ <?= FormatHelper::money($pedido['costo_envio']) ?></td>
                                </tr>
                                <?php endif; ?>
                                <tr class="bg-light">
                                    <td colspan="3" class="text-end fw-bold fs-6 text-dark">TOTAL GENERAL:</td>
                                    <td class="text-end fw-bold fs-5 text-primary"><?= FormatHelper::money($pedido['total']) ?></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>

                <!-- Historial de Pagos -->
                <div class="card-enterprise mb-4">
                    <div class="card-header-clean">
                        <div class="card-title-clean">
                            <i class="bi bi-credit-card-2-front-fill text-success"></i> Pagos Registrados
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            <span class="small text-muted">Pagado: <strong><?= FormatHelper::money($pedido['monto_pagado']) ?></strong></span>
                            <?php if ((float)$pedido['saldo_pendiente'] > 0): ?>
                                <span class="badge bg-danger-subtle text-danger border border-danger-subtle">
                                    Pendiente: <?= FormatHelper::money($pedido['saldo_pendiente']) ?>
                                </span>
                            <?php else: ?>
                                <span class="badge bg-success-subtle text-success border border-success-subtle">Totalmente cancelado</span>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-modern align-middle mb-0">
                            <thead>
                                <tr>
                                    <th>Fecha</th>
                                    <th>Método</th>
                                    <th>Referencia</th>
                                    <th>Monto</th>
                                    <th>Atendido por</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($pagos)): ?>
                                    <tr>
                                        <td colspan="5" class="text-center py-3 text-muted small">No se han registrado pagos para este pedido.</td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($pagos as $pago): ?>
                                        <tr>
                                            <td class="small"><?= FormatHelper::date($pago['fecha_pago']) ?></td>
                                            <td>
                                                <span class="badge bg-light text-dark border text-uppercase">
                                                    <i class="bi bi-wallet2 me-1"></i><?= htmlspecialchars($pago['metodo_pago']) ?>
                                                </span>
                                            </td>
                                            <td class="small text-muted"><?= htmlspecialchars($pago['referencia'] ?: 'Sin referencia') ?></td>
                                            <td class="fw-bold text-success"><?= FormatHelper::money($pago['monto']) ?></td>
                                            <td class="small text-secondary"><?= htmlspecialchars($pago['usuario_nombre']) ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Línea de Tiempo de Estados -->
                <div class="card-enterprise">
                    <div class="card-header-clean">
                        <div class="card-title-clean">
                            <i class="bi bi-clock-history text-secondary"></i> Historial de Cambios de Estado
                        </div>
                    </div>
                    <div class="card-body-clean">
                        <ul class="timeline list-unstyled position-relative ms-2 ps-3 mb-0" style="border-left: 2px solid #e2e8f0;">
                            <?php foreach ($historial as $h): ?>
                                <li class="position-relative mb-3">
                                    <div class="position-absolute rounded-circle bg-primary" style="width: 10px; height: 10px; left: -18px; top: 5px;"></div>
                                    <div class="d-flex align-items-center gap-2 mb-1">
                                        <?= FormatHelper::statusBadge($h['estado_nuevo']) ?>
                                        <span class="small text-muted"><?= FormatHelper::date($h['created_at']) ?></span>
                                    </div>
                                    <p class="small text-dark mb-0">
                                        <?= htmlspecialchars($h['motivo'] ?: 'Actualización de estado') ?>
                                        <span class="text-muted">(por <?= htmlspecialchars($h['usuario_nombre']) ?>)</span>
                                    </p>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- ============================================================
                 COLUMNA DERECHA: CLIENTE, ENTREGA Y GESTIÓN DE ESTADO
            ============================================================ -->
            <div class="col-12 col-xl-4">
                <!-- Tarjeta: Datos del Cliente -->
                <div class="card-enterprise mb-4">
                    <div class="card-header-clean">
                        <div class="card-title-clean">
                            <i class="bi bi-person-fill text-primary"></i> Cliente
                        </div>
                        <a href="index.php?route=clientes_detalle&id=<?= $pedido['cliente_id'] ?>" class="small text-primary text-decoration-none fw-bold">
                            Ver historial
                        </a>
                    </div>
                    <div class="card-body-clean">
                        <h6 class="fw-bold text-dark mb-1"><?= htmlspecialchars($pedido['cliente_nombre']) ?></h6>
                        <?php if (!empty($pedido['cliente_documento'])): ?>
                            <p class="small text-muted mb-1"><?= $pedido['cliente_tipo_documento'] ?>: <?= htmlspecialchars($pedido['cliente_documento']) ?></p>
                        <?php endif; ?>
                        <p class="small text-secondary mb-1">
                            <i class="bi bi-telephone me-1"></i><?= htmlspecialchars($pedido['cliente_telefono'] ?: 'No registrado') ?>
                        </p>
                        <?php if (!empty($pedido['cliente_email'])): ?>
                            <p class="small text-secondary mb-1">
                                <i class="bi bi-envelope me-1"></i><?= htmlspecialchars($pedido['cliente_email']) ?>
                            </p>
                        <?php endif; ?>
                        <?php if (!empty($pedido['cliente_direccion'])): ?>
                            <p class="small text-secondary mb-0">
                                <i class="bi bi-geo-alt me-1"></i><?= htmlspecialchars($pedido['cliente_direccion']) ?>
                            </p>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Tarjeta: Modalidad y Entrega -->
                <div class="card-enterprise mb-4">
                    <div class="card-header-clean">
                        <div class="card-title-clean">
                            <i class="bi bi-truck text-primary"></i> Datos de Atención
                        </div>
                        <div><?= FormatHelper::modalityBadge($pedido['modalidad']) ?></div>
                    </div>
                    <div class="card-body-clean">
                        <?php if ($pedido['modalidad'] === 'domicilio'): ?>
                            <div class="mb-2">
                                <label class="small text-muted fw-bold d-block">Dirección de Despacho:</label>
                                <span class="small text-dark fw-semibold"><?= htmlspecialchars($pedido['entrega_direccion'] ?: $pedido['direccion_entrega']) ?></span>
                            </div>
                            <?php if (!empty($pedido['entrega_referencia'])): ?>
                                <div class="mb-2">
                                    <label class="small text-muted fw-bold d-block">Referencia:</label>
                                    <span class="small text-secondary"><?= htmlspecialchars($pedido['entrega_referencia']) ?></span>
                                </div>
                            <?php endif; ?>
                            <div class="mb-2">
                                <label class="small text-muted fw-bold d-block">Repartidor Asignado:</label>
                                <span class="small text-dark"><?= htmlspecialchars($pedido['repartidor_nombre'] ?: 'Pendiente de asignación') ?></span>
                                <?php if (!empty($pedido['repartidor_telefono'])): ?>
                                    <span class="text-muted">(<?= htmlspecialchars($pedido['repartidor_telefono']) ?>)</span>
                                <?php endif; ?>
                            </div>
                            <?php if (!empty($pedido['entrega_salida'])): ?>
                                <div class="small text-muted">
                                    <i class="bi bi-arrow-right-circle me-1"></i>Salida: <?= FormatHelper::timeOnly($pedido['entrega_salida']) ?>
                                </div>
                            <?php endif; ?>
                            <?php if (!empty($pedido['entrega_fin'])): ?>
                                <div class="small text-success">
                                    <i class="bi bi-check-all me-1"></i>Entregado: <?= FormatHelper::timeOnly($pedido['entrega_fin']) ?>
                                </div>
                            <?php endif; ?>
                        <?php else: ?>
                            <div class="alert alert-light border small text-secondary mb-0">
                                <i class="bi bi-shop me-1"></i> El cliente retirará su orden directamente en tienda.
                            </div>
                        <?php endif; ?>

                        <?php if (!empty($pedido['notas'])): ?>
                            <hr class="my-2.5">
                            <label class="small text-muted fw-bold d-block mb-1">Notas del Pedido:</label>
                            <p class="small text-dark bg-light p-2 rounded border mb-0"><?= nl2br(htmlspecialchars($pedido['notas'])) ?></p>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Tarjeta: Control de Estado del Pedido -->
                <div class="card-enterprise">
                    <div class="card-header-clean">
                        <div class="card-title-clean">
                            <i class="bi bi-sliders text-primary"></i> Cambiar Estado
                        </div>
                    </div>
                    <div class="card-body-clean">
                        <?php if ($isFinalized): ?>
                            <div class="alert <?= $pedido['estado'] === 'entregado' ? 'alert-success' : 'alert-danger' ?> small mb-0">
                                <i class="bi <?= $pedido['estado'] === 'entregado' ? 'bi-check-circle-fill' : 'bi-x-octagon-fill' ?> me-1"></i>
                                Este pedido está <strong><?= strtoupper($pedido['estado']) ?></strong> y su ciclo ha finalizado.
                            </div>
                        <?php else: ?>
                            <form action="index.php?route=pedidos_cambiar_estado" method="POST">
                                <?= CsrfHelper::field() ?>
                                <input type="hidden" name="pedido_id" value="<?= $pedido['id'] ?>">

                                <div class="mb-3">
                                    <label class="form-label small fw-bold text-dark">Nuevo Estado</label>
                                    <select name="nuevo_estado" class="form-select form-select-sm" required>
                                        <option value="confirmado" <?= $pedido['estado'] === 'confirmado' ? 'selected' : '' ?>>Confirmado</option>
                                        <option value="en_preparacion" <?= $pedido['estado'] === 'en_preparacion' ? 'selected' : '' ?>>En preparación</option>
                                        <option value="listo" <?= $pedido['estado'] === 'listo' ? 'selected' : '' ?>>Listo para entregar</option>
                                        <option value="en_camino" <?= $pedido['estado'] === 'en_camino' ? 'selected' : '' ?>>En camino</option>
                                        <option value="entregado">Entregado (Finalizar)</option>
                                        <option value="cancelado" class="text-danger fw-bold">Cancelar Pedido (Restaura Stock)</option>
                                    </select>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label small fw-bold text-dark">Observación / Motivo</label>
                                    <input type="text" name="motivo" class="form-control form-control-sm" placeholder="Ej: Comanda entregada al motorizado">
                                </div>

                                <button type="submit" class="btn btn-primary btn-sm w-100 rounded-pill fw-bold">
                                    Actualizar Estado
                                </button>
                            </form>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

    </div>
</main>

<!-- Modal: Registrar Pago -->
<div class="modal fade" id="modalRegistrarPago" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header bg-light">
                <h6 class="modal-title fw-bold text-dark"><i class="bi bi-cash-stack text-success me-2"></i>Registrar Pago</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="index.php?route=pagos_store" method="POST">
                <?= CsrfHelper::field() ?>
                <input type="hidden" name="pedido_id" value="<?= $pedido['id'] ?>">
                <input type="hidden" name="redirect" value="pedidos_detalle">

                <div class="modal-body">
                    <div class="mb-2 text-center p-2 bg-light rounded-3">
                        <span class="small text-muted d-block">Saldo pendiente:</span>
                        <strong class="fs-5 text-danger"><?= FormatHelper::money($pedido['saldo_pendiente']) ?></strong>
                    </div>

                    <div class="mb-2.5">
                        <label class="form-label small fw-bold">Monto a Pagar <span class="text-danger">*</span></label>
                        <div class="input-group input-group-sm">
                            <span class="input-group-text bg-light">S/</span>
                            <input type="number" step="0.10" min="0.10" max="<?= $pedido['saldo_pendiente'] ?>" name="monto" class="form-control form-control-sm text-end" value="<?= number_format($pedido['saldo_pendiente'], 2, '.', '') ?>" required>
                        </div>
                    </div>

                    <div class="mb-2.5">
                        <label class="form-label small fw-bold">Método de Pago <span class="text-danger">*</span></label>
                        <select name="metodo_pago" class="form-select form-select-sm" required>
                            <option value="efectivo">Efectivo</option>
                            <option value="yape" selected>Yape</option>
                            <option value="plin">Plin</option>
                            <option value="transferencia">Transferencia Bancaria</option>
                            <option value="tarjeta">Tarjeta Débito/Crédito</option>
                            <option value="otro">Otro</option>
                        </select>
                    </div>

                    <div class="mb-2.5">
                        <label class="form-label small fw-bold">N° Referencia / Operación</label>
                        <input type="text" name="referencia" class="form-control form-control-sm" placeholder="Ej: YAPE-98210">
                    </div>

                    <div class="mb-2">
                        <label class="form-label small fw-bold">Notas de Pago</label>
                        <input type="text" name="notas" class="form-control form-control-sm" placeholder="Ej: Pago total recibido">
                    </div>
                </div>

                <div class="modal-footer p-2 bg-light">
                    <button type="button" class="btn btn-secondary btn-sm rounded-pill px-3" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-success text-white btn-sm rounded-pill px-4 fw-bold">Guardar Pago</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
