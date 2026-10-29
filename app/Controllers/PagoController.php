<?php
namespace App\Controllers;

use App\Helpers\AuthHelper;
use App\Helpers\CsrfHelper;
use App\Helpers\FlashHelper;
use App\Models\Pago;
use App\Models\Pedido;
use Exception;

class PagoController {
    public function index(): void {
        AuthHelper::requireAuth();

        $page = max(1, (int)($_GET['page'] ?? 1));
        $limit = 15;
        $offset = ($page - 1) * $limit;

        $filters = [
            'search'      => $_GET['search'] ?? '',
            'metodo_pago' => $_GET['metodo_pago'] ?? '',
            'fecha_desde' => $_GET['fecha_desde'] ?? '',
            'fecha_hasta' => $_GET['fecha_hasta'] ?? ''
        ];

        $pagos = Pago::getAll($filters, $limit, $offset);
        $total = Pago::count($filters);
        $totalPages = ceil($total / $limit);

        require __DIR__ . '/../../views/pagos/index.php';
    }

    public function store(): void {
        AuthHelper::requireAuth();
        CsrfHelper::checkOrAbort();

        $pedidoId = (int)($_POST['pedido_id'] ?? 0);
        $monto = (float)($_POST['monto'] ?? 0);
        $metodo = $_POST['metodo_pago'] ?? 'efectivo';
        $referencia = $_POST['referencia'] ?? null;
        $notas = $_POST['notas'] ?? null;
        $redirect = $_POST['redirect'] ?? 'pedidos_detalle';

        try {
            Pago::registerPayment($pedidoId, AuthHelper::id(), $monto, $metodo, $referencia, $notas);
            FlashHelper::set('success', 'Pago registrado exitosamente.');
        } catch (Exception $e) {
            FlashHelper::set('danger', $e->getMessage());
        }

        if ($redirect === 'pagos') {
            header("Location: index.php?route=pagos");
        } else {
            header("Location: index.php?route=pedidos_detalle&id=" . $pedidoId);
        }
        exit;
    }
}
