<?php

namespace App\Http\Controllers\Docente;
use App\Http\Controllers\Controller;
use App\Models\Docente\Titulo;
use Illuminate\Http\Request;


class TituloController extends Controller{

    public function obtenerTitulos(){
        $titulos = Titulo::all();
        if ($titulos->isNotEmpty()){
            return response()->json([
                "salida" => true,
                "titulos" => $titulos
            ],200);
        }else{
            return response()->json([
                "salida" => false
            ],200);
        }
    }

    public function obtenerNombre(int $id){
        $nombre = Titulo::where('id','=',$id)->first();
        return $nombre['nombre'];
    }
}