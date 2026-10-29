<?php
namespace App\Models;

use App\Helpers\Database;
use PDO;
use Exception;

class Entrega {
    public static function getAll(array $filters = [], int $limit = 50, int $offset = 0): array {
        $db = Database::getConnection();
        $where = ["1=1"];
        $params = [];

        if (!empty($filters['estado'])) {
            $where[] = "e.estado = :estado";
            $params[':estado'] = $filters['estado'];
        }

        if (!empty($filters['search'])) {
            $where[] = "(ped.numero_pedido LIKE :search OR e.repartidor_nombre LIKE :search OR cli.nombre LIKE :search OR e.direccion LIKE :search)";
            $params[':search'] = '%' . trim($filters['search']) . '%';
        }

        $whereClause = implode(" AND ", $where);
        $sql = "
            SELECT e.*,
                   ped.numero_pedido,
                   ped.total AS pedido_total,
                   ped.estado AS pedido_estado,
                   ped.estado_pago AS pedido_estado_pago,
                   cli.nombre AS cliente_nombre,
                   cli.telefono AS cliente_telefono
            FROM entregas e
            JOIN pedidos ped ON ped.id = e.pedido_id
            JOIN clientes cli ON cli.id = ped.cliente_id
            WHERE {$whereClause}
            ORDER BY e.id DESC
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

        if (!empty($filters['estado'])) {
            $where[] = "e.estado = :estado";
            $params[':estado'] = $filters['estado'];
        }

        if (!empty($filters['search'])) {
            $where[] = "(ped.numero_pedido LIKE :search OR e.repartidor_nombre LIKE :search OR cli.nombre LIKE :search)";
            $params[':search'] = '%' . trim($filters['search']) . '%';
        }

        $whereClause = implode(" AND ", $where);
        $stmt = $db->prepare("
            SELECT COUNT(*)
            FROM entregas e
            JOIN pedidos ped ON ped.id = e.pedido_id
            JOIN clientes cli ON cli.id = ped.cliente_id
            WHERE {$whereClause}
        ");
        $stmt->execute($params);
        return (int)$stmt->fetchColumn();
    }

    public static function updateDelivery(int $entregaId, array $data, int $usuarioId): bool {
        $db = Database::getConnection();
        $db->beginTransaction();

        try {
            $stmt = $db->prepare("SELECT * FROM entregas WHERE id = :id FOR UPDATE");
            $stmt->execute([':id' => $entregaId]);
            $entrega = $stmt->fetch();

            if (!$entrega) {
                throw new Exception("Entrega no encontrada.");
            }

            $nuevoEstado = $data['estado'] ?? $entrega['estado'];
            $fechaSalida = $entrega['fecha_salida'];
            $fechaEntrega = $entrega['fecha_entrega'];

            if ($nuevoEstado === 'en_camino' && empty($fechaSalida)) {
                $fechaSalida = date('Y-m-d H:i:s');
            } elseif ($nuevoEstado === 'entregado' && empty($fechaEntrega)) {
                if (empty($fechaSalida)) $fechaSalida = date('Y-m-d H:i:s');
                $fechaEntrega = date('Y-m-d H:i:s');
            }

            $stmtUp = $db->prepare("
                UPDATE entregas
                SET repartidor_nombre = :repartidor_nombre,
                    repartidor_telefono = :repartidor_telefono,
                    direccion = :direccion,
                    referencia = :referencia,
                    estado = :estado,
                    fecha_salida = :fecha_salida,
                    fecha_entrega = :fecha_entrega,
                    observaciones = :observaciones
                WHERE id = :id
            ");

            $stmtUp->execute([
                ':id'                  => $entregaId,
                ':repartidor_nombre'   => !empty($data['repartidor_nombre']) ? trim($data['repartidor_nombre']) : null,
                ':repartidor_telefono' => !empty($data['repartidor_telefono']) ? trim($data['repartidor_telefono']) : null,
                ':direccion'           => trim($data['direccion']),
                ':referencia'          => !empty($data['referencia']) ? trim($data['referencia']) : null,
                ':estado'              => $nuevoEstado,
                ':fecha_salida'        => $fechaSalida,
                ':fecha_entrega'       => $fechaEntrega,
                ':observaciones'       => !empty($data['observaciones']) ? trim($data['observaciones']) : null
            ]);

            // Sincronizar estado del pedido si corresponde
            $pedidoId = (int)$entrega['pedido_id'];
            if ($nuevoEstado === 'en_camino') {
                Pedido::changeStatus($pedidoId, 'en_camino', $usuarioId, 'Despacho con motorizado asignado');
            } elseif ($nuevoEstado === 'entregado') {
                Pedido::changeStatus($pedidoId, 'entregado', $usuarioId, 'Motorizado confirmó entrega con el cliente');
            }

            $db->commit();
            return true;
        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }
    }
}
