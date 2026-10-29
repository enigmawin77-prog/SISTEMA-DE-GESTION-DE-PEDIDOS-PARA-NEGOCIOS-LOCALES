<?php
namespace App\Helpers;

class AuthHelper {
    public static function initSession(): void {
        if (session_status() === PHP_SESSION_NONE) {
            $config = require __DIR__ . '/../../config/app.php';
            $sess = $config['session'];

            session_name($sess['name']);
            session_set_cookie_params([
                'lifetime' => $sess['lifetime'],
                'path'     => $sess['path'],
                'domain'   => $sess['domain'],
                'secure'   => $sess['secure'],
                'httponly' => $sess['httponly'],
                'samesite' => $sess['samesite']
            ]);
            session_start();
        }
    }

    public static function check(): bool {
        self::initSession();
        return isset($_SESSION['usuario_id']) && !empty($_SESSION['usuario_id']);
    }

    public static function user(): ?array {
        self::initSession();
        if (!self::check()) {
            return null;
        }
        return [
            'id'     => $_SESSION['usuario_id'],
            'nombre' => $_SESSION['usuario_nombre'] ?? 'Usuario',
            'email'  => $_SESSION['usuario_email'] ?? '',
            'rol'    => $_SESSION['usuario_rol'] ?? 'empleado',
            'avatar' => $_SESSION['usuario_avatar'] ?? null
        ];
    }

    public static function id(): ?int {
        self::initSession();
        return $_SESSION['usuario_id'] ?? null;
    }

    public static function rol(): string {
        self::initSession();
        return $_SESSION['usuario_rol'] ?? 'empleado';
    }

    public static function isAdmin(): bool {
        return self::rol() === 'admin';
    }

    public static function isEmpleado(): bool {
        return self::rol() === 'empleado';
    }

    public static function login(array $usuario): void {
        self::initSession();
        session_regenerate_id(true);
        $_SESSION['usuario_id']     = (int)$usuario['id'];
        $_SESSION['usuario_nombre'] = $usuario['nombre'];
        $_SESSION['usuario_email']  = $usuario['email'];
        $_SESSION['usuario_rol']    = $usuario['rol'];
        $_SESSION['usuario_avatar'] = $usuario['avatar'] ?? null;
        $_SESSION['login_time']     = time();
    }

    public static function logout(): void {
        self::initSession();
        $_SESSION = [];
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
            );
        }
        session_destroy();
    }

    public static function requireAuth(): void {
        if (!self::check()) {
            header("Location: index.php?route=login");
            exit;
        }
    }

    public static function requireAdmin(): void {
        self::requireAuth();
        if (!self::isAdmin()) {
            FlashHelper::set('danger', 'Acceso denegado: Se requieren permisos de Administrador.');
            header("Location: index.php?route=dashboard");
            exit;
        }
    }
}
