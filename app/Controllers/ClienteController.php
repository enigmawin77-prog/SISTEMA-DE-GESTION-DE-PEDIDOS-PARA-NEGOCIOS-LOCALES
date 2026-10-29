<?php
namespace App\Controllers;

use App\Helpers\AuthHelper;
use App\Helpers\CsrfHelper;
use App\Helpers\FlashHelper;
use App\Models\Cliente;
use App\Validators\Validator;

class ClienteController {
    public function index(): void {
        AuthHelper::requireAuth();

        $page = max(1, (int)($_GET['page'] ?? 1));
        $limit = 15;
        $offset = ($page - 1) * $limit;

        $filters = [
            'search' => $_GET['search'] ?? '',
            'activo' => $_GET['activo'] ?? ''
        ];

        $clientes = Cliente::getAll($filters, $limit, $offset);
        $total = Cliente::count($filters);
        $totalPages = ceil($total / $limit);

        require __DIR__ . '/../../views/clientes/index.php';
    }

    public function detalle(): void {
        AuthHelper::requireAuth();
        $id = (int)($_GET['id'] ?? 0);
        $cliente = Cliente::findById($id);

        if (!$cliente) {
            FlashHelper::set('danger', 'Cliente no encontrado.');
            header("Location: index.php?route=clientes");
            exit;
        }

        $pedidos = Cliente::getHistorialPedidos($id);
        require __DIR__ . '/../../views/clientes/detalle.php';
    }

    public function store(): void {
        AuthHelper::requireAuth();
        CsrfHelper::checkOrAbort();

        $validator = new Validator($_POST);
        $validator->required('nombre', 'Nombre completo');

        if (!$validator->isValid()) {
            if (isset($_POST['is_ajax'])) {
                echo json_encode(['success' => false, 'message' => $validator->getFirstError()]);
                exit;
            }
            FlashHelper::set('danger', $validator->getFirstError());
            header("Location: index.php?route=clientes");
            exit;
        }

        $id = Cliente::create([
            'tipo_documento'   => $_POST['tipo_documento'] ?? 'DNI',
            'numero_documento' => $_POST['numero_documento'] ?? null,
            'nombre'           => $_POST['nombre'],
            'telefono'         => $_POST['telefono'] ?? null,
            'email'            => $_POST['email'] ?? null,
            'direccion'        => $_POST['direccion'] ?? null,
            'referencia'       => $_POST['referencia'] ?? null,
            'notas'            => $_POST['notas'] ?? null,
            'activo'           => 1
        ]);

        if (isset($_POST['is_ajax'])) {
            $nuevoCliente = Cliente::findById($id);
            echo json_encode(['success' => true, 'cliente' => $nuevoCliente]);
            exit;
        }

        FlashHelper::set('success', 'Cliente registrado exitosamente.');
        header("Location: index.php?route=clientes");
        exit;
    }

    public function update(): void {
        AuthHelper::requireAuth();
        CsrfHelper::checkOrAbort();

        $id = (int)($_POST['id'] ?? 0);
        $validator = new Validator($_POST);
        $validator->required('nombre', 'Nombre completo');

        if (!$validator->isValid()) {
            FlashHelper::set('danger', $validator->getFirstError());
            header("Location: index.php?route=clientes");
            exit;
        }

        Cliente::update($id, [
            'tipo_documento'   => $_POST['tipo_documento'] ?? 'DNI',
            'numero_documento' => $_POST['numero_documento'] ?? null,
            'nombre'           => $_POST['nombre'],
            'telefono'         => $_POST['telefono'] ?? null,
            'email'            => $_POST['email'] ?? null,
            'direccion'        => $_POST['direccion'] ?? null,
            'referencia'       => $_POST['referencia'] ?? null,
            'notas'            => $_POST['notas'] ?? null,
            'activo'           => isset($_POST['activo']) ? 1 : 0
        ]);

        FlashHelper::set('success', 'Cliente actualizado exitosamente.');
        header("Location: index.php?route=clientes");
        exit;
    }

    public function searchJson(): void {
        AuthHelper::requireAuth();
        header('Content-Type: application/json');

        $query = $_GET['q'] ?? '';
        $clientes = Cliente::getAll(['search' => $query, 'activo' => 1], 15, 0);

        echo json_encode(['success' => true, 'clientes' => $clientes]);
        exit;
    }
}
