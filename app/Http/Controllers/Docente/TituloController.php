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
}