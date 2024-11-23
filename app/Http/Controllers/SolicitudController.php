<?php

namespace App\Http\Controllers;
use App\Http\Controllers\Controller;
use App\Models\Solicitud;
use Exception;
use Illuminate\Http\Request;


class SolicitudController extends Controller{

    public function enviarSolicitud(Request $request){
        try{
            $solicitud = new Solicitud();
            $solicitud->nombres = $request->input('nombres');
            $solicitud->primerApellido = $request->input('primerApellido');
            $solicitud->segundoApellido = $request->input('segundoApellido');
            $solicitud->correo = $request->input('correo');
            $solicitud->telefono = $request->input('telefono');
            $solicitud->ciudad = $request->input('ciudad');
            $solicitud->mensaje = $request->input('mensaje');
            $solicitud->save();
            return response()->json([
                "salida" => true,
                "mensaje" => "Se envio la solicitud exitosamente"
            ],200);
        } catch (Exception $e){
            return $this->Error($e);
        }
    }

    public function obtenerSolicitudes(Request $request){
        try {
            $token = $request->input('token');
            $idUsuario = $request->input('idUsuario');
            if (!($this->tokenValido($idUsuario,$token))){
                return response()->json([
                    "salida" => false,
                    "mensaje" => $this->TOKEN_INVALIDO
                ],200);
            }

            $solicitudes = Solicitud::where('Eliminado','=',0)->get();
            if ($solicitudes->isNotEmpty()){
                return response()->json([
                    "salida" => true,
                    "solicitudes" => $solicitudes
                ],200);
            }else{
                return response()->json([
                    "salida" => false,
                    "mensaje" => "No hay solicitudes"
                ],200);
            }
        } catch (Exception $e){
            return $this->Error($e);
        }
    }

    public function eliminarSolicitud(Request $request){
        try{
            $token = $request->input('token');
            $idUsuario = $request->input('idUsuario');
            if (!($this->tokenValido($idUsuario,$token))){
                return response()->json([
                    "salida" => false,
                    "mensaje" => $this->TOKEN_INVALIDO
                ],200);
            }

            $idSolicitud = $request->input('idSolicitud');
            $solicitud = Solicitud::find($idSolicitud);
            $solicitud->Eliminado = 1;
            $solicitud->save();

            return response()->json([
                "salida" => true,
                "mensaje" => "La solicitud fue eliminada exitosamente"
            ],200);

        } catch (Exception $e){
            return $this->Error($e);
        }
    }
}