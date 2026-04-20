<?php
    class Usuario{
        private $conn;
        public function __construct($conexion)
        {
            $this->conn = $conexion;
        }
        public function login($correo, $passwd) {

            
            $sql = "SELECT 
                u.id_usuario,
                u.nombre,
                r.nombre AS rol,
                rm.puede_ver,
                rm.puede_crear,
                rm.puede_editar,
                rm.puede_eliminar
            FROM usuarios u
            JOIN usuarios_roles ur ON u.id_usuario = ur.id_usuario
            JOIN roles r ON ur.id_roles = r.id_roles
            JOIN roles_modulos rm ON r.id_roles = rm.id_roles
            JOIN modulos m ON rm.id_modulo = m.id_modulo
            WHERE u.correo = ?
            AND u.password = ?
            AND m.nombre = 'solicitudes'
            AND u.activo = '1'
            ";
            
            $parametros = [$correo, $passwd];
            

            
            $query = sqlsrv_query($this->conn, $sql, $parametros);
            
            if (!$query) {
                die(print_r(sqlsrv_errors(), true));
            }
            
            $result = sqlsrv_fetch_array($query, SQLSRV_FETCH_ASSOC);
            
            return $result;
        }
    }