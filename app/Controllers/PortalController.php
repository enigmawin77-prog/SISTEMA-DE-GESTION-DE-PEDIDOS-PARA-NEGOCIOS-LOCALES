<?php
namespace App\Controllers;

use App\Helpers\PortalAuthHelper;
use App\Helpers\Database;
use PDO;

/**
 * PortalController — Portal público del cliente
 * Rutas: portal_menu, portal_login, portal_registro, portal_logout,
 *        portal_carrito_*, portal_checkout, portal_mis_pedidos, portal_pedido_detalle
 */
class PortalController {

    private function db(): \PDO {
        return Database::getConnection();
    }

    /* ====================================================
     * MENÚ PÚBLICO
     * ==================================================== */
    public function menu(): void {
        PortalAuthHelper::init();
        $db = $this->db();

        $categorias = $db->query(
            "SELECT id, nombre, icono, color FROM categorias WHERE activo=1 ORDER BY nombre ASC"
        )->fetchAll();

        $productos = $db->query(
            "SELECT p.id, p.nombre, p.descripcion, p.precio_venta, p.imagen, p.stock,
                    c.nombre AS categoria_nombre, c.id AS categoria_id
             FROM productos p
             JOIN categorias c ON c.id = p.categoria_id
             WHERE p.activo = 1 AND p.stock > 0
             ORDER BY c.nombre ASC, p.nombre ASC"
        )->fetchAll();

        // Config negocio
        $conf = $db->query("SELECT * FROM configuracion_negocio LIMIT 1")->fetch();

        require __DIR__ . '/../../views/portal/menu.php';
    }

    /* ====================================================
     * CARRITO — API JSON
     * ==================================================== */
    public function carritoAgregar(): void {
        PortalAuthHelper::init();
        header('Content-Type: application/json');
        $data   = json_decode(file_get_contents('php://input'), true) ?? [];
        $pid    = (int)($data['producto_id'] ?? 0);
        $cant   = max(1, (int)($data['cantidad'] ?? 1));

        if ($pid <= 0) { echo json_encode(['ok'=>false,'msg'=>'Producto inválido']); exit; }

        // Validar existencia y stock
        $prod = $this->db()->prepare("SELECT id,nombre,precio_venta,imagen,stock FROM productos WHERE id=? AND activo=1");
        $prod->execute([$pid]);
        $p = $prod->fetch();

        if (!$p) { echo json_encode(['ok'=>false,'msg'=>'Producto no encontrado']); exit; }

        $carritoActual = PortalAuthHelper::carritoGet()[$pid]['cantidad'] ?? 0;
        if ($carritoActual + $cant > $p['stock']) {
            echo json_encode(['ok'=>false,'msg'=>'Stock insuficiente. Disponible: '.$p['stock']]);
            exit;
        }

        PortalAuthHelper::carritoAgregar($pid, $cant, (float)$p['precio_venta'], $p['nombre'], $p['imagen'] ?? '');
        echo json_encode([
            'ok'    => true,
            'count' => PortalAuthHelper::carritoCount(),
            'total' => number_format(PortalAuthHelper::carritoTotal(), 2),
        ]);
        exit;
    }

    public function carritoActualizar(): void {
        PortalAuthHelper::init();
        header('Content-Type: application/json');
        $data = json_decode(file_get_contents('php://input'), true) ?? [];
        $pid  = (int)($data['producto_id'] ?? 0);
        $cant = (int)($data['cantidad'] ?? 0);
        PortalAuthHelper::carritoActualizar($pid, $cant);
        echo json_encode([
            'ok'    => true,
            'count' => PortalAuthHelper::carritoCount(),
            'total' => number_format(PortalAuthHelper::carritoTotal(), 2),
        ]);
        exit;
    }

    public function carritoEliminar(): void {
        PortalAuthHelper::init();
        header('Content-Type: application/json');
        $data = json_decode(file_get_contents('php://input'), true) ?? [];
        $pid  = (int)($data['producto_id'] ?? 0);
        PortalAuthHelper::carritoEliminar($pid);
        echo json_encode([
            'ok'    => true,
            'count' => PortalAuthHelper::carritoCount(),
            'total' => number_format(PortalAuthHelper::carritoTotal(), 2),
        ]);
        exit;
    }

    public function carritoJson(): void {
        PortalAuthHelper::init();
        header('Content-Type: application/json');
        echo json_encode([
            'items' => array_values(PortalAuthHelper::carritoGet()),
            'count' => PortalAuthHelper::carritoCount(),
            'total' => number_format(PortalAuthHelper::carritoTotal(), 2),
        ]);
        exit;
    }

    /* ====================================================
     * CHECKOUT
     * ==================================================== */
    public function checkout(): void {
        PortalAuthHelper::requireAuth();
        $carrito = PortalAuthHelper::carritoGet();
        if (empty($carrito)) {
            header('Location: index.php?route=portal_menu');
            exit;
        }
        $conf = $this->db()->query("SELECT * FROM configuracion_negocio LIMIT 1")->fetch();
        $cliente = $this->db()->prepare("SELECT * FROM clientes WHERE id=?");
        $cliente->execute([PortalAuthHelper::id()]);
        $cliente = $cliente->fetch();
        require __DIR__ . '/../../views/portal/checkout.php';
    }

    public function checkoutStore(): void {
        PortalAuthHelper::requireAuth();
        $carrito = PortalAuthHelper::carritoGet();
        if (empty($carrito)) {
            header('Location: index.php?route=portal_menu');
            exit;
        }

        $db         = $this->db();
        $clienteId  = PortalAuthHelper::id();
        $modalidad  = in_array($_POST['modalidad'] ?? '', ['recojo','domicilio']) ? $_POST['modalidad'] : 'domicilio';
        $notas      = htmlspecialchars(trim($_POST['notas'] ?? ''));
        $direccion  = htmlspecialchars(trim($_POST['direccion_entrega'] ?? ''));

        // Obtener config
        $conf       = $db->query("SELECT * FROM configuracion_negocio LIMIT 1")->fetch();
        $costoEnvio = $modalidad === 'domicilio' ? (float)($conf['costo_envio_predeterminado'] ?? 0) : 0;

        // Calcular totales
        $subtotal = PortalAuthHelper::carritoTotal();
        $total    = $subtotal + $costoEnvio;

        // Número de pedido único
        $numPedido = 'PED-' . strtoupper(substr(uniqid(), -6));

        // Obtener usuario sistema (admin por defecto para pedidos del portal)
        $usuarioSistema = $db->query("SELECT id FROM usuarios WHERE rol='admin' LIMIT 1")->fetchColumn() ?: 1;

        $db->beginTransaction();
        try {
            $stmt = $db->prepare("
                INSERT INTO pedidos
                    (numero_pedido, cliente_id, usuario_id, modalidad, estado, subtotal, descuento,
                     costo_envio, total, notas, estado_pago, monto_pagado, saldo_pendiente,
                     direccion_entrega, fecha_pedido)
                VALUES (?,?,?,?,'pendiente',?,0,?,?,?,'pendiente',0,?,?,NOW())
            ");
            $stmt->execute([
                $numPedido, $clienteId, $usuarioSistema, $modalidad,
                $subtotal, $costoEnvio, $total, $notas,
                $total, $direccion
            ]);
            $pedidoId = (int)$db->lastInsertId();

            // Insertar items
            $stmtItem = $db->prepare("
                INSERT INTO detalle_pedidos (pedido_id, producto_id, cantidad, precio_unitario, subtotal)
                VALUES (?,?,?,?,?)
            ");
            foreach ($carrito as $item) {
                $stmtItem->execute([
                    $pedidoId,
                    $item['producto_id'],
                    $item['cantidad'],
                    $item['precio'],
                    $item['subtotal'],
                ]);
                // Descontar stock
                $db->prepare("UPDATE productos SET stock = stock - ? WHERE id = ? AND stock >= ?")
                   ->execute([$item['cantidad'], $item['producto_id'], $item['cantidad']]);
            }

            // Historial — columna 'motivo' (no 'notas')
            $db->prepare("
                INSERT INTO historial_estados_pedido (pedido_id, estado_anterior, estado_nuevo, usuario_id, motivo)
                VALUES (?, NULL, 'pendiente', ?, 'Pedido realizado desde el portal del cliente')
            ")->execute([$pedidoId, $usuarioSistema]);

            $db->commit();
            PortalAuthHelper::carritoLimpiar();
            header("Location: index.php?route=portal_pedido_detalle&id=$pedidoId&nuevo=1");
            exit;
        } catch (\Exception $e) {
            $db->rollBack();
            error_log('[PortalCheckout] ' . $e->getMessage());
            $_SESSION['portal_error'] = 'Error al procesar tu pedido: ' . htmlspecialchars($e->getMessage());
            header('Location: index.php?route=portal_checkout');
            exit;
        }
    }

    /* ====================================================
     * MIS PEDIDOS
     * ==================================================== */
    public function misPedidos(): void {
        PortalAuthHelper::requireAuth();
        $db = $this->db();
        $stmt = $db->prepare("
            SELECT p.id, p.numero_pedido, p.estado, p.total, p.modalidad, p.fecha_pedido, p.estado_pago
            FROM pedidos p
            WHERE p.cliente_id = ?
            ORDER BY p.fecha_pedido DESC
            LIMIT 50
        ");
        $stmt->execute([PortalAuthHelper::id()]);
        $pedidos = $stmt->fetchAll();
        $conf = $db->query("SELECT * FROM configuracion_negocio LIMIT 1")->fetch();
        require __DIR__ . '/../../views/portal/mis_pedidos.php';
    }

    public function pedidoDetalle(): void {
        PortalAuthHelper::requireAuth();
        $db  = $this->db();
        $pid = (int)($_GET['id'] ?? 0);
        $nuevo = isset($_GET['nuevo']);

        $stmt = $db->prepare("
            SELECT p.*, c.nombre AS cliente_nombre, c.telefono AS cliente_telefono
            FROM pedidos p
            JOIN clientes c ON c.id = p.cliente_id
            WHERE p.id = ? AND p.cliente_id = ?
        ");
        $stmt->execute([$pid, PortalAuthHelper::id()]);
        $pedido = $stmt->fetch();

        if (!$pedido) {
            header('Location: index.php?route=portal_mis_pedidos');
            exit;
        }

        $items = $db->prepare("
            SELECT dp.*, pr.nombre AS producto_nombre, pr.imagen
            FROM detalle_pedidos dp
            JOIN productos pr ON pr.id = dp.producto_id
            WHERE dp.pedido_id = ?
        ");
        $items->execute([$pid]);
        $items = $items->fetchAll();

        $conf = $db->query("SELECT * FROM configuracion_negocio LIMIT 1")->fetch();
        require __DIR__ . '/../../views/portal/pedido_detalle.php';
    }

    /* ====================================================
     * AUTH CLIENTE
     * ==================================================== */
    public function showLogin(): void {
        PortalAuthHelper::init();
        if (PortalAuthHelper::check()) {
            header('Location: index.php?route=portal_menu');
            exit;
        }
        require __DIR__ . '/../../views/portal/login.php';
    }

    public function login(): void {
        PortalAuthHelper::init();
        $email    = trim($_POST['email'] ?? '');
        $password = trim($_POST['password'] ?? '');

        if (!$email || !$password) {
            $_SESSION['portal_error'] = 'Por favor completa todos los campos.';
            header('Location: index.php?route=portal_login');
            exit;
        }

        $stmt = $this->db()->prepare(
            "SELECT id, nombre, email, password_hash, activo FROM clientes WHERE email = ? LIMIT 1"
        );
        $stmt->execute([$email]);
        $cliente = $stmt->fetch();

        if (!$cliente || !$cliente['password_hash'] || !password_verify($password, $cliente['password_hash'])) {
            $_SESSION['portal_error'] = 'Correo o contraseña incorrectos.';
            header('Location: index.php?route=portal_login');
            exit;
        }

        if (!$cliente['activo']) {
            $_SESSION['portal_error'] = 'Tu cuenta está desactivada. Contacta al negocio.';
            header('Location: index.php?route=portal_login');
            exit;
        }

        PortalAuthHelper::login($cliente);
        $_SESSION['portal_success'] = '¡Bienvenido(a) de nuevo, ' . $cliente['nombre'] . '!';
        header('Location: index.php?route=portal_menu');
        exit;
    }

    public function showRegistro(): void {
        PortalAuthHelper::init();
        if (PortalAuthHelper::check()) {
            header('Location: index.php?route=portal_menu');
            exit;
        }
        require __DIR__ . '/../../views/portal/registro.php';
    }

    public function registro(): void {
        PortalAuthHelper::init();
        $nombre   = htmlspecialchars(trim($_POST['nombre'] ?? ''));
        $email    = trim($_POST['email'] ?? '');
        $telefono = trim($_POST['telefono'] ?? '');
        $password = trim($_POST['password'] ?? '');
        $password2 = trim($_POST['password2'] ?? '');
        $direccion = htmlspecialchars(trim($_POST['direccion'] ?? ''));

        if (!$nombre || !$email || !$password) {
            $_SESSION['portal_error'] = 'Completa los campos requeridos.';
            header('Location: index.php?route=portal_registro');
            exit;
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $_SESSION['portal_error'] = 'El correo electrónico no es válido.';
            header('Location: index.php?route=portal_registro');
            exit;
        }
        if (strlen($password) < 6) {
            $_SESSION['portal_error'] = 'La contraseña debe tener al menos 6 caracteres.';
            header('Location: index.php?route=portal_registro');
            exit;
        }
        if ($password !== $password2) {
            $_SESSION['portal_error'] = 'Las contraseñas no coinciden.';
            header('Location: index.php?route=portal_registro');
            exit;
        }

        $db = $this->db();
        // Verificar email único
        $exists = $db->prepare("SELECT id FROM clientes WHERE email = ? LIMIT 1");
        $exists->execute([$email]);
        if ($exists->fetch()) {
            $_SESSION['portal_error'] = 'Ya existe una cuenta con ese correo. <a href="index.php?route=portal_login">Inicia sesión</a>.';
            header('Location: index.php?route=portal_registro');
            exit;
        }

        $stmt = $db->prepare("
            INSERT INTO clientes (nombre, email, telefono, direccion, password_hash, activo)
            VALUES (?,?,?,?,?,1)
        ");
        $stmt->execute([
            $nombre, $email, $telefono, $direccion,
            password_hash($password, PASSWORD_BCRYPT)
        ]);
        $clienteId = (int)$db->lastInsertId();

        $cliente = ['id' => $clienteId, 'nombre' => $nombre, 'email' => $email];
        PortalAuthHelper::login($cliente);
        $_SESSION['portal_success'] = '¡Cuenta creada con éxito! Bienvenido(a), ' . $nombre . '.';
        header('Location: index.php?route=portal_menu');
        exit;
    }

    public function logout(): void {
        PortalAuthHelper::init();
        PortalAuthHelper::logout();
        header('Location: index.php?route=portal_menu');
        exit;
    }
}
