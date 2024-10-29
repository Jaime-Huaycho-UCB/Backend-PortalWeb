<?php

namespace App\Http\Controllers\Docente;
use App\Http\Controllers\Controller;
use App\Http\Controllers\FotoController;
use App\Models\Docente\Docente;
use Illuminate\Http\Request;


class DocenteController extends Controller{

    public function agregarDocente(Request $request){
        $fotoController = new FotoController();
        $idFoto = $fotoController->ingresarFoto($request->input('ruta'));
        $docente = new Docente();
        $docente->nombre = $request->input('nombre');
        $docente->correo = $request->input('correo');
        $docente->titulo = $request->input('titulo');
        $docente->frase = $request->input('frase');
        $docente->foto = $idFoto;
        $docente->save();
        return response()->json([
            "mensaje" => "Se agrego exitosamente el docente"
        ],200);
    }

    public function obtenerDocentes(){
        $docentes = Docente::all();
        if ($docentes->isNotEmpty()){
            return response()->json([
                "salida" => true,
                "docentes" => $docentes
            ],200);
        }else{
            return response()->json([
                "salida" => false
            ],404);
        }
    }
}