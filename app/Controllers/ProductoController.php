<?php
namespace App\Controllers;

use App\Helpers\AuthHelper;
use App\Helpers\CsrfHelper;
use App\Helpers\FlashHelper;
use App\Helpers\UploadHelper;
use App\Models\Producto;
use App\Models\Categoria;
use App\Validators\Validator;

class ProductoController {
    public function index(): void {
        AuthHelper::requireAuth();

        $page = max(1, (int)($_GET['page'] ?? 1));
        $limit = 12;
        $offset = ($page - 1) * $limit;

        $filters = [
            'search'       => $_GET['search'] ?? '',
            'categoria_id' => $_GET['categoria_id'] ?? '',
            'activo'       => $_GET['activo'] ?? '',
            'stock_bajo'   => $_GET['stock_bajo'] ?? ''
        ];

        $productos = Producto::getAll($filters, $limit, $offset);
        $total = Producto::count($filters);
        $totalPages = ceil($total / $limit);
        $categorias = Categoria::getAll(true);

        require __DIR__ . '/../../views/productos/index.php';
    }

    public function store(): void {
        AuthHelper::requireAuth();
        CsrfHelper::checkOrAbort();

        $validator = new Validator($_POST);
        $validator->required('codigo', 'Código')
                  ->required('nombre', 'Nombre')
                  ->required('categoria_id', 'Categoría')
                  ->required('precio_venta', 'Precio de venta')
                  ->numeric('precio_venta', 'Precio de venta', 0.01)
                  ->required('stock', 'Stock')
                  ->numeric('stock', 'Stock', 0);

        if (!$validator->isValid()) {
            FlashHelper::set('danger', $validator->getFirstError());
            header("Location: index.php?route=productos");
            exit;
        }

        // Validar código duplicado
        if (Producto::findByCodigo($_POST['codigo'])) {
            FlashHelper::set('danger', 'El código de producto ya se encuentra registrado.');
            header("Location: index.php?route=productos");
            exit;
        }

        $imagen = null;
        if (!empty($_FILES['imagen']['name'])) {
            try {
                $imagen = UploadHelper::uploadProductImage($_FILES['imagen']);
            } catch (\Exception $e) {
                FlashHelper::set('danger', $e->getMessage());
                header("Location: index.php?route=productos");
                exit;
            }
        }

        $data = [
            'codigo'        => $_POST['codigo'],
            'nombre'        => $_POST['nombre'],
            'descripcion'   => $_POST['descripcion'] ?? '',
            'categoria_id'  => (int)$_POST['categoria_id'],
            'precio_venta'  => (float)$_POST['precio_venta'],
            'costo_compra'  => (float)($_POST['costo_compra'] ?? 0),
            'stock'         => (int)$_POST['stock'],
            'stock_minimo'  => (int)($_POST['stock_minimo'] ?? 5),
            'imagen'        => $imagen,
            'activo'        => isset($_POST['activo']) ? 1 : 0
        ];

        Producto::create($data);
        FlashHelper::set('success', 'Producto registrado exitosamente.');
        header("Location: index.php?route=productos");
        exit;
    }

    public function update(): void {
        AuthHelper::requireAuth();
        CsrfHelper::checkOrAbort();

        $id = (int)($_POST['id'] ?? 0);
        $producto = Producto::findById($id);

        if (!$producto) {
            FlashHelper::set('danger', 'Producto no encontrado.');
            header("Location: index.php?route=productos");
            exit;
        }

        $validator = new Validator($_POST);
        $validator->required('codigo', 'Código')
                  ->required('nombre', 'Nombre')
                  ->required('categoria_id', 'Categoría')
                  ->required('precio_venta', 'Precio de venta')
                  ->numeric('precio_venta', 'Precio de venta', 0.01)
                  ->required('stock', 'Stock')
                  ->numeric('stock', 'Stock', 0);

        if (!$validator->isValid()) {
            FlashHelper::set('danger', $validator->getFirstError());
            header("Location: index.php?route=productos");
            exit;
        }

        // Validar código duplicado para otro producto
        if (Producto::findByCodigo($_POST['codigo'], $id)) {
            FlashHelper::set('danger', 'El código ya pertenece a otro producto.');
            header("Location: index.php?route=productos");
            exit;
        }

        $imagen = null;
        if (!empty($_FILES['imagen']['name'])) {
            try {
                $imagen = UploadHelper::uploadProductImage($_FILES['imagen']);
            } catch (\Exception $e) {
                FlashHelper::set('danger', $e->getMessage());
                header("Location: index.php?route=productos");
                exit;
            }
        }

        $data = [
            'codigo'        => $_POST['codigo'],
            'nombre'        => $_POST['nombre'],
            'descripcion'   => $_POST['descripcion'] ?? '',
            'categoria_id'  => (int)$_POST['categoria_id'],
            'precio_venta'  => (float)$_POST['precio_venta'],
            'costo_compra'  => (float)($_POST['costo_compra'] ?? 0),
            'stock'         => (int)$_POST['stock'],
            'stock_minimo'  => (int)($_POST['stock_minimo'] ?? 5),
            'imagen'        => $imagen,
            'activo'        => isset($_POST['activo']) ? 1 : 0
        ];

        Producto::update($id, $data);
        FlashHelper::set('success', 'Producto actualizado exitosamente.');
        header("Location: index.php?route=productos");
        exit;
    }

    public function toggle(): void {
        AuthHelper::requireAuth();
        CsrfHelper::checkOrAbort();

        $id = (int)($_POST['id'] ?? 0);
        Producto::toggleStatus($id);
        FlashHelper::set('info', 'Estado del producto actualizado.');
        header("Location: index.php?route=productos");
        exit;
    }

    public function getJson(): void {
        AuthHelper::requireAuth();
        header('Content-Type: application/json');

        $id = (int)($_GET['id'] ?? 0);
        $producto = Producto::findById($id);

        if (!$producto) {
            http_response_code(404);
            echo json_encode(['success' => false, 'message' => 'Producto no encontrado']);
            exit;
        }

        echo json_encode(['success' => true, 'producto' => $producto]);
        exit;
    }
}
