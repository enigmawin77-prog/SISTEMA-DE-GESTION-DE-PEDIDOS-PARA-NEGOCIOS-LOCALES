<?php
namespace App\Helpers;

class FlashHelper {
    public static function set(string $type, string $message): void {
        AuthHelper::initSession();
        $_SESSION['flash_message'] = [
            'type'    => $type, // success, danger, warning, info
            'message' => $message
        ];
    }

    public static function get(): ?array {
        AuthHelper::initSession();
        if (isset($_SESSION['flash_message'])) {
            $msg = $_SESSION['flash_message'];
            unset($_SESSION['flash_message']);
            return $msg;
        }
        return null;
    }

    public static function render(): string {
        $flash = self::get();
        if (!$flash) return '';

        $type = htmlspecialchars($flash['type'], ENT_QUOTES, 'UTF-8');
        $message = htmlspecialchars($flash['message'], ENT_QUOTES, 'UTF-8');

        $icons = [
            'success' => 'bi-check-circle-fill',
            'danger'  => 'bi-exclamation-triangle-fill',
            'warning' => 'bi-exclamation-diamond-fill',
            'info'    => 'bi-info-circle-fill'
        ];
        $icon = $icons[$type] ?? 'bi-bell-fill';

        return "
        <div class='alert alert-{$type} alert-dismissible fade show d-flex align-items-center shadow-sm rounded-3 py-2 px-3 mb-3' role='alert'>
            <i class='bi {$icon} me-2 fs-5'></i>
            <div>{$message}</div>
            <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Cerrar'></button>
        </div>";
    }
}
