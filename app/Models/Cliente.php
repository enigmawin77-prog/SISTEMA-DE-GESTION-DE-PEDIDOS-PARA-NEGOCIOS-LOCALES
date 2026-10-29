<?php
namespace App\Models;

use App\Helpers\Database;
use PDO;

class Cliente {
    public static function getAll(array $filters = [], int $limit = 50, int $offset = 0): array {
        $db = Database::getConnection();
        $where = ["1=1"];
        $params = [];

        if (!empty($filters['search'])) {
            $where[] = "(c.nombre LIKE :search OR c.numero_documento LIKE :search OR c.telefono LIKE :search)";
            $params[':search'] = '%' . trim($filters['search']) . '%';
        }

        if (isset($filters['activo']) && $filters['activo'] !== '') {
            $where[] = "c.activo = :activo";
            $params[':activo'] = (int)$filters['activo'];
        }

        $whereClause = implode(" AND ", $where);
        $sql = "
            SELECT c.*,
                   COUNT(p.id) AS total_pedidos,
                   COALESCE(SUM(CASE WHEN p.estado != 'cancelado' THEN p.total ELSE 0 END), 0) AS total_compras
            FROM clientes c
            LEFT JOIN pedidos p ON p.cliente_id = c.id
            WHERE {$whereClause}
            GROUP BY c.id
            ORDER BY c.nombre ASC
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
            $where[] = "(nombre LIKE :search OR numero_documento LIKE :search OR telefono LIKE :search)";
            $params[':search'] = '%' . trim($filters['search']) . '%';
        }

        $whereClause = implode(" AND ", $where);
        $stmt = $db->prepare("SELECT COUNT(*) FROM clientes WHERE {$whereClause}");
        $stmt->execute($params);
        return (int)$stmt->fetchColumn();
    }

    public static function findById(int $id): ?array {
        $db = Database::getConnection();
        $stmt = $db->prepare("
            SELECT c.*,
                   COUNT(p.id) AS total_pedidos,
                   COALESCE(SUM(CASE WHEN p.estado != 'cancelado' THEN p.total ELSE 0 END), 0) AS total_compras
            FROM clientes c
            LEFT JOIN pedidos p ON p.cliente_id = c.id
            WHERE c.id = :id
            GROUP BY c.id
            LIMIT 1
        ");
        $stmt->execute([':id' => $id]);
        return $stmt->fetch() ?: null;
    }

    public static function getHistorialPedidos(int $clienteId): array {
        $db = Database::getConnection();
        $stmt = $db->prepare("
            SELECT p.*, u.nombre AS usuario_nombre
            FROM pedidos p
            LEFT JOIN usuarios u ON u.id = p.usuario_id
            WHERE p.cliente_id = :cliente_id
            ORDER BY p.fecha_pedido DESC
        ");
        $stmt->execute([':cliente_id' => $clienteId]);
        return $stmt->fetchAll();
    }

    public static function create(array $data): int {
        $db = Database::getConnection();
        $stmt = $db->prepare("
            INSERT INTO clientes (tipo_documento, numero_documento, nombre, telefono, email, direccion, referencia, notas, activo)
            VALUES (:tipo_documento, :numero_documento, :nombre, :telefono, :email, :direccion, :referencia, :notas, :activo)
        ");
        $stmt->execute([
            ':tipo_documento'   => $data['tipo_documento'] ?? 'DNI',
            ':numero_documento' => !empty($data['numero_documento']) ? trim($data['numero_documento']) : null,
            ':nombre'           => trim($data['nombre']),
            ':telefono'         => !empty($data['telefono']) ? trim($data['telefono']) : null,
            ':email'            => !empty($data['email']) ? trim($data['email']) : null,
            ':direccion'        => !empty($data['direccion']) ? trim($data['direccion']) : null,
            ':referencia'       => !empty($data['referencia']) ? trim($data['referencia']) : null,
            ':notas'            => !empty($data['notas']) ? trim($data['notas']) : null,
            ':activo'           => isset($data['activo']) ? (int)$data['activo'] : 1
        ]);
        return (int)$db->lastInsertId();
    }

    public static function update(int $id, array $data): bool {
        $db = Database::getConnection();
        $stmt = $db->prepare("
            UPDATE clientes
            SET tipo_documento = :tipo_documento,
                numero_documento = :numero_documento,
                nombre = :nombre,
                telefono = :telefono,
                email = :email,
                direccion = :direccion,
                referencia = :referencia,
                notas = :notas,
                activo = :activo
            WHERE id = :id
        ");
        return $stmt->execute([
            ':id'               => $id,
            ':tipo_documento'   => $data['tipo_documento'] ?? 'DNI',
            ':numero_documento' => !empty($data['numero_documento']) ? trim($data['numero_documento']) : null,
            ':nombre'           => trim($data['nombre']),
            ':telefono'         => !empty($data['telefono']) ? trim($data['telefono']) : null,
            ':email'            => !empty($data['email']) ? trim($data['email']) : null,
            ':direccion'        => !empty($data['direccion']) ? trim($data['direccion']) : null,
            ':referencia'       => !empty($data['referencia']) ? trim($data['referencia']) : null,
            ':notas'            => !empty($data['notas']) ? trim($data['notas']) : null,
            ':activo'           => isset($data['activo']) ? (int)$data['activo'] : 1
        ]);
    }
}
