<?php
namespace App\Models;

use App\Helpers\Database;
use PDO;

class Producto {
    public static function getAll(array $filters = [], int $limit = 50, int $offset = 0): array {
        $db = Database::getConnection();
        $where = ["1=1"];
        $params = [];

        if (!empty($filters['search'])) {
            $where[] = "(p.nombre LIKE :search OR p.codigo LIKE :search OR p.descripcion LIKE :search)";
            $params[':search'] = '%' . trim($filters['search']) . '%';
        }

        if (!empty($filters['categoria_id'])) {
            $where[] = "p.categoria_id = :categoria_id";
            $params[':categoria_id'] = (int)$filters['categoria_id'];
        }

        if (isset($filters['activo']) && $filters['activo'] !== '') {
            $where[] = "p.activo = :activo";
            $params[':activo'] = (int)$filters['activo'];
        }

        if (!empty($filters['stock_bajo'])) {
            $where[] = "p.stock <= p.stock_minimo";
        }

        $whereClause = implode(" AND ", $where);
        $sql = "
            SELECT p.*, c.nombre AS categoria_nombre, c.color AS categoria_color
            FROM productos p
            LEFT JOIN categorias c ON c.id = p.categoria_id
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
            $where[] = "(p.nombre LIKE :search OR p.codigo LIKE :search)";
            $params[':search'] = '%' . trim($filters['search']) . '%';
        }

        if (!empty($filters['categoria_id'])) {
            $where[] = "p.categoria_id = :categoria_id";
            $params[':categoria_id'] = (int)$filters['categoria_id'];
        }

        if (isset($filters['activo']) && $filters['activo'] !== '') {
            $where[] = "p.activo = :activo";
            $params[':activo'] = (int)$filters['activo'];
        }

        if (!empty($filters['stock_bajo'])) {
            $where[] = "p.stock <= p.stock_minimo";
        }

        $whereClause = implode(" AND ", $where);
        $stmt = $db->prepare("SELECT COUNT(*) FROM productos p WHERE {$whereClause}");
        $stmt->execute($params);
        return (int)$stmt->fetchColumn();
    }

    public static function findById(int $id): ?array {
        $db = Database::getConnection();
        $stmt = $db->prepare("
            SELECT p.*, c.nombre AS categoria_nombre, c.color AS categoria_color
            FROM productos p
            LEFT JOIN categorias c ON c.id = p.categoria_id
            WHERE p.id = :id
            LIMIT 1
        ");
        $stmt->execute([':id' => $id]);
        return $stmt->fetch() ?: null;
    }

    public static function findByCodigo(string $codigo, ?int $excludeId = null): ?array {
        $db = Database::getConnection();
        $sql = "SELECT * FROM productos WHERE codigo = :codigo";
        $params = [':codigo' => trim($codigo)];
        if ($excludeId !== null) {
            $sql .= " AND id != :exclude_id";
            $params[':exclude_id'] = $excludeId;
        }
        $sql .= " LIMIT 1";
        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetch() ?: null;
    }

    public static function getActivosParaVenta(): array {
        $db = Database::getConnection();
        $stmt = $db->query("
            SELECT p.id, p.codigo, p.nombre, p.precio_venta, p.stock, p.stock_minimo, p.imagen,
                   c.id AS categoria_id, c.nombre AS categoria_nombre
            FROM productos p
            JOIN categorias c ON c.id = p.categoria_id
            WHERE p.activo = 1 AND c.activo = 1
            ORDER BY c.nombre ASC, p.nombre ASC
        ");
        return $stmt->fetchAll();
    }

    public static function create(array $data): int {
        $db = Database::getConnection();
        $stmt = $db->prepare("
            INSERT INTO productos (codigo, nombre, descripcion, categoria_id, precio_venta, costo_compra, stock, stock_minimo, imagen, activo)
            VALUES (:codigo, :nombre, :descripcion, :categoria_id, :precio_venta, :costo_compra, :stock, :stock_minimo, :imagen, :activo)
        ");
        $stmt->execute([
            ':codigo'        => strtoupper(trim($data['codigo'])),
            ':nombre'        => trim($data['nombre']),
            ':descripcion'   => !empty($data['descripcion']) ? trim($data['descripcion']) : null,
            ':categoria_id'  => (int)$data['categoria_id'],
            ':precio_venta'  => (float)$data['precio_venta'],
            ':costo_compra'  => !empty($data['costo_compra']) ? (float)$data['costo_compra'] : 0.00,
            ':stock'         => (int)$data['stock'],
            ':stock_minimo'  => isset($data['stock_minimo']) ? (int)$data['stock_minimo'] : 5,
            ':imagen'        => !empty($data['imagen']) ? $data['imagen'] : null,
            ':activo'        => isset($data['activo']) ? (int)$data['activo'] : 1
        ]);
        return (int)$db->lastInsertId();
    }

    public static function update(int $id, array $data): bool {
        $db = Database::getConnection();
        $stmt = $db->prepare("
            UPDATE productos
            SET codigo = :codigo,
                nombre = :nombre,
                descripcion = :descripcion,
                categoria_id = :categoria_id,
                precio_venta = :precio_venta,
                costo_compra = :costo_compra,
                stock = :stock,
                stock_minimo = :stock_minimo,
                imagen = COALESCE(:imagen, imagen),
                activo = :activo
            WHERE id = :id
        ");
        return $stmt->execute([
            ':id'            => $id,
            ':codigo'        => strtoupper(trim($data['codigo'])),
            ':nombre'        => trim($data['nombre']),
            ':descripcion'   => !empty($data['descripcion']) ? trim($data['descripcion']) : null,
            ':categoria_id'  => (int)$data['categoria_id'],
            ':precio_venta'  => (float)$data['precio_venta'],
            ':costo_compra'  => !empty($data['costo_compra']) ? (float)$data['costo_compra'] : 0.00,
            ':stock'         => (int)$data['stock'],
            ':stock_minimo'  => isset($data['stock_minimo']) ? (int)$data['stock_minimo'] : 5,
            ':imagen'        => !empty($data['imagen']) ? $data['imagen'] : null,
            ':activo'        => isset($data['activo']) ? (int)$data['activo'] : 1
        ]);
    }

    public static function toggleStatus(int $id): bool {
        $db = Database::getConnection();
        $stmt = $db->prepare("UPDATE productos SET activo = IF(activo = 1, 0, 1) WHERE id = :id");
        return $stmt->execute([':id' => $id]);
    }

    public static function getLowStock(int $limit = 10): array {
        $db = Database::getConnection();
        $stmt = $db->prepare("
            SELECT p.*, c.nombre AS categoria_nombre
            FROM productos p
            LEFT JOIN categorias c ON c.id = p.categoria_id
            WHERE p.stock <= p.stock_minimo AND p.activo = 1
            ORDER BY p.stock ASC
            LIMIT :limit
        ");
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }
}
