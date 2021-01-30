<?php
require_once 'conexion/conexion.php';

header('content-type: application/json; charset=utf-8');
header('Access-Control-Allow-Methods: GET, POST, PATCH, PUT, DELETE,');
header('Access-Control-Allow-Origin: *');

class token extends conexion{
    
    public function actualizarToken($fecha){
        $query = "UPDATE usuarios_token SET Estado = 'Inactivo' WHERE Fecha < '$fecha' AND Estado = 'Activo'";
        $verificar = parent::nonQuery($query);
        if($verificar > 0){
            return 1;
        }else{
            return 0;
        }
    }
}

?>