<?php
namespace App\Controllers;

use App\Helpers\AuthHelper;
use App\Helpers\CsrfHelper;
use App\Helpers\FlashHelper;
use App\Models\Categoria;
use App\Validators\Validator;

class CategoriaController {
    public function index(): void {
        AuthHelper::requireAuth();
        $categorias = Categoria::getAll();
        require __DIR__ . '/../../views/categorias/index.php';
    }

    public function store(): void {
        AuthHelper::requireAuth();
        CsrfHelper::checkOrAbort();

        $validator = new Validator($_POST);
        $validator->required('nombre', 'Nombre de categoría');

        if (!$validator->isValid()) {
            FlashHelper::set('danger', $validator->getFirstError());
            header("Location: index.php?route=categorias");
            exit;
        }

        try {
            Categoria::create([
                'nombre'      => $_POST['nombre'],
                'descripcion' => $_POST['descripcion'] ?? null,
                'icono'       => $_POST['icono'] ?? 'bi-tag',
                'color'       => $_POST['color'] ?? '#0d6efd',
                'activo'      => isset($_POST['activo']) ? 1 : 0
            ]);
            FlashHelper::set('success', 'Categoría creada con éxito.');
        } catch (\Exception $e) {
            FlashHelper::set('danger', 'Error: La categoría ya existe o datos no válidos.');
        }

        header("Location: index.php?route=categorias");
        exit;
    }

    public function update(): void {
        AuthHelper::requireAuth();
        CsrfHelper::checkOrAbort();

        $id = (int)($_POST['id'] ?? 0);
        $validator = new Validator($_POST);
        $validator->required('nombre', 'Nombre de categoría');

        if (!$validator->isValid()) {
            FlashHelper::set('danger', $validator->getFirstError());
            header("Location: index.php?route=categorias");
            exit;
        }

        try {
            Categoria::update($id, [
                'nombre'      => $_POST['nombre'],
                'descripcion' => $_POST['descripcion'] ?? null,
                'icono'       => $_POST['icono'] ?? 'bi-tag',
                'color'       => $_POST['color'] ?? '#0d6efd',
                'activo'      => isset($_POST['activo']) ? 1 : 0
            ]);
            FlashHelper::set('success', 'Categoría actualizada correctamente.');
        } catch (\Exception $e) {
            FlashHelper::set('danger', 'Error al actualizar la categoría.');
        }

        header("Location: index.php?route=categorias");
        exit;
    }

    public function toggle(): void {
        AuthHelper::requireAuth();
        CsrfHelper::checkOrAbort();

        $id = (int)($_POST['id'] ?? 0);
        Categoria::toggleStatus($id);
        FlashHelper::set('info', 'Estado de la categoría actualizado.');
        header("Location: index.php?route=categorias");
        exit;
    }
}
