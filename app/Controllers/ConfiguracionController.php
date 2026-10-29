<?php
namespace App\Controllers;

use App\Helpers\AuthHelper;
use App\Helpers\CsrfHelper;
use App\Helpers\FlashHelper;
use App\Models\Configuracion;
use App\Validators\Validator;

class ConfiguracionController {
    public function index(): void {
        AuthHelper::requireAdmin();
        $configuracion = Configuracion::get();
        require __DIR__ . '/../../views/configuracion/index.php';
    }

    public function update(): void {
        AuthHelper::requireAdmin();
        CsrfHelper::checkOrAbort();

        $validator = new Validator($_POST);
        $validator->required('nombre_comercial', 'Nombre Comercial')
                  ->required('direccion', 'Dirección')
                  ->required('telefono', 'Teléfono')
                  ->required('email', 'Correo electrónico')
                  ->email('email', 'Correo electrónico');

        if (!$validator->isValid()) {
            FlashHelper::set('danger', $validator->getFirstError());
            header("Location: index.php?route=configuracion");
            exit;
        }

        Configuracion::update([
            'nombre_comercial'           => $_POST['nombre_comercial'],
            'ruc'                        => $_POST['ruc'] ?? '',
            'razon_social'               => $_POST['razon_social'] ?? '',
            'direccion'                  => $_POST['direccion'],
            'telefono'                   => $_POST['telefono'],
            'email'                      => $_POST['email'],
            'moneda_simbolo'             => $_POST['moneda_simbolo'] ?? 'S/',
            'moneda_codigo'              => $_POST['moneda_codigo'] ?? 'PEN',
            'igv_porcentaje'             => (float)($_POST['igv_porcentaje'] ?? 18.00),
            'costo_envio_predeterminado' => (float)($_POST['costo_envio_predeterminado'] ?? 5.00),
            'mensaje_ticket'             => $_POST['mensaje_ticket'] ?? ''
        ]);

        FlashHelper::set('success', 'Configuración del negocio actualizada con éxito.');
        header("Location: index.php?route=configuracion");
        exit;
    }
}
