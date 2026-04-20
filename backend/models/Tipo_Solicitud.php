<?php
class TipoSolicitud
{
    private $conn;
    public function __construct($conexion)
    {
        $this->conn = $conexion;
    }
    public function tSolicitud()
    {
        $sql = "SELECT id_tiso, nombre FROM tipo_solicitud WHERE activo = '1'";
        $query = sqlsrv_query($this->conn, $sql);
        $tipos = [];

        while ($row = sqlsrv_fetch_array($query, SQLSRV_FETCH_ASSOC)) {
            $tipos[] = $row;
        }
        // error_log("tipos: " . print_r($tipos, true));
        return $tipos;
    }
}
