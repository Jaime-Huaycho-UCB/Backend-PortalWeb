<?php

namespace App\Http\Controllers;
use App\Http\Controllers\Controller;
use App\Models\Pensum;
use Exception;
use Illuminate\Http\Request;


class PensumController extends Controller{

    public function obtenerPensums($opcion){
        if ($opcion==1){
            $pensums = Pensum::where('Eliminado','=',0)->get();
        }else if ($opcion==0){
            $pensums = Pensum::where('Eliminado','=',0)->where('estaActivo','=',1)->get();
        }
        if ($pensums->isNotEmpty()){
            $salidaPensums = array();
            foreach ($pensums as $pensum){
                $prePensum = [
                    "id" => $pensum['id'],
                    "nombre" => $pensum['nombre'],
                    "archivo" => $this->obtenerArchivo($pensum['archivo'],$pensum['tipo']),
                    "estaActivo" => $pensum['estaActivo']
                ];
                array_push($salidaPensums,$prePensum);
            }
            return response()->json([
                "salida" => true,
                "pensums" => $salidaPensums
            ],200);
        }else{
            return response()->json([
                "salida" => false,
                "mensaje" => "No hay pensums existentes"
            ],200);
        }
    }

    public function obtenerArchivo($archivoBlob,$tipo){
        $base64Content = base64_encode($archivoBlob);
        $archivoBase64 = "data:$tipo;base64,$base64Content";
        return $archivoBase64;
    }

    public function ingresarPensum(Request $request){

        $token = $request->input('token');
        $idUsuario = $request->input('idUsuario');
        if (!($this->tokenValido($idUsuario,$token))){
            return response()->json([
                "salida" => false,
                "mensaje" => $this->TOKEN_INVALIDO
            ],200);
        }

        try{
            $pensum = new Pensum();
            $archivoContenido = base64_decode($request->input('archivo'));
            $finfo = new \finfo(FILEINFO_MIME_TYPE);
            $archivoTipo = $finfo->buffer($archivoContenido);
            $pensum->nombre = $request->input('nombre');
            $pensum->archivo = $archivoContenido;
            $pensum->tipo = $archivoTipo;
            $pensum->estaActivo = 1;
            $pensum->save();
            return response()->json([
                "salida" => true,
                "mensaje" => "El pensum fue agregado exitosamente"
            ],200);
        } catch (Exception $e){
            return response()->json([
                "salida" => false,
                "mensaje" => "Error: {$e->getMessage()}"
            ],200);
        }
    }

    public function desactivarPensum(Request $request){
        return $this->camabiarActivacion($request,0);


    }
    public function activarPensum(Request $request){
        return $this->camabiarActivacion($request,1);
    }

    public function camabiarActivacion(Request $request,$activacion){
        
        $token = $request->input('token');
        $idUsuario = $request->input('idUsuario');
        if (!($this->tokenValido($idUsuario,$token))){
            return response()->json([
                "salida" => false,
                "mensaje" => $this->TOKEN_INVALIDO
            ],200);
        }

        try{
            $idPensum = $request->input('idPensum');
            $pensum = Pensum::find($idPensum);
            $pensum->estaActivo = $activacion;
            $pensum->save();
            $mensaje = ($activacion == 0) ? "desactivo" : "activo";
            return response()->json([
                "salida" => true,
                "mensaje" => "El pensum se $mensaje exitosamente"
            ],200);
        } catch (Exception $e){
            return response()->json([
                "salida" => false,
                "mensaje" => "Error: {$e->getMessage()}"
            ],200);
        }
    }

    public function eliminarPensum(Request $request){

        $token = $request->input('token');
        $idUsuario = $request->input('idUsuario');
        if (!($this->tokenValido($idUsuario,$token))){
            return response()->json([
                "salida" => false,
                "mensaje" => $this->TOKEN_INVALIDO
            ],200);
        }

        try{
            $idPensum = $request->input('idPensum');
            $pensum = Pensum::find($idPensum);
            $pensum->Eliminado = 1;
            $pensum->save();
            return response()->json([
                "salida" => true,
                "mensaje" => "El pensum se elimino exitosamente"
            ],200);
        } catch (Exception $e){
            return response()->json([
                "salida" => false,
                "mensaje" => "Error: {$e->getMessage()}"
            ],200);
        }
    }

    public function actualizarPensum(Request $request){

        $token = $request->input('token');
        $idUsuario = $request->input('idUsuario');
        if (!($this->tokenValido($idUsuario,$token))){
            return response()->json([
                "salida" => false,
                "mensaje" => $this->TOKEN_INVALIDO
            ],200);
        }

        try{
            $pensum = Pensum::find($request->input('idPensum'));
            $archivoContenido = base64_decode($request->input('archivo'));
            $finfo = new \finfo(FILEINFO_MIME_TYPE);
            $archivoTipo = $finfo->buffer($archivoContenido);
            $pensum->nombre = $request->input('nombre');
            $pensum->archivo = $archivoContenido;
            $pensum->tipo = $archivoTipo;
            $pensum->save();
            return response()->json([
                "salida" => true,
                "mensaje" => "El pensum fue actualizado exitosamente"
            ],200);
        } catch (Exception $e){
            return response()->json([
                "salida" => false,
                "mensaje" => "Error: {$e->getMessage()}"
            ],200);
        }
    }
}