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
        $noticias = Noticia::all();
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
            $noticia->fotoRellano = $fotoRelleno;
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
}