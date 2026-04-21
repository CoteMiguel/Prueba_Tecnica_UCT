
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
    public function listar()
    {
        $sql = "SELECT 
                s.id_solicitud,
                u.nombre + ' ' + u.apellido_paterno AS solicitante,
                t.nombre AS tipo,
                s.descripcion,
                s.estado,
                FORMAT(s.fecha_creacion, 'dd-MM-yyyy') AS fecha_creacion
            FROM solicitudes s
            JOIN usuarios u ON s.id_usuario = u.id_usuario
            JOIN tipo_solicitud t ON s.id_tiso = t.id_tiso
            ORDER BY s.fecha_creacion DESC";
        $stmt = sqlsrv_query($this->conn, $sql);

        $result = [];
        while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
            $result[] = $row;
        }
        return $result;
    }
}
