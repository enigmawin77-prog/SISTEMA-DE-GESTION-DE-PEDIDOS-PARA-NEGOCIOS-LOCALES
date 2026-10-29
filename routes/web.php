<?php
/**
 * Definición de Rutas del Sistema Web
 */

use App\Controllers\AuthController;
use App\Controllers\DashboardController;
use App\Controllers\ProductoController;
use App\Controllers\CategoriaController;
use App\Controllers\ClienteController;
use App\Controllers\PedidoController;
use App\Controllers\PagoController;
use App\Controllers\EntregaController;
use App\Controllers\ReporteController;
use App\Controllers\ConfiguracionController;
use App\Controllers\PortalController;

return function (string $route, string $method) {
    switch ($route) {
        // Autenticación
        case 'login':
            $c = new AuthController();
            $method === 'POST' ? $c->login() : $c->showLogin();
            break;
        case 'logout':
            (new AuthController())->logout();
            break;
        case 'perfil':
            $c = new AuthController();
            $method === 'POST' ? $c->updatePerfil() : $c->perfil();
            break;

        // Dashboard
        case '':
        case 'dashboard':
            (new DashboardController())->index();
            break;
        case 'api_dashboard_charts':
            (new DashboardController())->chartData();
            break;

        // Productos
        case 'productos':
            (new ProductoController())->index();
            break;
        case 'productos_store':
            (new ProductoController())->store();
            break;
        case 'productos_update':
            (new ProductoController())->update();
            break;
        case 'productos_toggle':
            (new ProductoController())->toggle();
            break;
        case 'api_producto_json':
            (new ProductoController())->getJson();
            break;

        // Categorías
        case 'categorias':
            (new CategoriaController())->index();
            break;
        case 'categorias_store':
            (new CategoriaController())->store();
            break;
        case 'categorias_update':
            (new CategoriaController())->update();
            break;
        case 'categorias_toggle':
            (new CategoriaController())->toggle();
            break;

        // Clientes
        case 'clientes':
            (new ClienteController())->index();
            break;
        case 'clientes_detalle':
            (new ClienteController())->detalle();
            break;
        case 'clientes_store':
            (new ClienteController())->store();
            break;
        case 'clientes_update':
            (new ClienteController())->update();
            break;
        case 'api_clientes_search':
            (new ClienteController())->searchJson();
            break;

        // Pedidos
        case 'pedidos':
            (new PedidoController())->index();
            break;
        case 'pedidos_crear':
            (new PedidoController())->nuevo();
            break;
        case 'pedidos_store':
            (new PedidoController())->store();
            break;
        case 'pedidos_detalle':
            (new PedidoController())->detalle();
            break;
        case 'pedidos_cambiar_estado':
            (new PedidoController())->cambiarEstado();
            break;
        case 'pedidos_comprobante':
            (new PedidoController())->comprobante();
            break;

        // Pagos
        case 'pagos':
            (new PagoController())->index();
            break;
        case 'pagos_store':
            (new PagoController())->store();
            break;

        // Entregas
        case 'entregas':
            (new EntregaController())->index();
            break;
        case 'entregas_update':
            (new EntregaController())->update();
            break;

        // Reportes
        case 'reportes':
            (new ReporteController())->index();
            break;
        case 'reportes_exportar':
            (new ReporteController())->exportar();
            break;

        // Configuración
        case 'configuracion':
            $c = new ConfiguracionController();
            $method === 'POST' ? $c->update() : $c->index();
            break;

        // =============================================
        // PORTAL CLIENTE
        // =============================================
        case 'portal_menu':
        case 'portal':
            (new PortalController())->menu();
            break;
        case 'portal_login':
            $c = new PortalController();
            $method === 'POST' ? $c->login() : $c->showLogin();
            break;
        case 'portal_registro':
            $c = new PortalController();
            $method === 'POST' ? $c->registro() : $c->showRegistro();
            break;
        case 'portal_logout':
            (new PortalController())->logout();
            break;
        case 'portal_carrito_agregar':
            (new PortalController())->carritoAgregar();
            break;
        case 'portal_carrito_actualizar':
            (new PortalController())->carritoActualizar();
            break;
        case 'portal_carrito_eliminar':
            (new PortalController())->carritoEliminar();
            break;
        case 'portal_carrito_json':
            (new PortalController())->carritoJson();
            break;
        case 'portal_checkout':
            $c = new PortalController();
            $method === 'POST' ? $c->checkoutStore() : $c->checkout();
            break;
        case 'portal_checkout_store':
            (new PortalController())->checkoutStore();
            break;
        case 'portal_mis_pedidos':
            (new PortalController())->misPedidos();
            break;
        case 'portal_pedido_detalle':
            (new PortalController())->pedidoDetalle();
            break;

        default:
            http_response_code(404);
            require __DIR__ . '/../views/errors/404.php';
            break;
    }
};
