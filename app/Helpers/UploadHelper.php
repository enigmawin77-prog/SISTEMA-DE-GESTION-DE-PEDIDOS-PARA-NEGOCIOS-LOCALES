<?php
namespace App\Helpers;

class UploadHelper {
    public static function uploadProductImage(array $file): ?string {
        if (!isset($file['error']) || is_array($file['error']) || $file['error'] !== UPLOAD_ERR_OK) {
            return null;
        }

        // Validación de tamaño (Máximo 2MB)
        if ($file['size'] > 2 * 1024 * 1024) {
            throw new \Exception('La imagen es demasiado grande. El límite máximo es de 2 MB.');
        }

        // Validación de formato por tipo MIME real
        $finfo = new \finfo(FILEINFO_MIME_TYPE);
        $mime = $finfo->file($file['tmp_name']);

        $allowedMimes = [
            'jpg'  => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'png'  => 'image/png',
            'webp' => 'image/webp'
        ];

        $ext = array_search($mime, $allowedMimes, true);
        if ($ext === false) {
            throw new \Exception('Formato de imagen inválido. Solo se admiten JPG, PNG y WEBP.');
        }

        $uploadDir = __DIR__ . '/../../public/uploads/productos/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        $filename = sprintf('prod_%s.%s', bin2hex(random_bytes(10)), $ext);
        $destination = $uploadDir . $filename;

        if (!move_uploaded_file($file['tmp_name'], $destination)) {
            throw new \Exception('Error al guardar el archivo en el servidor.');
        }

        return $filename;
    }
}
