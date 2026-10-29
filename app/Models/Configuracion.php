<?php
namespace App\Models;

use App\Helpers\Database;
use PDO;

class Configuracion {
    public static function get(): array {
        $db = Database::getConnection();
        $stmt = $db->query("SELECT * FROM configuracion_negocio LIMIT 1");
        $conf = $stmt->fetch();
        if (!$conf) {
            return [
                'nombre_comercial'          => 'Restaurante El Buen Sabor',
                'ruc'                       => '20601234567',
                'razon_social'              => 'El Buen Sabor S.A.C.',
                'direccion'                 => 'Av. Las Palmeras 123, Miraflores, Lima',
                'telefono'                  => '+51 987 654 321',
                'email'                     => 'contacto@elbuensabor.pe',
                'logo'                      => 'logo.png',
                'moneda_simbolo'            => 'S/',
                'moneda_codigo'             => 'PEN',
                'igv_porcentaje'            => 18.00,
                'costo_envio_predeterminado' => 5.00,
                'mensaje_ticket'            => '¡Gracias por su preferencia! Comprobante interno.'
            ];
        }
        return $conf;
    }

    public static function update(array $data): bool {
        $db = Database::getConnection();
        $stmt = $db->prepare("
            UPDATE configuracion_negocio
            SET nombre_comercial = :nombre_comercial,
                ruc = :ruc,
                razon_social = :razon_social,
                direccion = :direccion,
                telefono = :telefono,
                email = :email,
                moneda_simbolo = :moneda_simbolo,
                moneda_codigo = :moneda_codigo,
                igv_porcentaje = :igv_porcentaje,
                costo_envio_predeterminado = :costo_envio_predeterminado,
                mensaje_ticket = :mensaje_ticket,
                logo = COALESCE(:logo, logo)
            WHERE id = 1
        ");
        return $stmt->execute([
            ':nombre_comercial'          => trim($data['nombre_comercial']),
            ':ruc'                       => !empty($data['ruc']) ? trim($data['ruc']) : null,
            ':razon_social'              => !empty($data['razon_social']) ? trim($data['razon_social']) : null,
            ':direccion'                 => trim($data['direccion']),
            ':telefono'                  => trim($data['telefono']),
            ':email'                     => trim($data['email']),
            ':moneda_simbolo'            => trim($data['moneda_simbolo'] ?? 'S/'),
            ':moneda_codigo'             => trim($data['moneda_codigo'] ?? 'PEN'),
            ':igv_porcentaje'            => (float)($data['igv_porcentaje'] ?? 18.00),
            ':costo_envio_predeterminado'=> (float)($data['costo_envio_predeterminado'] ?? 5.00),
            ':mensaje_ticket'            => trim($data['mensaje_ticket'] ?? ''),
            ':logo'                      => !empty($data['logo']) ? $data['logo'] : null
        ]);
    }
}
