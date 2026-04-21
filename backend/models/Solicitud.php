
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
                t.id_tiso as id_tipo,
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
    public function actualizar($idSolicitud, $idTipo, $descripcion, $estado)
    {
        $sql = "UPDATE solicitudes 
            SET id_tiso = ?, descripcion = ?, estado = ?, fecha_actualizacion = GETDATE()
            WHERE id_solicitud = ?";
        $params = [$idTipo, $descripcion, $estado, $idSolicitud];
        $stmt = sqlsrv_query($this->conn, $sql, $params);

        return $stmt ? true : false;
    }
    public function filtrar($buscar, $estado, $tipo)
    {
        $sql = "SELECT s.id_solicitud, u.nombre AS solicitante, s.descripcion, s.estado, s.fecha_creacion, t.nombre AS tipo, t.id_tiso AS id_tipo
            FROM solicitudes s
            JOIN usuarios u ON s.id_usuario = u.id_usuario
            JOIN tipo_solicitud t ON s.id_tiso = t.id_tiso
            WHERE 1=1";

        $params = [];


        if (!empty($buscar)) {
            $sql .= " AND (u.nombre LIKE ? OR u.correo LIKE ?)";
            $params[] = "%$buscar%";
            $params[] = "%$buscar%";
        }
        if ($estado !== "") {
            $sql .= " AND s.estado = ?";
            $params[] = $estado;
        }
        if (!empty($tipo)) {
            $sql .= " AND s.id_tiso = ?";
            $params[] = $tipo;
        }

        $stmt = sqlsrv_query($this->conn, $sql, $params);
        $result = [];
        while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
            $result[] = $row;
        }

        return $result;
    }
}
