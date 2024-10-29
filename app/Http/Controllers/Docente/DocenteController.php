<?php

namespace App\Http\Controllers\Docente;
use App\Http\Controllers\Controller;
use App\Http\Controllers\FotoController;
use App\Models\Docente\Docente;
use Illuminate\Http\Request;


class DocenteController extends Controller{

    public function agregarDocente(Request $request){
        $fotoController = new FotoController();
        if (!($this->existe($request->input('correo')))){
            $idFoto = $fotoController->ingresarFoto($request->input('ruta'));
            $docente = new Docente();
            $docente->nombre = $request->input('nombre');
            $docente->correo = $request->input('correo');
            $docente->titulo = $request->input('titulo');
            $docente->frase = $request->input('frase');
            $docente->foto = $idFoto;
            $docente->save();
            return response()->json([
                "salida" => true,
                "mensaje" => "Se agrego exitosamente el docente"
            ],200);
        }else{
            return response()->json([
                "salida" => false,
                "mensaje" => "El docente ya existe"
            ],400);
        }
    }

    public function existe(string $correo){
        $docente = Docente::where('correo','=',$correo)->first();
        if ($docente){
            return $docente;
        }else{
            return false;
        }
    }

    public function obtenerDocentes(){
        $docentes = Docente::all();
        if ($docentes->isNotEmpty()){
            $docentesSalida = $this->llenarTitulosRutas($docentes);
            return response()->json([
                "salida" => true,
                "docentes" => $docentesSalida
            ],200);
        }else{
            return response()->json([
                "salida" => false
            ],404);
        }
    }

    public function llenarTitulosRutas($docentes){
        $tituloController = new TituloController();
        $fotoController = new FotoController();
        $salidaDocentes = array();
        foreach ($docentes as $docente){
            $temporal = [
                "id" => $docente['id'],
                "nombre" => $docente['nombre'],
                "correo" => $docente['correo'],
                "titulo" => $tituloController->obtenerNombre($docente['titulo']),
                "frase" => $docente['frase'],
                "ruta" => $fotoController->obtenerRuta($docente['foto'])
            ];
            array_push($salidaDocentes,$temporal);
        }
        return $salidaDocentes;
    }
}