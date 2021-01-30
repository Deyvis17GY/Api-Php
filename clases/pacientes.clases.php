<?php
require_once 'conexion/conexion.php';
require_once 'respuestas.clases.php';

header('content-type: application/json; charset=utf-8');
header('Access-Control-Allow-Methods: GET, POST, PATCH, PUT, DELETE,');
header('Access-Control-Allow-Origin: *');

class pacientes extends conexion {
    private $table="pacientes";
    private $pacienteId = "";
    private $dni = "";
    private $nombre ="";
    private $direccion ="";
    private $codigoPostal ="";
    private $genero = "";
    private $telefono ="";
    private $fechaNacimiento ="0000-00-00";
    private $correo ="";
    private $token ="";
    private $imagen="";
    
    //685d6d5d054c459f4be7fa9395749468
    //LISTAR PACIENTE
    public function listaPacientes($pagina =1){
        $inicio = 0;
        $cantidad = 100;
        if($pagina>1){
            $inicio = ($cantidad*($pagina-1))+1;
            $cantidad = $cantidad * $pagina;
        }
        $query = "SELECT PacienteId,Nombre,DNI,Telefono,Correo FROM " . $this->table . " limit $inicio,$cantidad";
        $datos = parent::obtenerDatos($query);
        return ($datos);
    }
    public function obtenerPaciente($id){
        $query = "SELECT * FROM " .$this->table ." WHERE PacienteId = '$id'";
        return parent::obtenerDatos($query);  
    }

    //INSERT PACIENTE
    public function post($json){
        $_respuesta = new respuestas;
        $datos = json_decode($json,true);
        if(!isset($datos['token'])){
            return $_respuesta->error_401();
        }else{
            $this->token = $datos['token'];
            $arrayToken = $this->buscarToken();
            if($arrayToken){
                if(!isset($datos['nombre']) || !isset($datos['dni']) || !isset($datos['correo'])){
                    return $_respuesta->error_400();
                }else{
                    $this->nombre = $datos['nombre'];
                    $this->dni = $datos['dni'];
                    $this->correo = $datos['correo'];
                    if(isset($datos['telefono'])){ $this->telefono = $datos['telefono'];}
                    if(isset($datos['direccion'])){ $this->direccion = $datos['direccion'];}
                    if(isset($datos['codigoPostal'])){ $this->codigoPostal = $datos['codigoPostal'];}
                    if(isset($datos['genero'])){ $this->genero = $datos['genero'];}
                    if(isset($datos['fechaNacimiento'])){ $this->fechaNacimiento = $datos['fechaNacimiento'];}

                    if(isset($datos['imagen'])){
                        $resp = $this->procesarImagen($datos['imagen']);
                        $this->imagen = $resp;
                        
                    }

                    $res = $this->insertPaciente();
                    if($res){
                        $respuesta = $_respuesta->response;
                        $respuesta["result"] = array(
                            "pacienteId" => $res
                        );
                        return $respuesta;
                    }else{
                        return $_respuesta->error_500();
                    }
                }
            }else{
                return $_respuesta->error_401("El token es invalido o esta caducado");
            }
        }


        
    }

    private function procesarImagen($img){
        $direccion = dirname(__DIR__) . "\public\imagenes\\";
        $partes = explode(";base64,",$img);
        $extension = explode("/",mime_content_type($img))[1];
        $imagen_base64 = base64_decode($partes[1]);
        $file = $direccion . uniqid() . "." . $extension;
        file_put_contents($file,$imagen_base64);
 
        $nuevadireccion = str_replace('\\','/',$file);
        return $nuevadireccion;
    }
    
    
    private function insertPaciente(){
        $query = "INSERT INTO " . $this->table . "(DNI,Nombre,Direccion,CodigoPostal,Telefono,Genero,FechaNacimiento,Correo,Imagen)
        values
        ('" . $this->dni . "','" . $this->nombre . "','" . $this->direccion . "','"  . $this->codigoPostal . "','"  . $this->telefono . "','"  . $this->genero . "','"  . $this->fechaNacimiento . "','"  . $this->correo . "','" . $this->imagen . "')"; 
        $resp = parent::nonQueryId($query);
        if($resp){
            return $resp;
        }else{
            return 0;
        }
    }

    //ACRUALIZAR PACIENTE
    public function put($json){
        $_respuesta = new respuestas;
        $datos = json_decode($json,true);

        if(!isset($datos['token'])){
            return $_respuesta->error_401();
        }else{
            $this->token = $datos['token'];
            $arrayToken = $this->buscarToken();
            if($arrayToken){
                if(!isset($datos['pacienteId']) ){
                    return $_respuesta->error_400();
                }else{
                    $this->pacienteId= $datos['pacienteId'];
                    if(isset($datos['nombre'])){ $this->nombre = $datos['nombre'];}
                    if(isset($datos['dni'])){ $this->dni = $datos['dni'];}
                    if(isset($datos['correo'])){ $this->correo = $datos['correo'];}
                    if(isset($datos['telefono'])){ $this->telefono = $datos['telefono'];}
                    if(isset($datos['direccion'])){ $this->direccion = $datos['direccion'];}
                    if(isset($datos['codigoPostal'])){ $this->codigoPostal = $datos['codigoPostal'];}
                    if(isset($datos['genero'])){ $this->genero = $datos['genero'];}
                    if(isset($datos['fechaNacimiento'])){ $this->fechaNacimiento = $datos['fechaNacimiento'];}
                    $res = $this->modificarPaciente();
                    if($res){
                        $respuesta = $_respuesta->response;
                        $respuesta["result"] = array(
                            "pacienteId" => $this->pacienteId
                        );
                        return $respuesta;
                    }else{
                        return $_respuesta->error_500();
                    }
                }
            }else{
                return $_respuesta->error_401("El token es invalido o esta caducado");
            }
        }


       
    }

    private function modificarPaciente(){
        $query = "UPDATE " .$this->table . " SET Nombre='" . $this->nombre ."',Direccion ='" . $this->direccion . "',
        DNI = '" . $this->dni ."',CodigoPostal='" . $this->codigoPostal ."', Telefono ='" . $this->telefono . "',Genero = '" . $this->genero .
        "',fechaNacimiento = '" . $this->fechaNacimiento . "',Correo = '" . $this->correo . 
        "' WHERE PacienteId = '" . $this->pacienteId . "'";
      
        $resp = parent::nonQuery($query);
        if($resp >=1){
            return $resp;
        }else{
            return 0;
        }
    }

    //METODO DELETE
    public function delete($json){
        $_respuesta = new respuestas;
        $datos = json_decode($json,true);

        if(!isset($datos['token'])){
            return $_respuesta->error_401();
        }else{
            $this->token = $datos['token'];
            $arrayToken = $this->buscarToken();
            if($arrayToken){
                if(!isset($datos['pacienteId']) ){
                    return $_respuesta->error_400();
                }else{
                    $this->pacienteId= $datos['pacienteId'];
                    $res = $this->eliminarPaciente();
                    if($res){
                        $respuesta = $_respuesta->response;
                        $respuesta["result"] = array(
                            "pacienteId" => $this->pacienteId
                        );
                        return $respuesta;
                    }else{
                        return $_respuesta->error_500();
                    }
                }
            }else{
                return $_respuesta->error_401("El token es invalido o esta caducado");
            }
        }


        
    }

    private function eliminarPaciente(){
        $query = "DELETE FROM " . $this->table . " WHERE PacienteId = '" . $this->pacienteId . "'";
        $resp = parent::nonQuery($query);
        if($resp >= 1){
            return $resp;
        }else{
            return 0;
        }
    }

    private function buscarToken(){
        $query = "SELECT TokenId,UsuarioId,Estado FROM usuarios_token WHERE Token = '" . $this->token . "' AND Estado = 'Activo'";
        $resp = parent::obtenerDatos($query);
        if($resp){
            return $resp;
        }else{
            return 0;
        }
    }

    private function actualizarToken($tokenId){
        $date = date("Y-m-d H:1");
        $query = "UPDATE usuarios_token SET fecha = '$date' WHERE TokenId = '$tokenId' ";
        $resp = parent::nonQuery($query);
        if($resp){
            return $resp;
        }else{
            return 0;
        }
    }

}

?>