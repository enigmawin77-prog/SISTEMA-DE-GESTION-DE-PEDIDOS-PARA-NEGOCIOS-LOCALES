<?php
$pageTitle = "Historial del Cliente " . $cliente['nombre'];
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
                <a href="index.php?route=clientes" class="text-secondary small fw-bold text-decoration-none">
                    <i class="bi bi-arrow-left"></i> Volver a Clientes
                </a>
                <h2 class="h4 fw-bold text-dark mb-0 mt-1"><?= htmlspecialchars($cliente['nombre']) ?></h2>
                <p class="text-muted small mb-0">Historial de consumo y pedidos realizados.</p>
            </div>
            <div>
                <a href="index.php?route=pedidos_crear" class="btn btn-primary rounded-pill px-3.5 py-2 fw-bold shadow-sm">
                    <i class="bi bi-cart-plus me-1.5"></i> Crear Pedido para este Cliente
                </a>
            </div>
        </div>

        <div class="row g-4">
            <!-- Tarjeta Resumen Cliente -->
            <div class="col-12 col-xl-4">
                <div class="card-enterprise mb-4">
                    <div class="card-header-clean">
                        <div class="card-title-clean">
                            <i class="bi bi-person-badge-fill text-primary"></i> Información de Contacto
                        </div>
                    </div>
                    <div class="card-body-clean">
                        <div class="mb-3">
                            <label class="small text-muted fw-bold d-block">Documento:</label>
                            <span class="fw-semibold text-dark"><?= $cliente['tipo_documento'] ?> <?= htmlspecialchars($cliente['numero_documento'] ?: 'No registrado') ?></span>
                        </div>
                        <div class="mb-3">
                            <label class="small text-muted fw-bold d-block">Teléfono / WhatsApp:</label>
                            <span class="fw-semibold text-dark"><?= htmlspecialchars($cliente['telefono'] ?: 'No registrado') ?></span>
                        </div>
                        <div class="mb-3">
                            <label class="small text-muted fw-bold d-block">Correo Electrónico:</label>
                            <span class="text-dark"><?= htmlspecialchars($cliente['email'] ?: 'No registrado') ?></span>
                        </div>
                        <div class="mb-3">
                            <label class="small text-muted fw-bold d-block">Dirección de Entrega:</label>
                            <span class="text-dark"><?= htmlspecialchars($cliente['direccion'] ?: 'No registrada') ?></span>
                        </div>
                        <?php if (!empty($cliente['referencia'])): ?>
                            <div class="mb-3">
                                <label class="small text-muted fw-bold d-block">Referencia:</label>
                                <span class="text-secondary small"><?= htmlspecialchars($cliente['referencia']) ?></span>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Métricas de Compra -->
                <div class="card-enterprise">
                    <div class="card-header-clean">
                        <div class="card-title-clean">
                            <i class="bi bi-wallet2 text-success"></i> Indicadores de Consumo
                        </div>
                    </div>
                    <div class="card-body-clean">
                        <div class="d-flex justify-content-between mb-2 pb-2 border-bottom">
                            <span class="text-secondary small">Total pedidos:</span>
                            <span class="fw-bold text-dark fs-6"><?= $cliente['total_pedidos'] ?></span>
                        </div>
                        <div class="d-flex justify-content-between mb-2 pb-2 border-bottom">
                            <span class="text-secondary small">Consumo acumulado:</span>
                            <span class="fw-bold text-success fs-5"><?= FormatHelper::money($cliente['total_compras']) ?></span>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span class="text-secondary small">Ticket promedio:</span>
                            <span class="fw-bold text-primary">
                                <?= FormatHelper::money($cliente['total_pedidos'] > 0 ? ($cliente['total_compras'] / $cliente['total_pedidos']) : 0) ?>
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tabla de Historial de Pedidos -->
            <div class="col-12 col-xl-8">
                <div class="card-enterprise">
                    <div class="card-header-clean">
                        <div class="card-title-clean">
                            <i class="bi bi-receipt-cutoff text-primary"></i> Pedidos Realizados
                        </div>
                        <span class="badge bg-light text-dark border"><?= count($pedidos) ?> registros</span>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-modern align-middle mb-0">
                            <thead>
                                <tr>
                                    <th># Pedido</th>
                                    <th>Fecha</th>
                                    <th>Modalidad</th>
                                    <th>Estado</th>
                                    <th>Pago</th>
                                    <th class="text-end">Total</th>
                                    <th class="text-center">Acción</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($pedidos)): ?>
                                    <tr>
                                        <td colspan="7" class="text-center py-4 text-muted small">Este cliente aún no registra pedidos.</td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($pedidos as $p): ?>
                                        <tr>
                                            <td class="fw-bold"><?= htmlspecialchars($p['numero_pedido']) ?></td>
                                            <td class="small"><?= FormatHelper::date($p['fecha_pedido']) ?></td>
                                            <td><?= FormatHelper::modalityBadge($p['modalidad']) ?></td>
                                            <td><?= FormatHelper::statusBadge($p['estado']) ?></td>
                                            <td><?= FormatHelper::paymentBadge($p['estado_pago']) ?></td>
                                            <td class="text-end fw-bold"><?= FormatHelper::money($p['total']) ?></td>
                                            <td class="text-center">
                                                <a href="index.php?route=pedidos_detalle&id=<?= $p['id'] ?>" class="btn btn-outline-secondary btn-sm" title="Ver Detalle">
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
        </div>

    </div>
</main>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
