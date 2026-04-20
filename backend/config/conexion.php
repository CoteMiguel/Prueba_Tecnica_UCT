<?php  
class Database {
    public static function conexion(){
        $serverName = "localhost";
        $conexion = [
            "Database" => "prueba_tecnica",
            "UID" => "UCT",
            "PWD" => "PWD1234-",
            "CharacterSet" => "UTF-8"
            ];
            $conn = sqlsrv_connect($serverName,$conexion);
            if(!$conn){
                die(print_r(sqlsrv_errors(),true));
            }
            return $conn;
    }
}