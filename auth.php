<?php

require_once 'clases/auth.class.php';
require_once 'clases/respuestas.clases.php';

$_auth = new auth;
$_respuesta = new respuestas;

header('content-type: application/json; charset=utf-8');
header('Access-Control-Allow-Methods: GET, POST, PATCH, PUT, DELETE,');
header('Access-Control-Allow-Origin: *');

if($_SERVER['REQUEST_METHOD']=='POST'){

    //Recibir Datos
    $postBody = file_get_contents("php://input");


    //ENVIAR DATOS
    $dataArray = $_auth->login($postBody);

    //DEVOLVEMOS UNA RESPUESTA
    header('content-type: application/json; charset=utf-8');
    header('Access-Control-Allow-Methods: GET, POST, PATCH, PUT, DELETE,');
    header('Access-Control-Allow-Origin: *');
    if(isset($dataArray['result']['error_id'])){
        $responseCode = $dataArray['result']['error_id'];
        http_response_code($responseCode);
    }else{
        http_response_code(200);
    }
    echo json_encode($dataArray);
    
    
}else{
    header('content-type: application/json; charset=utf-8');
    header('Access-Control-Allow-Methods: GET, POST, PATCH, PUT, DELETE,');
    header('Access-Control-Allow-Origin: *');
    $dataArray = $_respuesta->error_405();
    echo json_encode($dataArray);
}




?>