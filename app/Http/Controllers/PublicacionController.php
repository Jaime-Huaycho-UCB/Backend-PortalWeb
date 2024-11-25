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
            $publicacion->numero = $request->input('numero');
            $publicacion->nombre = $request->input('nombre');
            $publicacion->foto = $fotoController->ingresarFoto($request->input('foto'));
            $publicacion->fechaPublicacion = $this->obtenerFechaActual();
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
            $publicacion->numero = $request->input('numero');
            $publicacion->nombre = $request->input('nombre');
            if (($request->input('foto')) != null){
                $publicacion->foto = $fotoController->ingresarFoto($request->input('foto'));
            }
            $publicacion->fechaPublicacion = $this->obtenerFechaActual();
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

    public function obtenerPublicaciones($numero){
        try{
            if ($numero==0){
                $publicaciones = Publicacion::where('Eliminado','=',0)
                                        ->orderBy('fechaPublicacion','asc')
                                        ->get();
            }else{
                $publicaciones = Publicacion::where('Eliminado','=',0)
                                        ->where('numero','=',$numero)
                                        ->orderBy('fechaPublicacion','asc')
                                        ->get();
            }
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
                    "numero" => $publicacion['numero'],
                    "nombre" => $publicacion['nombre'],
                    "foto" => $fotoController->obtenerFoto($publicacion['foto']),
                    "fechaPublicacion" => $publicacion['fechaPublicacion'],
                    "contenido" => $publicacion['contenido'],
                    "redactor" => $publicacion['redactor']
                ];
                array_push($salida,$preSalida);
            }
            return $salida;
        } catch (Exception $e){
            return null;
        }
    }
}