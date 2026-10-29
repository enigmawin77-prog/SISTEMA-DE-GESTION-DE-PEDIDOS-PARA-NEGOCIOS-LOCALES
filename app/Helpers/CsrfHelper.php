<?php
namespace App\Helpers;

class CsrfHelper {
    public static function getToken(): string {
        AuthHelper::initSession();
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }

    public static function field(): string {
        $token = htmlspecialchars(self::getToken(), ENT_QUOTES, 'UTF-8');
        return '<input type="hidden" name="csrf_token" value="' . $token . '">';
    }

    public static function validate(?string $token): bool {
        AuthHelper::initSession();
        if (empty($_SESSION['csrf_token']) || empty($token)) {
            return false;
        }
        return hash_equals($_SESSION['csrf_token'], $token);
    }

    public static function checkOrAbort(): void {
        $token = $_POST['csrf_token'] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? null;
        if (!self::validate($token)) {
            http_response_code(403);
            die(json_encode([
                'success' => false,
                'message' => 'Error de seguridad CSRF: Petición inválida o sesión expirada. Recargue la página e intente nuevamente.'
            ]));
        }
    }
}
