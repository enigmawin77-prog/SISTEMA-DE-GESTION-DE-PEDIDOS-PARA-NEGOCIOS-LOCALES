<?php
/**
 * Configuración General de la Aplicación
 */

date_default_timezone_set('America/Lima');

// Detección automática del Base URL
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || ($_SERVER['SERVER_PORT'] ?? 80) == 443) ? "https://" : "http://";
$host = $_SERVER['HTTP_HOST'] ?? 'localhost';
$scriptDir = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? ''));
$baseUrl = rtrim($protocol . $host . $scriptDir, '/') . '/';

// Si se accede a subcarpetas como /public o rutas internas, normalizamos a la raíz del proyecto
if (str_ends_with($baseUrl, '/public/')) {
    $baseUrl = substr($baseUrl, 0, -7);
}

return [
    'app_name'    => 'SaborLocal',
    'app_tagline' => 'Gestión de Pedidos para Negocios Locales',
    'version'     => '1.0.0',
    'base_url'    => $baseUrl,
    'currency'    => 'S/',
    'timezone'    => 'America/Lima',
    'upload_dir'  => __DIR__ . '/../public/uploads/productos/',
    'log_file'    => __DIR__ . '/../storage/logs/app.log',
    'session'     => [
        'name'     => 'SABORLOCAL_SESSID',
        'lifetime' => 86400, // 24 horas
        'path'     => '/',
        'domain'   => '',
        'secure'   => false, // true en HTTPS de producción
        'httponly' => true,
        'samesite' => 'Lax'
    ]
];
