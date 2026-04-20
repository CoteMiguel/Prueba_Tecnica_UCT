<?php
require_once __DIR__ . '/../models/Tipo_Solicitud.php';
class TipoSolicitudController {
    private $tiposSolicitud;

    public function __construct($conn) {
        $this->tiposSolicitud = new TipoSolicitud($conn);
    }

    public function listarTipoSolicitud() {
        $tipos = $this->tiposSolicitud->tSolicitud();
        echo json_encode($tipos);
        exit;
    }
}
