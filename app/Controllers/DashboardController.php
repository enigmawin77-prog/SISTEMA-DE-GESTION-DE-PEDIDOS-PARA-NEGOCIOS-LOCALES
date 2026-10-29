<?php
namespace App\Controllers;

use App\Helpers\AuthHelper;
use App\Models\Pedido;
use App\Models\Producto;
use App\Models\Configuracion;
use App\Services\ReporteService;

class DashboardController {
    public function index(): void {
        AuthHelper::requireAuth();

        $metrics = ReporteService::getDashboardMetrics();
        $recentOrders = Pedido::getAll([], 10, 0);
        $topProducts = ReporteService::getTopSellingProducts(5);
        $chartSales = ReporteService::getSalesLast7Days();
        $configuracion = Configuracion::get();
        $lowStockProducts = Producto::getLowStock(5);

        require __DIR__ . '/../../views/dashboard/index.php';
    }

    public function chartData(): void {
        AuthHelper::requireAuth();
        header('Content-Type: application/json');

        $chartSales = ReporteService::getSalesLast7Days();
        $metrics = ReporteService::getDashboardMetrics();

        echo json_encode([
            'sales' => $chartSales,
            'status' => [
                'labels' => ['Pendiente', 'Confirmado', 'En preparación', 'Listo para entregar', 'En camino', 'Entregado', 'Cancelado'],
                'counts' => [
                    $metrics['pendientes'],
                    $metrics['confirmados'],
                    $metrics['en_preparacion'],
                    $metrics['listos'],
                    $metrics['en_camino'],
                    $metrics['entregados'],
                    $metrics['cancelados']
                ]
            ]
        ]);
        exit;
    }
}
