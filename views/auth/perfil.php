<?php
$pageTitle = "Mi Perfil";
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
                <h2 class="h4 fw-bold text-dark mb-1">Mi Perfil</h2>
                <p class="text-muted small mb-0">Administra tus datos personales y credenciales de acceso al sistema.</p>
            </div>
        </div>

        <div class="row justify-content-center">
            <div class="col-12 col-md-8 col-xl-6">
                <div class="card-enterprise">
                    <div class="card-header-clean">
                        <div class="card-title-clean">
                            <i class="bi bi-person-circle text-primary"></i> Datos de la Cuenta
                        </div>
                        <span class="badge bg-primary text-uppercase"><?= htmlspecialchars($user['rol']) ?></span>
                    </div>
                    <form action="index.php?route=perfil" method="POST">
                        <?= CsrfHelper::field() ?>

                        <div class="card-body-clean p-4">
                            <div class="text-center mb-4">
                                <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center fw-bold fs-3 mx-auto mb-2" style="width: 72px; height: 72px;">
                                    <?= strtoupper(substr($user['nombre'] ?? 'U', 0, 1)) ?>
                                </div>
                                <h5 class="fw-bold mb-0 text-dark"><?= htmlspecialchars($user['nombre']) ?></h5>
                                <p class="text-muted small mb-0"><?= htmlspecialchars($user['email']) ?></p>
                            </div>

                            <div class="mb-3">
                                <label class="form-label small fw-bold">Nombre Completo <span class="text-danger">*</span></label>
                                <input type="text" name="nombre" class="form-control form-control-sm" value="<?= htmlspecialchars($user['nombre']) ?>" required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label small fw-bold">Correo Electrónico <span class="text-danger">*</span></label>
                                <input type="email" name="email" class="form-control form-control-sm" value="<?= htmlspecialchars($user['email']) ?>" required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label small fw-bold">Teléfono de Contacto</label>
                                <input type="text" name="telefono" class="form-control form-control-sm" value="<?= htmlspecialchars($user['telefono'] ?? '') ?>" placeholder="987654321">
                            </div>

                            <hr class="my-4">

                            <h6 class="fw-bold text-dark mb-2"><i class="bi bi-shield-lock text-warning me-2"></i>Cambiar Contraseña</h6>
                            <p class="text-muted small mb-3">Deje este campo vacío si no desea modificar su contraseña actual.</p>

                            <div class="mb-3">
                                <label class="form-label small fw-bold">Nueva Contraseña</label>
                                <input type="password" name="password" class="form-control form-control-sm" placeholder="Mínimo 6 caracteres">
                            </div>
                        </div>

                        <div class="card-footer bg-light p-3 text-end">
                            <button type="submit" class="btn btn-primary rounded-pill px-4 fw-bold shadow-sm">
                                <i class="bi bi-save me-1.5"></i> Actualizar Perfil
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    </div>
</main>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
