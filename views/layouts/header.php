<?php
use App\Helpers\AuthHelper;
use App\Models\Configuracion;

$conf = Configuracion::get();
$currentUser = AuthHelper::user();
$currentRoute = $_GET['route'] ?? 'dashboard';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($pageTitle) ? htmlspecialchars($pageTitle) . ' | ' : '' ?>SaborLocal - Gestión de Pedidos</title>
    <meta name="description" content="Sistema profesional de gestión de pedidos, clientes, inventario y entregas para negocios locales.">
    
    <!-- Bootstrap 5.3.3 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <!-- SweetAlert2 (Para confirmaciones profesionales) -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <!-- Chart.js 4 -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <!-- Estilos personalizados -->
    <link rel="stylesheet" href="public/assets/css/style.css">
</head>
<body>
<div class="app-wrapper">
