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
            $solicitud->fechaNacimiento = $request->input('fechaNacimiento');
            $solicitud->telefono = $request->input('telefono');
            $solicitud->codigoPostal = $request->input('codigoPostal');
            $solicitud->ciudad = $request->input('ciudad');
            $solicitud->escuelaProcedencia = $request->input('escuelaProcedencia');
            $solicitud->save();
            return response()->json([
                "salida" => true,
                "mensaje" => "Se envio la solicitud exitosamente"
            ],200);
        } catch (Exception $e){
            return $this->Error($e);
        }
    }

    public function obtenerSolicitudes(){
        try {
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
}