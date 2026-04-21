<?php
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/debug.log');

session_start();
require_once __DIR__ . '/config/conexion.php';
require_once __DIR__ . '/controllers/AuthController.php';
require_once __DIR__ . '/controllers/TipoSolicitudController.php';
require_once __DIR__ . '/controllers/Solicitudcontroller.php';

$conn = Database::conexion();

$controllerAuth = new AuthController($conn);
$controllerTipo = new TipoSolicitudController($conn);
$solicitudController = new Solicitudcontroller($conn);
$action = $_GET['action'] ?? '';

switch ($action) {

    case 'login':
        $controllerAuth->login();
        break;

    case 'logout':
        $controllerAuth->logout();
        break;

    case 'listarTipos':
        $controllerTipo->listarTipoSolicitud();
        break;
    case 'crearSolicitud':
        $solicitudController->crearSolicitud();
        break;

    default:
        echo json_encode(["msg" => "Ruta no válida"]);
}
