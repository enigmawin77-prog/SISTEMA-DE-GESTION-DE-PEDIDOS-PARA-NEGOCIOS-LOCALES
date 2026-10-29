<?php
namespace App\Helpers;

/**
 * Helper de autenticación para el portal del cliente
 */
class PortalAuthHelper {

    public static function check(): bool {
        self::init();
        return isset($_SESSION['cliente_id']) && !empty($_SESSION['cliente_id']);
    }

    public static function init(): void {
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
                'samesite' => $sess['samesite'],
            ]);
            session_start();
        }
    }

    public static function login(array $cliente): void {
        self::init();
        session_regenerate_id(true);
        $_SESSION['cliente_id']     = (int)$cliente['id'];
        $_SESSION['cliente_nombre'] = $cliente['nombre'];
        $_SESSION['cliente_email']  = $cliente['email'];
    }

    public static function logout(): void {
        self::init();
        unset($_SESSION['cliente_id'], $_SESSION['cliente_nombre'], $_SESSION['cliente_email'], $_SESSION['carrito']);
    }

    public static function id(): ?int {
        return $_SESSION['cliente_id'] ?? null;
    }

    public static function nombre(): string {
        return $_SESSION['cliente_nombre'] ?? 'Cliente';
    }

    public static function email(): string {
        return $_SESSION['cliente_email'] ?? '';
    }

    public static function requireAuth(): void {
        self::init();
        if (!self::check()) {
            header('Location: index.php?route=portal_login');
            exit;
        }
    }

    /* ---- Carrito en sesión ---- */

    public static function carritoGet(): array {
        self::init();
        return $_SESSION['carrito'] ?? [];
    }

    public static function carritoAgregar(int $productoId, int $cantidad, float $precio, string $nombre, string $imagen = ''): void {
        self::init();
        if (!isset($_SESSION['carrito'])) $_SESSION['carrito'] = [];
        if (isset($_SESSION['carrito'][$productoId])) {
            $_SESSION['carrito'][$productoId]['cantidad'] += $cantidad;
        } else {
            $_SESSION['carrito'][$productoId] = [
                'producto_id' => $productoId,
                'nombre'      => $nombre,
                'precio'      => $precio,
                'cantidad'    => $cantidad,
                'imagen'      => $imagen,
            ];
        }
        // Recalcular subtotal
        $_SESSION['carrito'][$productoId]['subtotal'] =
            $_SESSION['carrito'][$productoId]['precio'] * $_SESSION['carrito'][$productoId]['cantidad'];
    }

    public static function carritoActualizar(int $productoId, int $cantidad): void {
        self::init();
        if ($cantidad <= 0) {
            unset($_SESSION['carrito'][$productoId]);
        } elseif (isset($_SESSION['carrito'][$productoId])) {
            $_SESSION['carrito'][$productoId]['cantidad'] = $cantidad;
            $_SESSION['carrito'][$productoId]['subtotal']  = $_SESSION['carrito'][$productoId]['precio'] * $cantidad;
        }
    }

    public static function carritoEliminar(int $productoId): void {
        self::init();
        unset($_SESSION['carrito'][$productoId]);
    }

    public static function carritoLimpiar(): void {
        self::init();
        $_SESSION['carrito'] = [];
    }

    public static function carritoTotal(): float {
        $total = 0;
        foreach (self::carritoGet() as $item) {
            $total += $item['subtotal'];
        }
        return $total;
    }

    public static function carritoCount(): int {
        return array_sum(array_column(self::carritoGet(), 'cantidad'));
    }
}
