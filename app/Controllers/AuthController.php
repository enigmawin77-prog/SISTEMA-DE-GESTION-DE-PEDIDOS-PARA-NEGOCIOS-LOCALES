<?php
namespace App\Controllers;

use App\Models\Usuario;
use App\Helpers\AuthHelper;
use App\Helpers\CsrfHelper;
use App\Helpers\FlashHelper;
use App\Validators\Validator;

class AuthController {
    public function showLogin(): void {
        if (AuthHelper::check()) {
            header("Location: index.php?route=dashboard");
            exit;
        }
        require __DIR__ . '/../../views/auth/login.php';
    }

    public function login(): void {
        CsrfHelper::checkOrAbort();

        $email = trim($_POST['email'] ?? '');
        $password = trim($_POST['password'] ?? '');

        $validator = new Validator($_POST);
        $validator->required('email', 'Correo electrónico')
                  ->email('email', 'Correo electrónico')
                  ->required('password', 'Contraseña');

        if (!$validator->isValid()) {
            FlashHelper::set('danger', $validator->getFirstError());
            header("Location: index.php?route=login");
            exit;
        }

        $usuario = Usuario::findByEmail($email);

        if (!$usuario || !password_verify($password, $usuario['password'])) {
            FlashHelper::set('danger', 'Credenciales incorrectas o usuario inactivo.');
            header("Location: index.php?route=login");
            exit;
        }

        Usuario::updateLastLogin((int)$usuario['id']);
        AuthHelper::login($usuario);

        FlashHelper::set('success', '¡Bienvenido(a), ' . htmlspecialchars($usuario['nombre']) . '!');
        header("Location: index.php?route=dashboard");
        exit;
    }

    public function logout(): void {
        AuthHelper::logout();
        header("Location: index.php?route=login");
        exit;
    }

    public function perfil(): void {
        AuthHelper::requireAuth();
        $user = Usuario::findById(AuthHelper::id());
        require __DIR__ . '/../../views/auth/perfil.php';
    }

    public function updatePerfil(): void {
        AuthHelper::requireAuth();
        CsrfHelper::checkOrAbort();

        $userId = AuthHelper::id();
        $user = Usuario::findById($userId);

        $nombre = trim($_POST['nombre'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $telefono = trim($_POST['telefono'] ?? '');
        $password = trim($_POST['password'] ?? '');

        $validator = new Validator($_POST);
        $validator->required('nombre', 'Nombre completo')
                  ->required('email', 'Correo electrónico')
                  ->email('email', 'Correo electrónico');

        if (!$validator->isValid()) {
            FlashHelper::set('danger', $validator->getFirstError());
            header("Location: index.php?route=perfil");
            exit;
        }

        $data = [
            'nombre'   => $nombre,
            'email'    => $email,
            'telefono' => $telefono,
            'rol'      => $user['rol'],
            'activo'   => 1
        ];

        if (!empty($password)) {
            if (strlen($password) < 6) {
                FlashHelper::set('danger', 'La contraseña debe tener al menos 6 caracteres.');
                header("Location: index.php?route=perfil");
                exit;
            }
            $data['password'] = $password;
        }

        Usuario::update($userId, $data);
        $_SESSION['usuario_nombre'] = $nombre;
        $_SESSION['usuario_email'] = $email;

        FlashHelper::set('success', 'Perfil actualizado exitosamente.');
        header("Location: index.php?route=perfil");
        exit;
    }
}
