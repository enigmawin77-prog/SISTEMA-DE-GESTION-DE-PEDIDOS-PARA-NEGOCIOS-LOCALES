<?php
namespace App\Controllers;

use App\Helpers\AuthHelper;
use App\Helpers\CsrfHelper;
use App\Helpers\FlashHelper;
use App\Models\Pedido;
use App\Models\Producto;
use App\Models\Categoria;
use App\Models\Cliente;
use App\Models\Configuracion;
use Exception;

class PedidoController {
    public function index(): void {
        AuthHelper::requireAuth();

        $page = max(1, (int)($_GET['page'] ?? 1));
        $limit = 15;
        $offset = ($page - 1) * $limit;

        $filters = [
            'search'      => $_GET['search'] ?? '',
            'estado'      => $_GET['estado'] ?? '',
            'modalidad'   => $_GET['modalidad'] ?? '',
            'estado_pago' => $_GET['estado_pago'] ?? '',
            'fecha_desde' => $_GET['fecha_desde'] ?? '',
            'fecha_hasta' => $_GET['fecha_hasta'] ?? ''
        ];

        $pedidos = Pedido::getAll($filters, $limit, $offset);
        $total = Pedido::count($filters);
        $totalPages = ceil($total / $limit);

        require __DIR__ . '/../../views/pedidos/index.php';
    }

    public function nuevo(): void {
        AuthHelper::requireAuth();

        $productos = Producto::getActivosParaVenta();
        $categorias = Categoria::getAll(true);
        $clientes = Cliente::getAll(['activo' => 1], 100, 0);
        $configuracion = Configuracion::get();
        $proximoNumero = Pedido::generateNextNumero();

        require __DIR__ . '/../../views/pedidos/crear.php';
    }

    public function store(): void {
        AuthHelper::requireAuth();
        CsrfHelper::checkOrAbort();

        $clienteId = (int)($_POST['cliente_id'] ?? 0);
        $modalidad = $_POST['modalidad'] ?? 'domicilio';
        $descuento = (float)($_POST['descuento'] ?? 0.00);
        $costoEnvio = (float)($_POST['costo_envio'] ?? 0.00);
        $direccionEntrega = $_POST['direccion_entrega'] ?? '';
        $notas = $_POST['notas'] ?? '';
        $estadoInicial = $_POST['estado'] ?? 'pendiente';

        $itemsJson = $_POST['items_json'] ?? '[]';
        $items = json_decode($itemsJson, true);

        if (!is_array($items) || empty($items)) {
            FlashHelper::set('danger', 'Debe agregar al menos un producto a la orden.');
            header("Location: index.php?route=pedidos_crear");
            exit;
        }

        if ($clienteId <= 0) {
            FlashHelper::set('danger', 'Debe seleccionar un cliente.');
            header("Location: index.php?route=pedidos_crear");
            exit;
        }

        // Datos del pago inicial si se proporcionó
        $pagoInicial = null;
        if (!empty($_POST['pago_monto']) && (float)$_POST['pago_monto'] > 0) {
            $pagoInicial = [
                'monto'       => (float)$_POST['pago_monto'],
                'metodo_pago' => $_POST['pago_metodo'] ?? 'efectivo',
                'referencia'  => $_POST['pago_referencia'] ?? null
            ];
        }

        // Datos de entrega
        $deliveryData = null;
        if ($modalidad === 'domicilio') {
            $deliveryData = [
                'repartidor_nombre'   => $_POST['repartidor_nombre'] ?? null,
                'repartidor_telefono' => $_POST['repartidor_telefono'] ?? null,
                'referencia'          => $_POST['entrega_referencia'] ?? null
            ];
        }

        $orderData = [
            'cliente_id'        => $clienteId,
            'usuario_id'        => AuthHelper::id(),
            'modalidad'         => $modalidad,
            'estado'            => $estadoInicial,
            'descuento'         => $descuento,
            'costo_envio'       => $costoEnvio,
            'notas'             => $notas,
            'direccion_entrega' => $direccionEntrega
        ];

        try {
            $pedidoId = Pedido::createOrder($orderData, $items, $pagoInicial, $deliveryData);
            FlashHelper::set('success', '¡Pedido registrado exitosamente!');
            header("Location: index.php?route=pedidos_detalle&id=" . $pedidoId);
            exit;
        } catch (Exception $e) {
            FlashHelper::set('danger', 'Error al procesar el pedido: ' . $e->getMessage());
            header("Location: index.php?route=pedidos_crear");
            exit;
        }
    }

    public function detalle(): void {
        AuthHelper::requireAuth();

        $id = (int)($_GET['id'] ?? 0);
        $pedido = Pedido::findById($id);

        if (!$pedido) {
            FlashHelper::set('danger', 'El pedido solicitado no existe.');
            header("Location: index.php?route=pedidos");
            exit;
        }

        $detalles = Pedido::getDetalles($id);
        $pagos = Pedido::getPagos($id);
        $historial = Pedido::getHistorial($id);
        $configuracion = Configuracion::get();

        require __DIR__ . '/../../views/pedidos/detalle.php';
    }

    public function cambiarEstado(): void {
        AuthHelper::requireAuth();
        CsrfHelper::checkOrAbort();

        $id = (int)($_POST['pedido_id'] ?? 0);
        $nuevoEstado = $_POST['nuevo_estado'] ?? '';
        $motivo = $_POST['motivo'] ?? '';

        $allowedStates = ['pendiente', 'confirmado', 'en_preparacion', 'listo', 'en_camino', 'entregado', 'cancelado'];

        if (!in_array($nuevoEstado, $allowedStates, true)) {
            FlashHelper::set('danger', 'Estado no reconocido.');
            header("Location: index.php?route=pedidos_detalle&id=" . $id);
            exit;
        }

        try {
            Pedido::changeStatus($id, $nuevoEstado, AuthHelper::id(), $motivo);
            FlashHelper::set('success', 'El estado del pedido ha sido actualizado correctamente.');
        } catch (Exception $e) {
            FlashHelper::set('danger', $e->getMessage());
        }

        header("Location: index.php?route=pedidos_detalle&id=" . $id);
        exit;
    }

    public function comprobante(): void {
        AuthHelper::requireAuth();

        $id = (int)($_GET['id'] ?? 0);
        $pedido = Pedido::findById($id);

        if (!$pedido) {
            die("Pedido no encontrado.");
        }

        $detalles = Pedido::getDetalles($id);
        $pagos = Pedido::getPagos($id);
        $configuracion = Configuracion::get();

        require __DIR__ . '/../../views/pedidos/comprobante.php';
    }
}
