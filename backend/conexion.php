<?php  
    $serverName = "localhost";
    $conexion = array(
        "Database" => "prueba_tecnica",
        "UID" => "UCT",
        "PWD" => "PWD1234-",
        "CharacterSet" => "UTF-8"
    );
    $conn = sqlsrv_connect($serverName,$conexion);
    if($conn){
        echo "Conexion Exitosa";
    } else {
        echo "Error en la conexion";
        die(print_r(sqlsrv_errors(),true));
    }