<?php
namespace App\Helpers;

use PDO;
use PDOException;

class Database {
    private static ?PDO $instance = null;

    public static function getConnection(): PDO {
        if (self::$instance === null) {
            $config = require __DIR__ . '/../../config/database.php';
            
            $dsn = sprintf(
                "mysql:host=%s;port=%d;dbname=%s;charset=%s",
                $config['host'],
                $config['port'],
                $config['dbname'],
                $config['charset']
            );

            try {
                self::$instance = new PDO($dsn, $config['username'], $config['password'], $config['options']);
            } catch (PDOException $e) {
                // Registrar el error en el archivo privado sin exponer contraseñas
                $logFile = __DIR__ . '/../../storage/logs/app.log';
                $message = "[" . date('Y-m-d H:i:s') . "] Error de conexión a la BD: " . $e->getMessage() . PHP_EOL;
                @file_put_contents($logFile, $message, FILE_APPEND);
                
                die("<h3>Error al conectar con la base de datos.</h3><p>Por favor verifique que MySQL esté iniciado en XAMPP y que la base de datos 'gestion_pedidos_locales' esté creada e importada.</p>");
            }
        }

        return self::$instance;
    }

    /**
     * Cierra la conexión si es necesario
     */
    public static function closeConnection(): void {
        self::$instance = null;
    }
}
