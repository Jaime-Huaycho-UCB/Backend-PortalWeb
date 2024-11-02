<?php

namespace App\Http\Controllers;
use App\Http\Controllers\Controller;
use App\Models\Token;
use DateTime;
use Exception;
use Illuminate\Http\Request;


class TokenController extends Controller{

    public function crearToken($idUsuario){
        $tiempoActual = new DateTime();
        try{
            $codigo = $this->generarToken();
            $creacion = $tiempoActual->format('Y-m-d H:i:s');
            $tiempoActual->modify('+10 minutes');
            $expiracion = $tiempoActual->format('Y-m-d H:i:s');
            $respuesta = Token::where('usuario','=',$idUsuario)->
                                update([
                                    'codigo' => $codigo,
                                    'creacion' => $creacion,
                                    'expiracion' => $expiracion
                                ]);
            if ($respuesta){
                return [
                    'salida' => false,
                    'mensaje' => "Se creo correctamente el token",
                    'token' => $codigo
                ];
            }else{
                return [
                    'salida' => false,
                    'mensaje' => "no se actualizao el token"
                ];
            }
        }catch(Exception $e){
            return [
                'salida' => false,
                'mensaje' => $e->getMessage()
            ];
        }
    }

    public function tokenValido($idUsuario,$token){
        $objetoToken = $this->obtenerToken($idUsuario);
        if ($objetoToken['codigo']==$token){
            $expiracionToken = new DateTime($objetoToken['expiracion']);
            $tiempoActual = new DateTime();
            if ($expiracionToken>=$tiempoActual){
                return true;
            }else{
                return false;
            }
        }else{
            return false;
        }
    }

    public function obtenerToken($idUsuario){
        return Token::where('usuario','=',$idUsuario)->first();
    }

    public function generarToken($longitud = 50) {
        $bytes = random_bytes($longitud);
        return bin2hex($bytes);
    }
}