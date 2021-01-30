<?php

require_once 'conexion/conexion.php';
require_once 'respuestas.clases.php';

header('content-type: application/json; charset=utf-8');
header('Access-Control-Allow-Methods: GET, POST, PATCH, PUT, DELETE,');
header('Access-Control-Allow-Origin: *');

class auth extends conexion {

    public function login($json){
        $_respuesta = new respuestas;
        $datos = json_decode($json,true);
        if(!isset($datos['usuario']) || !isset($datos['password'])){
            //error
            return $_respuesta->error_400();
        }else{
            //todo esta bienn
            $usuario = $datos['usuario'];
            $password = $datos['password'];
            $password = parent::encriptar($password);
            $datos = $this->obtenerDatosUsuario($usuario);
            if($datos){
                //Verificar si la contraseña es igual
                if($password == $datos[0]['Password']){
                    if($datos[0]['Estado']== 'Activo'){
                        //Crear el token
                        $verficar = $this->insertarToken($datos[0]['UsuarioId']);
                        if($verficar){
                            //Si se Guardo
                            $result = $_respuesta->response;
                            $result['result']= array(
                                'token'=>$verficar
                            );
                            return $result;

                        }else{
                            //Error al guardar
                            return $_respuesta->error_500("El Interno, no hemos podido Guardar");
                        }
                    }else{
                        //El usuario esta inactivo
                        return $_respuesta->error_200("El usuario esta inactivo");
                    }

                }else{
                    return $_respuesta->error_200("El password es invalido");
                }


            }else{
                //No existe el usuario
                return $_respuesta->error_200("El usuario: $usuario no existe");
            }
        }

    }

    private function obtenerDatosUsuario($correo){
        $query = "SELECT UsuarioId,Password,Estado FROM usuarios WHERE usuario ='$correo'";
        $datos = parent::obtenerDatos($query);
        if(isset($datos[0]['UsuarioId'])){
            return $datos;
        }else{
            return 0;
        }
    }

    private function insertarToken($usuarioId){
        $val = true;
        $token = bin2hex(openssl_random_pseudo_bytes(16,$val));
        $date = date('Y-m-d H:i');
        $estado = 'Activo';
        $query = "INSERT INTO usuarios_token (UsuarioId,Token,Estado,Fecha) VALUES('$usuarioId','$token','$estado','$date')";
        $verifica = parent::nonQuery($query);
        if($verifica){
            return $token;
        }else{
            return false;
        }
    }
}

?>