<?php

namespace App\Http\Controllers;
use App\Http\Controllers\Controller;
use App\Models\Noticia;
use Illuminate\Http\Request;
use App\Http\Controllers\FotoController;
use Exception;
use Symfony\Component\Console\Input\Input;

class NoticiaController extends Controller{

    public function obtenerNoticias(){
        $fotoController = new FotoController();
        $noticias = Noticia::where('Eliminado','=',0)
                            ->orderBy('fechaPublicacion','desc')
                            ->get();
        if ($noticias->isNotEmpty()){
            $noticiasSalida = array();
            foreach ($noticias as $noticia){
                $fotoNoticia = null;
                $fotoRelleno = null;
                if ($noticia['fotoNoticia']!=null){
                    $fotoNoticia = $fotoController->obtenerFoto($noticia['fotoNoticia']);
                }
                if ($noticia['fotoRelleno']!=null){
                    $fotoRelleno = $fotoController->obtenerFoto($noticia['fotoRelleno']);
                }
                $noticiaLlena = [
                    "idNoticia" => $noticia['id'],
                    "titulo" => $noticia['titulo'],
                    "redactor" => $noticia['redactor'],
                    "noticia" => $noticia['noticia'],
                    "resumen" => $noticia['resumen'],
                    "fechaPublicacion" => $noticia['fechaPublicacion'],
                    "fotoNoticia" => $fotoNoticia,
                    "fotoRelleno" => $fotoRelleno
                ];
                array_push($noticiasSalida,$noticiaLlena);
            }
            return response()->json([
                "salida" => true,
                "noticias" => $noticiasSalida
            ],200);
        }else{
            return response()->json([
                "salida" => false,
                "mensaje" => "No hay noticias"
            ],200);
        }
    }

    public function ingresarNoticia(Request $request){

        $token = $request->input('token');
        $idUsuario = $request->input('idUsuario');
        if (!($this->tokenValido($idUsuario,$token))){
            return response()->json([
                "salida" => false,
                "mensaje" =>$this->TOKEN_INVALIDO
            ],200);
        }

        try{
            $fotoController = new FotoController();
            $fotoNoticia = $fotoController->ingresarFoto($request->input('fotoNoticia'));
            $fotoRelleno = $fotoController->ingresarFoto($request->input('fotoRelleno'));
            $noticia = new Noticia();
            $noticia->titulo = $request->input('titulo');
            $noticia->redactor = $request->input('redactor');
            $noticia->noticia = $request->input('noticia');
            $noticia->resumen = $request->input('resumen');
            $noticia->fechaPublicacion = $request->input('fechaPublicacion');
            $noticia->fotoNoticia = $fotoNoticia;
            $noticia->fotoRelleno = $fotoRelleno;
            $noticia->save();
            return response()->json([
                "salida" => true,
                "mensaje" => "La noticia se agrego exitosamente"
            ],200);
        }catch (Exception $e){
            return response()->json([
                "salida" => false,
                "mensaje" => $e->getMessage()
            ],200);
        }
    }

    public function actualizarNoticia(Request $request){

        $token = $request->input('token');
        $idUsuario = $request->input('idUsuario');
        if (!($this->tokenValido($idUsuario,$token))){
            return response()->json([
                "salida" => false,
                "mensaje" =>$this->TOKEN_INVALIDO
            ],200);
        }

        try{
            $idNoticia = $request->input('idNoticia');
            $fotoController = new FotoController();
            $noticia = Noticia::find($idNoticia);
            $noticia->titulo = $request->input('titulo');
            $noticia->redactor = $request->input('redactor');
            $noticia->noticia = $request->input('noticia');
            $noticia->resumen = $request->input('resumen');
            $noticia->fechaPublicacion = $request->input('fechaPublicacion');
            if ($request->input('fotoNoticia')!=null){
                $fotoNoticia = $fotoController->ingresarFoto($request->input('fotoNoticia'));
                $noticia->fotoNoticia = $fotoNoticia;
            }
            if ($request->input('fotoRelleno')!=null){
                $fotoRelleno = $fotoController->ingresarFoto($request->input('fotoRelleno'));
                $noticia->fotoRellano = $fotoRelleno;
            }
            
            
            $noticia->save();
            return response()->json([
                "salida" => true,
                "mensaje" => "La noticia se actualizo exitosamente"
            ],200);
        }catch (Exception $e){
            return response()->json([
                "salida" => false,
                "mensaje" => $e->getMessage()
            ],200);
        }
    }

    public function eliminarNoticia(Request $request){

        $token = $request->input('token');
        $idUsuario = $request->input('idUsuario');
        if (!($this->tokenValido($idUsuario,$token))){
            return response()->json([
                "salida" => false,
                "mensaje" =>$this->TOKEN_INVALIDO
            ],200);
        }

        try{
            $fotoController = new FotoController();
            $idNoticia = $request->input('idNoticia');
            $noticia = Noticia::find($idNoticia);
            $noticia->Eliminado = 1;
            if ($fotoController->eliminarFoto($noticia->fotoRelleno) && $fotoController->eliminarFoto($noticia->fotoNoticia)){
                $noticia->save();
                return response()->json([
                    "salida" => true,
                    "mensaje" => "Se elimino la noticia exitosamente"
                ],200);
            }else{
                return response()->json([
                    "salida" => false,
                    "mensaje" => "No se puedo eliminar la noticia"
                ],200);
            }
        } catch (Exception $e){
            return response()->json([
                "salida" => false,
                "mensaje" => "Error: {$e->getMessage()}"
            ],200);
        }
    }
}