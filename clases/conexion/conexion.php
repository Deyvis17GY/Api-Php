<?php


class conexion {
   
   private  $server;
   private  $user;
   private  $password;
   private  $database;
   private  $port;
   private $conexion;

   function __construct()
   {
       $listadatos = $this->datosConexion();
       foreach($listadatos as $key => $value){
       $this->server      = $value['server'];
       $this->user  = $value['user'];
       $this->password  = $value['password'];
       $this->database  = $value['database'];
       $this->port  = $value['port'];
       }

       $this->conexion = new mysqli($this->server,$this->user,$this->password,$this->database,$this->port);
       if($this->conexion->connect_errno){
           echo "Algo salio mal";
           die();
           
       }
   }



   private function datosConexion(){
       $direction = dirname(__FILE__);
       $jsondata = file_get_contents($direction. "/" ."config");
       return json_decode($jsondata, true);
   }

   private function convertirUtf8($array){
       array_walk_recursive($array,function(&$item,$key){
           if(!mb_detect_encoding($item,'utf-8',true)){
               $item = utf8_encode($item);
           }
       });
       return $array;
   }

   public function obtenerDatos($query){
       $result = $this->conexion->query($query);
       $resultArray = array();
       foreach ($result as $key) {
           $resultArray[] = $key;
       }
       return $this->convertirUtf8($resultArray);
   }

   //INSERTAR NUEVOS CAMPOS
   public function nonQuery($query){
       $result = $this->conexion->query($query);
       return $this->conexion->affected_rows;
   }

   //NOS DEVUELVE EL ULTIMO ID INSERTADO
   public function nonQueryId($query){
    $result = $this->conexion->query($query);
    $filas = $this->conexion->affected_rows;
    if($filas >=1){
        return $this->conexion->insert_id;
    }else{
        return 0;
    }

   }

   //Encriptar
   protected function encriptar($string){
       return md5($string);
   }

   



}




?>