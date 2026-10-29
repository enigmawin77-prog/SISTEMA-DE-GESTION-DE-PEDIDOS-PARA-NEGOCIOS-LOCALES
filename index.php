<?php
/**
 * PUNTO DE ENTRADA PRINCIPAL (FRONT CONTROLLER)
 * Sistema de GestiÃ³n de Pedidos para Negocios Locales - SaborLocal
 */

// Reporte de errores controlado
error_reporting(E_ALL);
ini_set('display_errors', '1');
ini_set('log_errors', '1');
ini_set('error_log', __DIR__ . '/storage/logs/app.log');

// Autocargador PSR-4 para el namespace App\
spl_autoload_register(function ($class) {
    $prefix = 'App\\';
    $base_dir = __DIR__ . '/app/';
    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        return;
    }
    $relative_class = substr($class, $len);
    $file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';
    if (file_exists($file)) {
        require_once $file;
    }
});

// Inicializar sesiÃ³n segura
\App\Helpers\AuthHelper::initSession();

// Determinar ruta solicitada
$route = $_GET['route'] ?? '';
$route = trim($route, '/');
$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';

// Cargar y despachar enrutador
$router = require __DIR__ . '/routes/web.php';
$router($route, $method);

