<?php
use App\Helpers\FormatHelper;

$pageTitle = "Comprobante " . $pedido['numero_pedido'];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Comprobante <?= htmlspecialchars($pedido['numero_pedido']) ?> - <?= htmlspecialchars($configuracion['nombre_comercial']) ?></title>
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: 'Courier New', Courier, monospace, monospace;
        }
        body {
            background-color: #f1f5f9;
            padding: 20px;
            display: flex;
            justify-content: center;
        }
        .ticket-wrapper {
            width: 80mm;
            background: #ffffff;
            padding: 15px 12px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            font-size: 12px;
            line-height: 1.35;
            color: #000000;
        }
        .text-center { text-align: center; }
        .text-end { text-align: right; }
        .fw-bold { font-weight: bold; }
        .text-uppercase { text-transform: uppercase; }
        .divider {
            border-bottom: 1px dashed #000000;
            margin: 8px 0;
        }
        .double-divider {
            border-bottom: 2px dashed #000000;
            margin: 10px 0;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 11.5px;
        }
        th, td {
            padding: 3px 0;
        }
        .btn-print-box {
            position: fixed;
            top: 20px;
            right: 20px;
        }
        .btn-print {
            background: #2563eb;
            color: #ffffff;
            border: none;
            padding: 10px 18px;
            border-radius: 8px;
            font-family: sans-serif;
            font-weight: bold;
            cursor: pointer;
            box-shadow: 0 4px 10px rgba(37,99,235,0.3);
        }
        @media print {
            body {
                background: none;
                padding: 0;
            }
            .ticket-wrapper {
                width: 100%;
                box-shadow: none;
                padding: 0;
            }
            .btn-print-box {
                display: none;
            }
        }
    </style>
</head>
<body>

<div class="btn-print-box">
    <button class="btn-print" onclick="window.print()">🖨️ Imprimir Ticket</button>
</div>

<div class="ticket-wrapper">
    <!-- Encabezado de la Empresa -->
    <div class="text-center">
        <h2 class="fw-bold fs-6" style="font-size: 16px; margin-bottom: 2px;"><?= htmlspecialchars($configuracion['nombre_comercial']) ?></h2>
        <?php if (!empty($configuracion['razon_social'])): ?>
            <div style="font-size: 11px;"><?= htmlspecialchars($configuracion['razon_social']) ?></div>
        <?php endif; ?>
        <?php if (!empty($configuracion['ruc'])): ?>
            <div class="fw-bold">RUC: <?= htmlspecialchars($configuracion['ruc']) ?></div>
        <?php endif; ?>
        <div style="font-size: 11px;"><?= htmlspecialchars($configuracion['direccion']) ?></div>
        <div style="font-size: 11px;">Telf: <?= htmlspecialchars($configuracion['telefono']) ?></div>
    </div>

    <div class="double-divider"></div>

    <!-- Información del Pedido -->
    <div class="text-center fw-bold" style="font-size: 14px; margin-bottom: 4px;">
        COMPROBANTE DE PEDIDO
    </div>
    <div class="text-center fw-bold" style="font-size: 15px; margin-bottom: 6px;">
        <?= htmlspecialchars($pedido['numero_pedido']) ?>
    </div>

    <div><strong>Fecha/Hora:</strong> <?= FormatHelper::date($pedido['fecha_pedido']) ?></div>
    <div><strong>Atendido por:</strong> <?= htmlspecialchars($pedido['usuario_nombre']) ?></div>
    <div><strong>Modalidad:</strong> <?= strtoupper($pedido['modalidad']) ?></div>

    <div class="divider"></div>

    <!-- Información del Cliente -->
    <div><strong>Cliente:</strong> <?= htmlspecialchars($pedido['cliente_nombre']) ?></div>
    <?php if (!empty($pedido['cliente_documento'])): ?>
        <div><strong><?= $pedido['cliente_tipo_documento'] ?>:</strong> <?= htmlspecialchars($pedido['cliente_documento']) ?></div>
    <?php endif; ?>
    <?php if (!empty($pedido['cliente_telefono'])): ?>
        <div><strong>Teléfono:</strong> <?= htmlspecialchars($pedido['cliente_telefono']) ?></div>
    <?php endif; ?>
    <?php if ($pedido['modalidad'] === 'domicilio'): ?>
        <div><strong>Dirección:</strong> <?= htmlspecialchars($pedido['entrega_direccion'] ?: $pedido['direccion_entrega']) ?></div>
        <?php if (!empty($pedido['entrega_referencia'])): ?>
            <div><strong>Ref:</strong> <?= htmlspecialchars($pedido['entrega_referencia']) ?></div>
        <?php endif; ?>
    <?php endif; ?>

    <div class="divider"></div>

    <!-- Detalle de Productos -->
    <table>
        <thead>
            <tr style="border-bottom: 1px dashed #000;">
                <th style="text-align: left;">Cant/Desc</th>
                <th style="text-align: right;">P.U.</th>
                <th style="text-align: right;">Total</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($detalles as $it): ?>
                <tr>
                    <td colspan="3" style="font-weight: bold; padding-top: 4px;">
                        <?= htmlspecialchars($it['producto_nombre']) ?>
                    </td>
                </tr>
                <tr style="border-bottom: 1px dotted #ccc;">
                    <td style="padding-left: 8px;"><?= $it['cantidad'] ?> uds</td>
                    <td style="text-align: right;"><?= number_format($it['precio_unitario'], 2) ?></td>
                    <td style="text-align: right; font-weight: bold;"><?= number_format($it['subtotal'], 2) ?></td>
                </tr>
                <?php if (!empty($it['notas'])): ?>
                    <tr>
                        <td colspan="3" style="font-style: italic; font-size: 10.5px; padding-left: 8px;">* <?= htmlspecialchars($it['notas']) ?></td>
                    </tr>
                <?php endif; ?>
            <?php endforeach; ?>
        </tbody>
    </table>

    <div class="divider"></div>

    <!-- Totales -->
    <table>
        <tr>
            <td>Subtotal:</td>
            <td class="text-end"><?= FormatHelper::money($pedido['subtotal']) ?></td>
        </tr>
        <?php if ((float)$pedido['descuento'] > 0): ?>
            <tr>
                <td>Descuento:</td>
                <td class="text-end">- <?= FormatHelper::money($pedido['descuento']) ?></td>
            </tr>
        <?php endif; ?>
        <?php if ((float)$pedido['costo_envio'] > 0): ?>
            <tr>
                <td>Envío:</td>
                <td class="text-end">+ <?= FormatHelper::money($pedido['costo_envio']) ?></td>
            </tr>
        <?php endif; ?>
        <tr style="font-size: 14px; font-weight: bold;">
            <td>TOTAL <?= $configuracion['moneda_simbolo'] ?>:</td>
            <td class="text-end"><?= FormatHelper::money($pedido['total']) ?></td>
        </tr>
    </table>

    <div class="divider"></div>

    <!-- Resumen de Pagos -->
    <div><strong>Estado Pago:</strong> <?= strtoupper($pedido['estado_pago']) ?></div>
    <div><strong>Monto Pagado:</strong> <?= FormatHelper::money($pedido['monto_pagado']) ?></div>
    <?php if ((float)$pedido['saldo_pendiente'] > 0): ?>
        <div style="font-weight: bold; color: #000;"><strong>SALDO PENDIENTE:</strong> <?= FormatHelper::money($pedido['saldo_pendiente']) ?></div>
    <?php endif; ?>

    <?php if (!empty($pagos)): ?>
        <div style="font-size: 11px; margin-top: 4px;">
            Método(s): 
            <?php 
                $metodos = array_map(function($p) { return strtoupper($p['metodo_pago']) . ' (S/ ' . number_format($p['monto'], 2) . ')'; }, $pagos);
                echo implode(', ', $metodos);
            ?>
        </div>
    <?php endif; ?>

    <div class="double-divider"></div>

    <!-- Mensaje Final -->
    <div class="text-center" style="font-size: 11px;">
        <p><?= htmlspecialchars($configuracion['mensaje_ticket']) ?></p>
        <p style="margin-top: 4px; font-size: 10px; color: #555;">Control Interno de Pedidos - SaborLocal</p>
    </div>
</div>

<script>
// Auto-abrir diálogo de impresión si se desea
// window.print();
</script>

</body>
</html>
