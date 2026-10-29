<?php
namespace App\Models;

use App\Helpers\Database;
use PDO;

class Categoria {
    public static function getAll(bool $onlyActive = false): array {
        $db = Database::getConnection();
        $where = $onlyActive ? "WHERE c.activo = 1" : "";
        $sql = "
            SELECT c.*, COUNT(p.id) AS total_productos
            FROM categorias c
            LEFT JOIN productos p ON p.categoria_id = c.id AND p.activo = 1
            {$where}
            GROUP BY c.id
            ORDER BY c.nombre ASC
        ";
        return $db->query($sql)->fetchAll();
    }

    public static function findById(int $id): ?array {
        $db = Database::getConnection();
        $stmt = $db->prepare("SELECT * FROM categorias WHERE id = :id LIMIT 1");
        $stmt->execute([':id' => $id]);
        return $stmt->fetch() ?: null;
    }

    public static function create(array $data): int {
        $db = Database::getConnection();
        $stmt = $db->prepare("
            INSERT INTO categorias (nombre, descripcion, icono, color, activo)
            VALUES (:nombre, :descripcion, :icono, :color, :activo)
        ");
        $stmt->execute([
            ':nombre'      => trim($data['nombre']),
            ':descripcion' => !empty($data['descripcion']) ? trim($data['descripcion']) : null,
            ':icono'       => !empty($data['icono']) ? trim($data['icono']) : 'bi-tag',
            ':color'       => !empty($data['color']) ? trim($data['color']) : '#0d6efd',
            ':activo'      => isset($data['activo']) ? (int)$data['activo'] : 1
        ]);
        return (int)$db->lastInsertId();
    }

    public static function update(int $id, array $data): bool {
        $db = Database::getConnection();
        $stmt = $db->prepare("
            UPDATE categorias
            SET nombre = :nombre,
                descripcion = :descripcion,
                icono = :icono,
                color = :color,
                activo = :activo
            WHERE id = :id
        ");
        return $stmt->execute([
            ':id'          => $id,
            ':nombre'      => trim($data['nombre']),
            ':descripcion' => !empty($data['descripcion']) ? trim($data['descripcion']) : null,
            ':icono'       => !empty($data['icono']) ? trim($data['icono']) : 'bi-tag',
            ':color'       => !empty($data['color']) ? trim($data['color']) : '#0d6efd',
            ':activo'      => isset($data['activo']) ? (int)$data['activo'] : 1
        ]);
    }

    public static function toggleStatus(int $id): bool {
        $db = Database::getConnection();
        $stmt = $db->prepare("UPDATE categorias SET activo = IF(activo = 1, 0, 1) WHERE id = :id");
        return $stmt->execute([':id' => $id]);
    }
}
