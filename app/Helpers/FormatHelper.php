<?php
namespace App\Helpers;

use DateTime;

class FormatHelper {
    public static function money(float|int|string $amount, string $symbol = 'S/'): string {
        return $symbol . ' ' . number_format((float)$amount, 2, '.', ',');
    }

    public static function date(?string $date, string $format = 'd/m/Y H:i'): string {
        if (!$date) return '-';
        $timestamp = strtotime($date);
        return date($format, $timestamp);
    }

    public static function dateOnly(?string $date): string {
        return self::date($date, 'd/m/Y');
    }

    public static function timeOnly(?string $date): string {
        return self::date($date, 'h:i A');
    }

    public static function timeAgo(?string $datetime): string {
        if (!$datetime) return '-';
        $time = strtotime($datetime);
        $diff = time() - $time;

        if ($diff < 60) {
            return 'Hace unos segundos';
        } elseif ($diff < 3600) {
            $mins = floor($diff / 60);
            return 'Hace ' . $mins . ' min';
        } elseif ($diff < 86400) {
            $hours = floor($diff / 3600);
            return 'Hace ' . $hours . ($hours > 1 ? ' horas' : ' hora');
        } elseif ($diff < 172800) {
            return 'Ayer ' . date('h:i A', $time);
        } else {
            return date('d/m/Y h:i A', $time);
        }
    }

    public static function statusBadge(string $status): string {
        $status = strtolower($status);
        $badges = [
            'pendiente'      => ['label' => 'Pendiente',          'class' => 'badge-estado-pendiente',      'icon' => 'bi-clock'],
            'confirmado'     => ['label' => 'Confirmado',         'class' => 'badge-estado-confirmado',     'icon' => 'bi-check-circle'],
            'en_preparacion' => ['label' => 'En preparación',     'class' => 'badge-estado-en_preparacion', 'icon' => 'bi-fire'],
            'listo'          => ['label' => 'Listo para entregar','class' => 'badge-estado-listo',          'icon' => 'bi-box2-heart'],
            'en_camino'      => ['label' => 'En camino',          'class' => 'badge-estado-en_camino',      'icon' => 'bi-bicycle'],
            'entregado'      => ['label' => 'Entregado',          'class' => 'badge-estado-entregado',      'icon' => 'bi-check2-all'],
            'cancelado'      => ['label' => 'Cancelado',          'class' => 'badge-estado-cancelado',      'icon' => 'bi-x-circle']
        ];

        $info = $badges[$status] ?? ['label' => ucfirst($status), 'class' => 'bg-secondary', 'icon' => 'bi-info-circle'];
        return sprintf(
            '<span class="badge %s d-inline-flex align-items-center gap-1 px-2.5 py-1 rounded-pill"><i class="bi %s"></i> %s</span>',
            $info['class'],
            $info['icon'],
            htmlspecialchars($info['label'])
        );
    }

    public static function paymentBadge(string $status): string {
        $status = strtolower($status);
        $badges = [
            'pagado'    => ['label' => 'Pagado',    'class' => 'badge-pago-pagado',    'icon' => 'bi-shield-check'],
            'parcial'   => ['label' => 'Parcial',   'class' => 'badge-pago-parcial',   'icon' => 'bi-hourglass-split'],
            'pendiente' => ['label' => 'Pendiente', 'class' => 'badge-pago-pendiente', 'icon' => 'bi-exclamation-circle']
        ];
        $info = $badges[$status] ?? ['label' => ucfirst($status), 'class' => 'bg-secondary', 'icon' => 'bi-question'];
        return sprintf(
            '<span class="badge %s d-inline-flex align-items-center gap-1 px-2 py-1 rounded-pill"><i class="bi %s"></i> %s</span>',
            $info['class'],
            $info['icon'],
            htmlspecialchars($info['label'])
        );
    }

    public static function modalityBadge(string $modality): string {
        $modality = strtolower($modality);
        if ($modality === 'domicilio') {
            return '<span class="badge-modalidad badge-domicilio"><i class="bi bi-bicycle"></i> Domicilio</span>';
        }
        return '<span class="badge-modalidad badge-recojo"><i class="bi bi-shop"></i> Recojo</span>';
    }

    public static function e(?string $string): string {
        return htmlspecialchars((string)$string, ENT_QUOTES, 'UTF-8');
    }
}
