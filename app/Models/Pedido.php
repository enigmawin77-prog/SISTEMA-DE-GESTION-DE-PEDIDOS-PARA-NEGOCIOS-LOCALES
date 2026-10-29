<?php
namespace App\Models;

use App\Helpers\Database;
use PDO;
use Exception;

class Pedido {
    public static function getAll(array $filters = [], int $limit = 50, int $offset = 0): array {
        $db = Database::getConnection();
        $where = ["1=1"];
        $params = [];

        if (!empty($filters['search'])) {
            $where[] = "(p.numero_pedido LIKE :search OR c.nombre LIKE :search OR c.telefono LIKE :search)";
            $params[':search'] = '%' . trim($filters['search']) . '%';
        }

        if (!empty($filters['estado'])) {
            $where[] = "p.estado = :estado";
            $params[':estado'] = $filters['estado'];
        }

        if (!empty($filters['modalidad'])) {
            $where[] = "p.modalidad = :modalidad";
            $params[':modalidad'] = $filters['modalidad'];
        }

        if (!empty($filters['estado_pago'])) {
            $where[] = "p.estado_pago = :estado_pago";
            $params[':estado_pago'] = $filters['estado_pago'];
        }

        if (!empty($filters['fecha_desde'])) {
            $where[] = "DATE(p.fecha_pedido) >= :fecha_desde";
            $params[':fecha_desde'] = $filters['fecha_desde'];
        }

        if (!empty($filters['fecha_hasta'])) {
            $where[] = "DATE(p.fecha_pedido) <= :fecha_hasta";
            $params[':fecha_hasta'] = $filters['fecha_hasta'];
        }

        $whereClause = implode(" AND ", $where);
        $sql = "
            SELECT p.*,
                   c.nombre AS cliente_nombre,
                   c.telefono AS cliente_telefono,
                   c.numero_documento AS cliente_documento,
                   u.nombre AS usuario_nombre,
                   e.repartidor_nombre,
                   e.estado AS entrega_estado,
                   (SELECT COUNT(*) FROM detalle_pedidos dp WHERE dp.pedido_id = p.id) AS total_items
            FROM pedidos p
            JOIN clientes c ON c.id = p.cliente_id
            JOIN usuarios u ON u.id = p.usuario_id
            LEFT JOIN entregas e ON e.pedido_id = p.id
            WHERE {$whereClause}
            ORDER BY p.id DESC
            LIMIT {$limit} OFFSET {$offset}
        ";

        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public static function count(array $filters = []): int {
        $db = Database::getConnection();
        $where = ["1=1"];
        $params = [];

        if (!empty($filters['search'])) {
            $where[] = "(p.numero_pedido LIKE :search OR c.nombre LIKE :search)";
            $params[':search'] = '%' . trim($filters['search']) . '%';
        }

        if (!empty($filters['estado'])) {
            $where[] = "p.estado = :estado";
            $params[':estado'] = $filters['estado'];
        }

        if (!empty($filters['modalidad'])) {
            $where[] = "p.modalidad = :modalidad";
            $params[':modalidad'] = $filters['modalidad'];
        }

        if (!empty($filters['estado_pago'])) {
            $where[] = "p.estado_pago = :estado_pago";
            $params[':estado_pago'] = $filters['estado_pago'];
        }

        if (!empty($filters['fecha_desde'])) {
            $where[] = "DATE(p.fecha_pedido) >= :fecha_desde";
            $params[':fecha_desde'] = $filters['fecha_desde'];
        }

        if (!empty($filters['fecha_hasta'])) {
            $where[] = "DATE(p.fecha_pedido) <= :fecha_hasta";
            $params[':fecha_hasta'] = $filters['fecha_hasta'];
        }

        $whereClause = implode(" AND ", $where);
        $sql = "
            SELECT COUNT(*)
            FROM pedidos p
            JOIN clientes c ON c.id = p.cliente_id
            WHERE {$whereClause}
        ";
        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        return (int)$stmt->fetchColumn();
    }

    public static function findById(int $id): ?array {
        $db = Database::getConnection();
        $stmt = $db->prepare("
            SELECT p.*,
                   c.nombre AS cliente_nombre,
                   c.tipo_documento AS cliente_tipo_documento,
                   c.numero_documento AS cliente_documento,
                   c.telefono AS cliente_telefono,
                   c.email AS cliente_email,
                   c.direccion AS cliente_direccion,
                   u.nombre AS usuario_nombre,
                   e.id AS entrega_id,
                   e.repartidor_nombre,
                   e.repartidor_telefono,
                   e.direccion AS entrega_direccion,
                   e.referencia AS entrega_referencia,
                   e.estado AS entrega_estado,
                   e.fecha_salida AS entrega_salida,
                   e.fecha_entrega AS entrega_fin
            FROM pedidos p
            JOIN clientes c ON c.id = p.cliente_id
            JOIN usuarios u ON u.id = p.usuario_id
            LEFT JOIN entregas e ON e.pedido_id = p.id
            WHERE p.id = :id
            LIMIT 1
        ");
        $stmt->execute([':id' => $id]);
        return $stmt->fetch() ?: null;
    }

    public static function findByNumero(string $numero): ?array {
        $db = Database::getConnection();
        $stmt = $db->prepare("SELECT id FROM pedidos WHERE numero_pedido = :numero LIMIT 1");
        $stmt->execute([':numero' => trim($numero)]);
        $res = $stmt->fetch();
        return $res ? self::findById((int)$res['id']) : null;
    }

    public static function getDetalles(int $pedidoId): array {
        $db = Database::getConnection();
        $stmt = $db->prepare("
            SELECT dp.*, p.nombre AS producto_nombre, p.codigo AS producto_codigo, p.imagen AS producto_imagen
            FROM detalle_pedidos dp
            JOIN productos p ON p.id = dp.producto_id
            WHERE dp.pedido_id = :pedido_id
            ORDER BY dp.id ASC
        ");
        $stmt->execute([':pedido_id' => $pedidoId]);
        return $stmt->fetchAll();
    }

    public static function getPagos(int $pedidoId): array {
        $db = Database::getConnection();
        $stmt = $db->prepare("
            SELECT pag.*, u.nombre AS usuario_nombre
            FROM pagos pag
            LEFT JOIN usuarios u ON u.id = pag.usuario_id
            WHERE pag.pedido_id = :pedido_id
            ORDER BY pag.fecha_pago DESC
        ");
        $stmt->execute([':pedido_id' => $pedidoId]);
        return $stmt->fetchAll();
    }

    public static function getHistorial(int $pedidoId): array {
        $db = Database::getConnection();
        $stmt = $db->prepare("
            SELECT h.*, u.nombre AS usuario_nombre
            FROM historial_estados_pedido h
            LEFT JOIN usuarios u ON u.id = h.usuario_id
            WHERE h.pedido_id = :pedido_id
            ORDER BY h.id ASC
        ");
        $stmt->execute([':pedido_id' => $pedidoId]);
        return $stmt->fetchAll();
    }

    public static function generateNextNumero(): string {
        $db = Database::getConnection();
        $stmt = $db->query("SELECT MAX(id) FROM pedidos");
        $maxId = (int)$stmt->fetchColumn();
        $next = $maxId + 1;
        return sprintf("PED-%05d", $next);
    }

    /**
     * Transacción integral para registrar un nuevo pedido con validación de stock
     */
    public static function createOrder(array $orderData, array $items, ?array $initialPayment = null, ?array $deliveryData = null): int {
        $db = Database::getConnection();
        $db->beginTransaction();

        try {
            if (empty($items)) {
                throw new Exception("El pedido debe contener al menos un producto.");
            }

            // 1. Validar y calcular productos desde la base de datos
            $subtotal = 0.00;
            $itemsProcessed = [];

            foreach ($items as $item) {
                $prodId = (int)($item['producto_id'] ?? 0);
                $qty = (int)($item['cantidad'] ?? 0);
                $notas = trim($item['notas'] ?? '');

                if ($qty <= 0) {
                    throw new Exception("La cantidad para cada producto debe ser mayor a cero.");
                }

                $stmtProd = $db->prepare("SELECT id, nombre, precio_venta, stock, activo FROM productos WHERE id = :id FOR UPDATE");
                $stmtProd->execute([':id' => $prodId]);
                $prod = $stmtProd->fetch();

                if (!$prod || !$prod['activo']) {
                    throw new Exception("El producto seleccionado no está disponible.");
                }

                if ($prod['stock'] < $qty) {
                    throw new Exception("Stock insuficiente para '{$prod['nombre']}'. Disponible: {$prod['stock']} unidades.");
                }

                $price = (float)$prod['precio_venta'];
                $itemSubtotal = $price * $qty;
                $subtotal += $itemSubtotal;

                $itemsProcessed[] = [
                    'producto_id'     => $prodId,
                    'cantidad'        => $qty,
                    'precio_unitario' => $price,
                    'subtotal'        => $itemSubtotal,
                    'notas'           => $notas
                ];
            }

            $descuento = max(0.00, (float)($orderData['descuento'] ?? 0.00));
            $costoEnvio = ($orderData['modalidad'] === 'domicilio') ? max(0.00, (float)($orderData['costo_envio'] ?? 5.00)) : 0.00;
            $total = max(0.00, ($subtotal - $descuento) + $costoEnvio);

            $montoPagado = 0.00;
            if ($initialPayment && !empty($initialPayment['monto']) && (float)$initialPayment['monto'] > 0) {
                $montoPagado = (float)$initialPayment['monto'];
            }

            $saldoPendiente = max(0.00, $total - $montoPagado);
            $estadoPago = 'pendiente';
            if ($montoPagado >= $total && $total > 0) {
                $estadoPago = 'pagado';
            } elseif ($montoPagado > 0) {
                $estadoPago = 'parcial';
            }

            $numeroPedido = self::generateNextNumero();

            // 2. Insertar pedido
            $stmtOrder = $db->prepare("
                INSERT INTO pedidos (
                    numero_pedido, cliente_id, usuario_id, modalidad, estado,
                    subtotal, descuento, costo_envio, total, notas,
                    estado_pago, monto_pagado, saldo_pendiente, direccion_entrega, fecha_pedido
                ) VALUES (
                    :numero_pedido, :cliente_id, :usuario_id, :modalidad, :estado,
                    :subtotal, :descuento, :costo_envio, :total, :notas,
                    :estado_pago, :monto_pagado, :saldo_pendiente, :direccion_entrega, NOW()
                )
            ");

            $initialState = $orderData['estado'] ?? 'pendiente';

            $stmtOrder->execute([
                ':numero_pedido'     => $numeroPedido,
                ':cliente_id'        => (int)$orderData['cliente_id'],
                ':usuario_id'        => (int)$orderData['usuario_id'],
                ':modalidad'         => $orderData['modalidad'],
                ':estado'            => $initialState,
                ':subtotal'          => $subtotal,
                ':descuento'         => $descuento,
                ':costo_envio'       => $costoEnvio,
                ':total'             => $total,
                ':notas'             => !empty($orderData['notas']) ? trim($orderData['notas']) : null,
                ':estado_pago'       => $estadoPago,
                ':monto_pagado'      => $montoPagado,
                ':saldo_pendiente'   => $saldoPendiente,
                ':direccion_entrega' => !empty($orderData['direccion_entrega']) ? trim($orderData['direccion_entrega']) : null
            ]);

            $pedidoId = (int)$db->lastInsertId();

            // 3. Insertar items y descontar stock
            $stmtItem = $db->prepare("
                INSERT INTO detalle_pedidos (pedido_id, producto_id, cantidad, precio_unitario, subtotal, notas)
                VALUES (:pedido_id, :producto_id, :cantidad, :precio_unitario, :subtotal, :notas)
            ");
            $stmtDeductStock = $db->prepare("UPDATE productos SET stock = stock - :cantidad WHERE id = :id");

            foreach ($itemsProcessed as $it) {
                $stmtItem->execute([
                    ':pedido_id'        => $pedidoId,
                    ':producto_id'     => $it['producto_id'],
                    ':cantidad'        => $it['cantidad'],
                    ':precio_unitario' => $it['precio_unitario'],
                    ':subtotal'        => $it['subtotal'],
                    ':notas'           => !empty($it['notas']) ? $it['notas'] : null
                ]);

                // Descontar stock
                $stmtDeductStock->execute([
                    ':cantidad' => $it['cantidad'],
                    ':id'       => $it['producto_id']
                ]);
            }

            // 4. Registrar pago inicial si fue proporcionado
            if ($initialPayment && $montoPagado > 0) {
                $stmtPago = $db->prepare("
                    INSERT INTO pagos (pedido_id, usuario_id, metodo_pago, monto, referencia, notas, fecha_pago)
                    VALUES (:pedido_id, :usuario_id, :metodo_pago, :monto, :referencia, :notas, NOW())
                ");
                $stmtPago->execute([
                    ':pedido_id'   => $pedidoId,
                    ':usuario_id'  => (int)$orderData['usuario_id'],
                    ':metodo_pago' => $initialPayment['metodo_pago'] ?? 'efectivo',
                    ':monto'       => $montoPagado,
                    ':referencia'  => !empty($initialPayment['referencia']) ? trim($initialPayment['referencia']) : null,
                    ':notas'       => 'Pago inicial registrado al crear el pedido'
                ]);
            }

            // 5. Registrar entrega si la modalidad es a domicilio
            if ($orderData['modalidad'] === 'domicilio') {
                $stmtEnt = $db->prepare("
                    INSERT INTO entregas (pedido_id, repartidor_nombre, repartidor_telefono, direccion, referencia, estado)
                    VALUES (:pedido_id, :repartidor_nombre, :repartidor_telefono, :direccion, :referencia, 'pendiente')
                ");
                $stmtEnt->execute([
                    ':pedido_id'           => $pedidoId,
                    ':repartidor_nombre'   => !empty($deliveryData['repartidor_nombre']) ? trim($deliveryData['repartidor_nombre']) : null,
                    ':repartidor_telefono' => !empty($deliveryData['repartidor_telefono']) ? trim($deliveryData['repartidor_telefono']) : null,
                    ':direccion'           => $orderData['direccion_entrega'] ?? 'Dirección no especificada',
                    ':referencia'          => !empty($deliveryData['referencia']) ? trim($deliveryData['referencia']) : null
                ]);
            }

            // 6. Registrar historial inicial
            $stmtHist = $db->prepare("
                INSERT INTO historial_estados_pedido (pedido_id, usuario_id, estado_anterior, estado_nuevo, motivo)
                VALUES (:pedido_id, :usuario_id, NULL, :estado_nuevo, 'Creación y registro del pedido en el sistema')
            ");
            $stmtHist->execute([
                ':pedido_id'    => $pedidoId,
                ':usuario_id'   => (int)$orderData['usuario_id'],
                ':estado_nuevo' => $initialState
            ]);

            $db->commit();
            return $pedidoId;
        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }
    }

    /**
     * Transición de estado con reglas de negocio y restauración de stock en cancelación
     */
    public static function changeStatus(int $pedidoId, string $newStatus, int $usuarioId, string $motivo = ''): bool {
        $db = Database::getConnection();
        $db->beginTransaction();

        try {
            $stmt = $db->prepare("SELECT * FROM pedidos WHERE id = :id FOR UPDATE");
            $stmt->execute([':id' => $pedidoId]);
            $pedido = $stmt->fetch();

            if (!$pedido) {
                throw new Exception("Pedido no encontrado.");
            }

            $currentStatus = $pedido['estado'];

            if ($currentStatus === 'entregado') {
                throw new Exception("No se puede modificar un pedido que ya ha sido entregado.");
            }

            if ($currentStatus === 'cancelado') {
                throw new Exception("No se puede reactivar un pedido que ya ha sido cancelado.");
            }

            if ($currentStatus === $newStatus) {
                $db->rollBack();
                return true;
            }

            // Si se cancela, restaurar el inventario devuelto una sola vez
            if ($newStatus === 'cancelado') {
                $stmtItems = $db->prepare("SELECT producto_id, cantidad FROM detalle_pedidos WHERE pedido_id = :pedido_id");
                $stmtItems->execute([':pedido_id' => $pedidoId]);
                $items = $stmtItems->fetchAll();

                $stmtRestore = $db->prepare("UPDATE productos SET stock = stock + :cantidad WHERE id = :id");
                foreach ($items as $item) {
                    $stmtRestore->execute([
                        ':cantidad' => $item['cantidad'],
                        ':id'       => $item['producto_id']
                    ]);
                }
            }

            // Actualizar pedido
            $stmtUp = $db->prepare("UPDATE pedidos SET estado = :estado WHERE id = :id");
            $stmtUp->execute([
                ':estado' => $newStatus,
                ':id'     => $pedidoId
            ]);

            // Si tiene entrega asociada, sincronizar estado de entrega
            if ($newStatus === 'en_camino') {
                $db->prepare("UPDATE entregas SET estado = 'en_camino', fecha_salida = NOW() WHERE pedido_id = :id AND estado = 'pendiente'")
                   ->execute([':id' => $pedidoId]);
            } elseif ($newStatus === 'entregado') {
                $db->prepare("UPDATE entregas SET estado = 'entregado', fecha_entrega = NOW() WHERE pedido_id = :id")
                   ->execute([':id' => $pedidoId]);
            }

            // Registrar en historial
            $stmtHist = $db->prepare("
                INSERT INTO historial_estados_pedido (pedido_id, usuario_id, estado_anterior, estado_nuevo, motivo)
                VALUES (:pedido_id, :usuario_id, :estado_anterior, :estado_nuevo, :motivo)
            ");
            $stmtHist->execute([
                ':pedido_id'       => $pedidoId,
                ':usuario_id'      => $usuarioId,
                ':estado_anterior' => $currentStatus,
                ':estado_nuevo'    => $newStatus,
                ':motivo'          => !empty($motivo) ? trim($motivo) : "Cambio de estado a " . ucfirst($newStatus)
            ]);

            $db->commit();
            return true;
        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }
    }
}
