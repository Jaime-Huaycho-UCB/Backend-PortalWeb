<?php

namespace App\Http\Controllers;

use Laravel\Lumen\Routing\Controller as BaseController;
use App\Models\Token;
use DateTime;
use Exception;

class Controller extends BaseController{

    public $TOKEN_INVALIDO = "TKIN";
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
                    'salida' => true,
                    'token' => $codigo
                ];
            }else{
                return [
                    'salida' => false,
                    'mensaje' => "no se actualizo el token"
                ];
            }
        }catch(Exception $e){
            return [
                'salida' => false,
                'mensaje' => $e->getMessage()
            ];
        }
    }

    public function inicializarToken($idUsuario){
        try{
            $token = new Token();
            $token->usuario = $idUsuario;
            $token->codigo = null;
            $token->creacion = null;
            $token->expiracion = null;
            $token->save();
            return true;
        } catch (Exception $e){
            return true;
        }
    }

    public function tokenValido($idUsuario,$token){
        $objetoToken = $this->obtenerToken($idUsuario);
        if ($objetoToken['codigo']==$token){
            $expiracionToken = new DateTime($objetoToken['expiracion']);
            $tiempoActual = new DateTime();
            if ($expiracionToken>=$tiempoActual){
                if ($this->extenderTiempoExpiracion($objetoToken['id'])){
                    return true;
                }else{
                    return false;
                }
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

    public function eliminarToken($id){
        $token = Token::where('usuario','=',$id)->update([
            "Eliminado" => 1
        ]);
    }

    private function extenderTiempoExpiracion($idToken){
        try{
            $tiempoActual = new DateTime();
            $tiempoActual->modify('+10 minutes');
            $token = Token::find($idToken);
            $token->expiracion = $tiempoActual;
            $token->save();
            return true;
        } catch (Exception $e){
            return false;
        }
    }

    public function Error(Exception $e){
        return response()->json([
            "salida" => false,
            "mensaje" => "Error: {$e->getMessage()}"
        ],400);
    }

    public function obtenerFechaActual(){
        $fecha = date('Y-m-d');
        return $fecha;
    }
}
