<?php
namespace App\Controllers;

use App\Helpers\AuthHelper;
use App\Models\Cliente;
use App\Models\Producto;
use App\Services\ReporteService;

class ReporteController {
    public function index(): void {
        AuthHelper::requireAuth();

        $filters = [
            'fecha_desde' => $_GET['fecha_desde'] ?? date('Y-m-01'), // Primer día del mes actual por defecto
            'fecha_hasta' => $_GET['fecha_hasta'] ?? date('Y-m-d'),
            'estado'      => $_GET['estado'] ?? '',
            'cliente_id'  => $_GET['cliente_id'] ?? '',
            'modalidad'   => $_GET['modalidad'] ?? ''
        ];

        $reporte = ReporteService::getSalesReport($filters);
        $topProducts = ReporteService::getTopSellingProducts(8);
        $lowStock = Producto::getLowStock(10);
        $clientes = Cliente::getAll(['activo' => 1], 100, 0);

        require __DIR__ . '/../../views/reportes/index.php';
    }

    public function exportar(): void {
        AuthHelper::requireAuth();

        $filters = [
            'fecha_desde' => $_GET['fecha_desde'] ?? '',
            'fecha_hasta' => $_GET['fecha_hasta'] ?? '',
            'estado'      => $_GET['estado'] ?? '',
            'cliente_id'  => $_GET['cliente_id'] ?? '',
            'modalidad'   => $_GET['modalidad'] ?? ''
        ];

        ReporteService::exportOrdersCsv($filters);
    }
}
