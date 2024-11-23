<?php

namespace App\Http\Controllers;
use App\Http\Controllers\Controller;
use App\Models\Publicacion;
use Exception;
use Illuminate\Http\Request;

class PublicacionController extends Controller{

    public function ingresarPublicacion(Request $request){
        try{
            $token = $request->input('token');
            $idUsuario = $request->input('idUsuario');
            if (!($this->tokenValido($idUsuario,$token))){
                return response()->json([
                    "salida" => false,
                    "mensaje" => $this->TOKEN_INVALIDO
                ],200);
            }

            $fotoController = new FotoController();
            $publicacion = new Publicacion();
            $publicacion->nombre = $request->input('nombre');
            $publicacion->foto = $fotoController->ingresarFoto($request->input('foto'));
            $publicacion->contenido = $request->input('contenido');
            $publicacion->redactor = $request->input('redactor');
            $publicacion->save();

            return response()->json([
                "salida" => true,
                "mensaje" => "Se agrego exitosamente la publicacion"
            ],200);
        }catch (Exception $e){
            return $this->Error($e);
        }
    }

    public function eliminarPublicacion(Request $request){
        try{
            $token = $request->input('token');
            $idUsuario = $request->input('idUsuario');
            if (!($this->tokenValido($idUsuario,$token))){
                return response()->json([
                    "salida" => false,
                    "mensaje" => $this->TOKEN_INVALIDO
                ],200);
            }

            $idPublicacion = $request->input('idPublicacion');
            $publicacion = Publicacion::find($idPublicacion);
            $publicacion->Eliminado = 1;
            $publicacion->save();

            return response()->json([
                "salida" => true,
                "mensaje" => "Se elimino la publicacion existosamente"
            ],200);
        }catch (Exception $e){
            return $this->Error($e);
        }
    }

    public function actualizarPublicacion(Request $request){
        try{
            $token = $request->input('token');
            $idUsuario = $request->input('idUsuario');
            if (!($this->tokenValido($idUsuario,$token))){
                return response()->json([
                    "salida" => false,
                    "mensaje" => $this->TOKEN_INVALIDO
                ],200);
            }

            $fotoController = new FotoController();
            $idPublicacion = $request->input('idPublicacion');
            $publicacion = Publicacion::find($idPublicacion);
            $publicacion->nombre = $request->input('nombre');
            if (($request->input('foto')) != null){
                $publicacion->foto = $fotoController->ingresarFoto($request->input('foto'));
            }
            $publicacion->contenido = $request->input('contenido');
            $publicacion->redactor = $request->input('redactor');
            $publicacion->save();

            return response()->json([
                "salida" => true,
                "mensaje" => "Se actualizo exitosamente la publicacion"
            ],200);
        }catch (Exception $e){
            return $this->Error($e);
        }
    }

    public function obtenerPublicaciones(){
        try{
            $publicaciones = Publicacion::where('Eliminado','=',0)->get();
            if ($publicaciones->isEmpty()){
                return response()->json([
                    "salida" => false,
                    "mensaje" => "No hay publicaiones disponibles"
                ],200);
            }

            $publicaciones = $this->llenarFoto($publicaciones);

            return response()->json([
                "salida" => true,
                "publicaciones" => $publicaciones
            ],200);
        } catch (Exception $e){
            return $this->Error($e);
        }
    }

    private function llenarFoto($publicaciones){
        try{
            $fotoController = new FotoController();
            $salida = array();
            foreach ($publicaciones as $publicacion){
                $preSalida = [
                    "id" => $publicacion['id'],
                    "nombre" => $publicacion['nombre'],
                    "foto" => $fotoController->obtenerFoto($publicacion['foto']),
                    "contenido" => $publicacion['contenido'],
                    "redactor" => $publicaciones['redactor']
                ];
                array_push($salida,$preSalida);
            }
            return $salida;
        } catch (Exception $e){
            return null;
        }
    }
}