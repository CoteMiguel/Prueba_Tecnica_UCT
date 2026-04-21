<?php
require_once __DIR__ . '/../models/Solicitud.php';

class SolicitudController {
    private $solicitudModel;

    public function __construct($conn) {
        $this->solicitudModel = new Solicitud($conn);
    }

    public function crearSolicitud() {
        $json = file_get_contents("php://input");
        $data = json_decode($json, true);

        $idUsuario   = $data['solicitanteId'] ?? null;
        $idTipo      = $data['tipo'] ?? null;
        $descripcion = $data['descripcion'] ?? null;
        $estado      = $data['estado'] ?? 'Pendiente';

        if (!$idUsuario || !$idTipo || !$descripcion) {
            echo json_encode(["success" => false, "msg" => "Campos obligatorios"]);
            return;
        }

        $ok = $this->solicitudModel->crear($idUsuario, $idTipo, $descripcion, $estado);

        if ($ok) {
            echo json_encode(["success" => true, "msg" => "Solicitud creada"]);
        } else {
            echo json_encode(["success" => false, "msg" => "Error al guardar"]);
        }
    }
}
