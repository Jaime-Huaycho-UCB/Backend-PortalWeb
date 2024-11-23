<?php

namespace App\Http\Controllers;
use App\Http\Controllers\Controller;
use App\Models\Contacto;
use Exception;
use Illuminate\Http\Request;


class ContactoController extends Controller{

    public function obtenerContactos(){
        try {   
            $contactos = Contacto::where('Eliminado','=',0)->get();
            if ($contactos->isNotEmpty()){
                return response()->json([
                    "salida" => true,
                    "contactos" => $contactos
                ],200);
            }else{
                return response()->json([
                    "salida" => false,
                    "mensaje" => "No hay contactos existentes"
                ],200);
            }
        } catch (Exception $e){
            return $this->Error($e);
        }
    }

    public function ingresarContacto(Request $request){
        try{
            $token = $request->input('token');
            $idUsuario = $request->input('idUsuario');
            if (!($this->tokenValido($idUsuario,$token))){
                return response()->json([
                    "salida" => false,
                    "mensaje" => $this->TOKEN_INVALIDO
                ],200);
            }

            if ($this->existeContacto($request->input('correo'))){
                return response()->json([
                    "salida" => false,
                    "mensaje" => "El contacto ingresado ya existe"
                ],200);
            }

            $contacto = new Contacto();
            $contacto->nombre = $request->input('nombre');
            $contacto->correo = $request->input('correo');
            $contacto->papel = $request->input('papel');
            $contacto->save();

            return response()->json([
                "salida" => true,
                "mensaje" => "El contacto fue agregado exitosamente"
            ],200);
        } catch (Exception $e){
            return $this->Error($e);
        }
    }

    public function existeContacto($correo){
        $contacto = Contacto::where('correo','=',$correo)->where('Eliminado','=',0)->first();
        if ($contacto){
            return true;
        }else{
            return false;
        }
    }

    public function eliminarContacto(Request $request){
        try{
            $token = $request->input('token');
            $idUsuario = $request->input('idUsuario');
            if (!($this->tokenValido($idUsuario,$token))){
                return response()->json([
                    "salida" => false,
                    "mensaje" => $this->TOKEN_INVALIDO
                ],200);
            }

            $idContacto = $request->input('idContacto');
            $contacto = Contacto::find($idContacto);
            $contacto->Eliminado = 1;
            $contacto->save();
            return response()->json([
                "salida" => true,
                "mensaje" => "El contacto fue eliminado exitosamente"
            ],200);
        }catch (Exception $e){
            return $this->Error($e);
        }
    }

    public function actualizarContacto(Request $request){
        try{
            $token = $request->input('token');
            $idUsuario = $request->input('idUsuario');
            if (!($this->tokenValido($idUsuario,$token))){
                return response()->json([
                    "salida" => false,
                    "mensaje" => $this->TOKEN_INVALIDO
                ],200);
            }

            $idContacto = $request->input('idContacto');
            $contacto = Contacto::find($idContacto);
            $contacto->nombre = $request->input('nombre');
            $contacto->correo = $request->input('correo');
            $contacto->papel = $request->input('papel');
            $contacto->save();
            return response()->json([
                "salida" => true,
                "mensaje" => "El contacto fue eliminado exitosamente"
            ],200);
        }catch (Exception $e){
            return $this->Error($e);
        }
    }
}