<?php
require_once __DIR__ . '/../models/Usuario.php';
class AuthController
{
    private $usuarioModel;

    public function __construct($conn)
    {
        $this->usuarioModel = new Usuario($conn);
    }
    public function login()
    {
        $correo = $_POST['correo'] ?? '';
        $passwd = $_POST['passwd'] ?? '';

        if (!$correo || !$passwd) {
            echo json_encode(["success" => false, "msg" => "Campos obligatorios"]);
            return;
        }
        $user = $this->usuarioModel->login($correo, $passwd);

        if ($user) {
            $_SESSION['usuario'] = [
                "id" => $user['id_usuario'],
                "nombre" => $user['nombre'],
                "rol" => $user['rol'],
                "permisos" => [
                    "ver" => $user['puede_ver'],
                    "crear" => $user['puede_crear'],
                    "editar" => $user['puede_editar'],
                    "eliminar" => $user['puede_eliminar']
                ]
            ];
            echo json_encode(["success" => true]);
        } else {
            echo json_encode(["success" => false, "msg" => "Credenciales inválidas"]);
        }
    }
    public function logout()
    {
        session_destroy();
        echo json_encode(["success" => true]);
    }
}
