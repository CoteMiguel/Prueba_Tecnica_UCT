<?php
require_once __DIR__ . '/../models/Solicitud.php';

class SolicitudController
{
    private $solicitudModel;

    public function __construct($conn)
    {
        $this->solicitudModel = new Solicitud($conn);
    }

    public function crearSolicitud()
    {
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
    public function listarSolicitudes()
    {
        $solicitudes = $this->solicitudModel->listar();
        echo json_encode($solicitudes);
    }
    public function actualizarSolicitud()
    {
        $json = file_get_contents("php://input");
        $data = json_decode($json, true);

        $idSolicitud = $data['id_solicitud'] ?? null;
        $idTipo      = $data['tipo'] ?? null;
        $descripcion = $data['descripcion'] ?? null;
        $estado      = $data['estado'] ?? null;

        if (!$idSolicitud || !$idTipo || !$descripcion || !$estado) {
            echo json_encode(["success" => false, "msg" => "Campos obligatorios"]);
            return;
        }

        $ok = $this->solicitudModel->actualizar($idSolicitud, $idTipo, $descripcion, $estado);

        echo json_encode(
            $ok
                ? ["success" => true, "msg" => "Solicitud actualizada"]
                : ["success" => false, "msg" => "Error al actualizar"]
        );
    }

    public function filtrarSolicitudes()
    {
        $json = file_get_contents("php://input");
        $data = json_decode($json, true);

        $buscar = $data['buscar'] ?? '';
        $estado = $data['estado'] ?? '';
        $tipo   = $data['tipo'] ?? '';

        $result = $this->solicitudModel->filtrar($buscar, $estado, $tipo);

        echo json_encode($result);
    }
}
