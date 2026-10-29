<?php
namespace App\Models;

use App\Helpers\Database;
use PDO;
use Exception;

class Pago {
    public static function getAll(array $filters = [], int $limit = 50, int $offset = 0): array {
        $db = Database::getConnection();
        $where = ["1=1"];
        $params = [];

        if (!empty($filters['metodo_pago'])) {
            $where[] = "pag.metodo_pago = :metodo_pago";
            $params[':metodo_pago'] = $filters['metodo_pago'];
        }

        if (!empty($filters['search'])) {
            $where[] = "(ped.numero_pedido LIKE :search OR pag.referencia LIKE :search OR cli.nombre LIKE :search)";
            $params[':search'] = '%' . trim($filters['search']) . '%';
        }

        if (!empty($filters['fecha_desde'])) {
            $where[] = "DATE(pag.fecha_pago) >= :fecha_desde";
            $params[':fecha_desde'] = $filters['fecha_desde'];
        }

        if (!empty($filters['fecha_hasta'])) {
            $where[] = "DATE(pag.fecha_pago) <= :fecha_hasta";
            $params[':fecha_hasta'] = $filters['fecha_hasta'];
        }

        $whereClause = implode(" AND ", $where);
        $sql = "
            SELECT pag.*,
                   ped.numero_pedido,
                   ped.total AS pedido_total,
                   ped.estado_pago AS pedido_estado_pago,
                   cli.nombre AS cliente_nombre,
                   u.nombre AS usuario_nombre
            FROM pagos pag
            JOIN pedidos ped ON ped.id = pag.pedido_id
            JOIN clientes cli ON cli.id = ped.cliente_id
            JOIN usuarios u ON u.id = pag.usuario_id
            WHERE {$whereClause}
            ORDER BY pag.id DESC
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

        if (!empty($filters['metodo_pago'])) {
            $where[] = "pag.metodo_pago = :metodo_pago";
            $params[':metodo_pago'] = $filters['metodo_pago'];
        }

        if (!empty($filters['search'])) {
            $where[] = "(ped.numero_pedido LIKE :search OR pag.referencia LIKE :search OR cli.nombre LIKE :search)";
            $params[':search'] = '%' . trim($filters['search']) . '%';
        }

        $whereClause = implode(" AND ", $where);
        $stmt = $db->prepare("
            SELECT COUNT(*)
            FROM pagos pag
            JOIN pedidos ped ON ped.id = pag.pedido_id
            JOIN clientes cli ON cli.id = ped.cliente_id
            WHERE {$whereClause}
        ");
        $stmt->execute($params);
        return (int)$stmt->fetchColumn();
    }

    public static function registerPayment(int $pedidoId, int $usuarioId, float $monto, string $metodoPago, ?string $referencia = null, ?string $notas = null): int {
        if ($monto <= 0) {
            throw new Exception("El monto a pagar debe ser mayor a cero.");
        }

        $db = Database::getConnection();
        $db->beginTransaction();

        try {
            $stmtPed = $db->prepare("SELECT id, total, monto_pagado, saldo_pendiente, estado FROM pedidos WHERE id = :id FOR UPDATE");
            $stmtPed->execute([':id' => $pedidoId]);
            $pedido = $stmtPed->fetch();

            if (!$pedido) {
                throw new Exception("El pedido especificado no existe.");
            }

            if ($pedido['estado'] === 'cancelado') {
                throw new Exception("No se pueden registrar pagos para un pedido cancelado.");
            }

            $currentPagado = (float)$pedido['monto_pagado'];
            $total = (float)$pedido['total'];
            $saldoActual = (float)$pedido['saldo_pendiente'];

            if ($monto > ($saldoActual + 0.01)) { // Margen pequeño de redondeo
                throw new Exception(sprintf("El monto ingresado (S/ %.2f) excede el saldo pendiente (S/ %.2f).", $monto, $saldoActual));
            }

            // 1. Insertar el pago
            $stmtIns = $db->prepare("
                INSERT INTO pagos (pedido_id, usuario_id, metodo_pago, monto, referencia, notas, fecha_pago)
                VALUES (:pedido_id, :usuario_id, :metodo_pago, :monto, :referencia, :notas, NOW())
            ");
            $stmtIns->execute([
                ':pedido_id'   => $pedidoId,
                ':usuario_id'  => $usuarioId,
                ':metodo_pago' => $metodoPago,
                ':monto'       => $monto,
                ':referencia'  => !empty($referencia) ? trim($referencia) : null,
                ':notas'       => !empty($notas) ? trim($notas) : null
            ]);
            $pagoId = (int)$db->lastInsertId();

            // 2. Recalcular montos en el pedido
            $nuevoPagado = $currentPagado + $monto;
            $nuevoSaldo = max(0.00, $total - $nuevoPagado);

            $nuevoEstadoPago = 'parcial';
            if ($nuevoSaldo <= 0.001) {
                $nuevoEstadoPago = 'pagado';
            }

            $stmtUp = $db->prepare("
                UPDATE pedidos
                SET monto_pagado = :monto_pagado,
                    saldo_pendiente = :saldo_pendiente,
                    estado_pago = :estado_pago
                WHERE id = :id
            ");
            $stmtUp->execute([
                ':monto_pagado'    => $nuevoPagado,
                ':saldo_pendiente' => $nuevoSaldo,
                ':estado_pago'     => $nuevoEstadoPago,
                ':id'              => $pedidoId
            ]);

            $db->commit();
            return $pagoId;
        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }
    }
}
