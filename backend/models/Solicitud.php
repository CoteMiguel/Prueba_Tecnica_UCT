
<?php
class Solicitud
{
    private $conn;

    public function __construct($conexion)
    {
        $this->conn = $conexion;
    }

    public function crear($idUsuario, $idTipo, $descripcion, $estado)
    {
        $sql = "INSERT INTO solicitudes (id_usuario, id_tiso, descripcion, estado, fecha_creacion, fecha_actualizacion)
                VALUES (?, ?, ?, ?, GETDATE(), GETDATE())";
        $params = [$idUsuario, $idTipo, $descripcion, $estado];
        $stmt = sqlsrv_query($this->conn, $sql, $params);

        if ($stmt) {
            return true;
        }
        return false;
    }
}
