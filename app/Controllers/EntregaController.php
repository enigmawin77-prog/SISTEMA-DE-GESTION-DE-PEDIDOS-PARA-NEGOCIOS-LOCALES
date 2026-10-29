<?php
namespace App\Controllers;

use App\Helpers\AuthHelper;
use App\Helpers\CsrfHelper;
use App\Helpers\FlashHelper;
use App\Models\Entrega;
use Exception;

class EntregaController {
    public function index(): void {
        AuthHelper::requireAuth();

        $page = max(1, (int)($_GET['page'] ?? 1));
        $limit = 15;
        $offset = ($page - 1) * $limit;

        $filters = [
            'search' => $_GET['search'] ?? '',
            'estado' => $_GET['estado'] ?? ''
        ];

        $entregas = Entrega::getAll($filters, $limit, $offset);
        $total = Entrega::count($filters);
        $totalPages = ceil($total / $limit);

        require __DIR__ . '/../../views/entregas/index.php';
    }

    public function update(): void {
        AuthHelper::requireAuth();
        CsrfHelper::checkOrAbort();

        $id = (int)($_POST['id'] ?? 0);
        $data = [
            'repartidor_nombre'   => $_POST['repartidor_nombre'] ?? null,
            'repartidor_telefono' => $_POST['repartidor_telefono'] ?? null,
            'direccion'           => $_POST['direccion'] ?? '',
            'referencia'          => $_POST['referencia'] ?? null,
            'estado'              => $_POST['estado'] ?? 'pendiente',
            'observaciones'       => $_POST['observaciones'] ?? null
        ];

        try {
            Entrega::updateDelivery($id, $data, AuthHelper::id());
            FlashHelper::set('success', 'Información de la entrega actualizada.');
        } catch (Exception $e) {
            FlashHelper::set('danger', $e->getMessage());
        }

        header("Location: index.php?route=entregas");
        exit;
    }
}
