<?php
namespace App\Models;

use App\Helpers\Database;
use PDO;

class Usuario {
    public static function findByEmail(string $email): ?array {
        $db = Database::getConnection();
        $stmt = $db->prepare("SELECT * FROM usuarios WHERE email = :email AND activo = 1 LIMIT 1");
        $stmt->execute([':email' => $email]);
        return $stmt->fetch() ?: null;
    }

    public static function findById(int $id): ?array {
        $db = Database::getConnection();
        $stmt = $db->prepare("SELECT id, nombre, email, rol, telefono, avatar, activo, ultimo_acceso, created_at FROM usuarios WHERE id = :id LIMIT 1");
        $stmt->execute([':id' => $id]);
        return $stmt->fetch() ?: null;
    }

    public static function getAll(): array {
        $db = Database::getConnection();
        $stmt = $db->query("SELECT id, nombre, email, rol, telefono, avatar, activo, ultimo_acceso, created_at FROM usuarios ORDER BY nombre ASC");
        return $stmt->fetchAll();
    }

    public static function updateLastLogin(int $id): void {
        $db = Database::getConnection();
        $stmt = $db->prepare("UPDATE usuarios SET ultimo_acceso = NOW() WHERE id = :id");
        $stmt->execute([':id' => $id]);
    }

    public static function create(array $data): int {
        $db = Database::getConnection();
        $stmt = $db->prepare("
            INSERT INTO usuarios (nombre, email, password, rol, telefono, avatar, activo)
            VALUES (:nombre, :email, :password, :rol, :telefono, :avatar, :activo)
        ");
        $stmt->execute([
            ':nombre'   => $data['nombre'],
            ':email'    => $data['email'],
            ':password' => password_hash($data['password'], PASSWORD_BCRYPT),
            ':rol'      => $data['rol'] ?? 'empleado',
            ':telefono' => $data['telefono'] ?? null,
            ':avatar'   => $data['avatar'] ?? null,
            ':activo'   => $data['activo'] ?? 1
        ]);
        return (int)$db->lastInsertId();
    }

    public static function update(int $id, array $data): bool {
        $db = Database::getConnection();
        $sql = "UPDATE usuarios SET nombre = :nombre, email = :email, rol = :rol, telefono = :telefono, activo = :activo";
        $params = [
            ':id'       => $id,
            ':nombre'   => $data['nombre'],
            ':email'    => $data['email'],
            ':rol'      => $data['rol'],
            ':telefono' => $data['telefono'] ?? null,
            ':activo'   => $data['activo'] ?? 1
        ];

        if (!empty($data['password'])) {
            $sql .= ", password = :password";
            $params[':password'] = password_hash($data['password'], PASSWORD_BCRYPT);
        }

        $sql .= " WHERE id = :id";
        $stmt = $db->prepare($sql);
        return $stmt->execute($params);
    }
}
