<?php
$pageTitle = "Configuración del Negocio";
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
                <h2 class="h4 fw-bold text-dark mb-1">Configuración del Negocio</h2>
                <p class="text-muted small mb-0">Datos generales de la empresa, identidad de marca, moneda y comprobantes internos.</p>
            </div>
        </div>

        <div class="row justify-content-center">
            <div class="col-12 col-xl-9">
                <div class="card-enterprise">
                    <div class="card-header-clean">
                        <div class="card-title-clean">
                            <i class="bi bi-shop-window text-primary"></i> Datos de la Tienda / Restaurante
                        </div>
                    </div>
                    <form action="index.php?route=configuracion" method="POST">
                        <?= CsrfHelper::field() ?>

                        <div class="card-body-clean p-4">
                            <div class="row g-3 mb-3">
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold">Nombre Comercial <span class="text-danger">*</span></label>
                                    <input type="text" name="nombre_comercial" class="form-control form-control-sm" value="<?= htmlspecialchars($configuracion['nombre_comercial']) ?>" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold">Razón Social</label>
                                    <input type="text" name="razon_social" class="form-control form-control-sm" value="<?= htmlspecialchars($configuracion['razon_social']) ?>">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label small fw-bold">RUC (Registro Único de Contribuyente)</label>
                                    <input type="text" name="ruc" class="form-control form-control-sm" value="<?= htmlspecialchars($configuracion['ruc']) ?>">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label small fw-bold">Teléfono de Atención <span class="text-danger">*</span></label>
                                    <input type="text" name="telefono" class="form-control form-control-sm" value="<?= htmlspecialchars($configuracion['telefono']) ?>" required>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label small fw-bold">Correo Electrónico <span class="text-danger">*</span></label>
                                    <input type="email" name="email" class="form-control form-control-sm" value="<?= htmlspecialchars($configuracion['email']) ?>" required>
                                </div>
                                <div class="col-12">
                                    <label class="form-label small fw-bold">Dirección del Local Principal <span class="text-danger">*</span></label>
                                    <input type="text" name="direccion" class="form-control form-control-sm" value="<?= htmlspecialchars($configuracion['direccion']) ?>" required>
                                </div>
                            </div>

                            <hr class="my-3">

                            <h6 class="fw-bold text-dark mb-3"><i class="bi bi-cash-coin text-success me-2"></i>Moneda y Parámetros Operativos</h6>
                            <div class="row g-3 mb-3">
                                <div class="col-md-4">
                                    <label class="form-label small fw-bold">Símbolo de Moneda</label>
                                    <input type="text" name="moneda_simbolo" class="form-control form-control-sm" value="<?= htmlspecialchars($configuracion['moneda_simbolo']) ?>" required>
                                    <span class="text-muted" style="font-size: 11px;">Ejemplo: S/ (Soles Peruanos)</span>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label small fw-bold">Código ISO Moneda</label>
                                    <input type="text" name="moneda_codigo" class="form-control form-control-sm" value="<?= htmlspecialchars($configuracion['moneda_codigo']) ?>" required>
                                    <span class="text-muted" style="font-size: 11px;">Ejemplo: PEN</span>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label small fw-bold">Costo de Envío Predeterminado</label>
                                    <div class="input-group input-group-sm">
                                        <span class="input-group-text bg-light">S/</span>
                                        <input type="number" step="0.50" min="0" name="costo_envio_predeterminado" class="form-control form-control-sm" value="<?= number_format($configuracion['costo_envio_predeterminado'], 2, '.', '') ?>" required>
                                    </div>
                                </div>
                            </div>

                            <hr class="my-3">

                            <h6 class="fw-bold text-dark mb-3"><i class="bi bi-receipt text-primary me-2"></i>Comprobantes Internos de Venta</h6>
                            <div class="mb-3">
                                <label class="form-label small fw-bold">Mensaje al Pie del Ticket</label>
                                <textarea name="mensaje_ticket" class="form-control form-control-sm" rows="3"><?= htmlspecialchars($configuracion['mensaje_ticket']) ?></textarea>
                                <span class="text-muted" style="font-size: 11px;">
                                    * Nota: Este comprobante es un control interno operativo y no constituye un comprobante electrónico tributario regulado por SUNAT.
                                </span>
                            </div>
                        </div>

                        <div class="card-footer bg-light p-3 text-end">
                            <button type="submit" class="btn btn-primary rounded-pill px-4 fw-bold shadow-sm">
                                <i class="bi bi-check-circle me-1.5"></i> Guardar Configuración
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    </div>
</main>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
