<?php

namespace App\Http\Controllers\Docente;
use App\Http\Controllers\Controller;
use App\Models\Docente\Titulo;
use Exception;
use Illuminate\Http\Request;


class TituloController extends Controller{

    public function obtenerTitulos(){
        try {
            $titulos = Titulo::where('Eliminado','=',0)->get();
            if ($titulos->isNotEmpty()){
                return response()->json([
                    "salida" => true,
                    "titulos" => $titulos
                ],200);
            }else{
                return response()->json([
                    "salida" => false,
                    "mensaje" => "No hay titulos disponobles"
                ],200);
            }
        } catch (Exception $e){
            return $this->Error($e);
        }
    }

    public function obtenerNombre($id){

        if ($id==null){
            return null;
        }

        $nombre = Titulo::where('id','=',$id)->first();
        return $nombre['nombre'];
    }

    public function obtenerId($nombre){
        $titulo = Titulo::where('nombre','=',$nombre)->first();
        return $titulo['id'];
    }
}