<?php
header('content-type: application/json; charset=utf-8');
header('Access-Control-Allow-Methods: GET, POST, PATCH, PUT, DELETE,');
header('Access-Control-Allow-Origin: *');

class respuestas{
    public $response = [
        'status' => "ok",
        "result" => array()
    ];

    public function error_405(){
        $this->response['status']= "error";
        $this->response['result']= array(
            "error_id" => "405",
            "error_msg" => "Metodo no permitido"
        );
        return $this->response;
    }

    public function error_200($string = "Datos Incorrectos"){
        $this->response['status']= "error";
        $this->response['result']= array(
            "error_id" => "200",
            "error_msg" => $string
        );
        return $this->response;
    }

    public function error_400(){
        $this->response['status']= "error";
        $this->response['result']= array(
            "error_id" => "400",
            "error_msg" => "Datos enviados Incompletos con formato null"
        );
        return $this->response;
    }

    public function error_500($value ="Error interno del Servidor"){
        $this->response['status']= "error";
        $this->response['result']= array(
            "error_id" => "500",
            "error_msg" => $value
        );
        return $this->response;
    }

    public function error_401($value ="No autorizado, Token Invalido"){
        $this->response['status']= "error";
        $this->response['result']= array(
            "error_id" => "401",
            "error_msg" => $value
        );
        return $this->response;
    }





}

?>