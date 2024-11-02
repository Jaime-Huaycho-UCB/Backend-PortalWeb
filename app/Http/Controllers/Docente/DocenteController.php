<?php

namespace App\Http\Controllers\Docente;
use App\Http\Controllers\Controller;
use App\Http\Controllers\FotoController;
use App\Http\Controllers\Docente\TituloController;
use App\Http\Controllers\UsuarioController;
use App\Models\Docente\Docente;
use App\Models\Docente\Titulo;
use Illuminate\Http\Request;


class DocenteController extends Controller{

    public function agregarDocente(Request $request){
        $fotoController = new FotoController();
        if (!($this->existe($request->input('correo')))){
            $idFoto = $fotoController->ingresarFoto($request->input('fotoBase64'));
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

    public function obtenerDocentes(Request $request){
        $docentes = Docente::where('Eliminado','=',0)
                            ->where('nombre','<>','SuperUsuario')->get();
        if ($docentes->isNotEmpty()){
            $docentesSalida = $this->llenarTituloFoto($docentes);
            $docentesSalida = $this->filtrarDocentesSinUsuario($docentesSalida);
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

    public function llenarTituloFoto($docentes){
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
                "foto" => $fotoController->obtenerFoto($docente['foto'])
            ];
            array_push($salidaDocentes,$temporal);
        }
        return $salidaDocentes;
    }

    public function eliminarDocente(Request $request){
        $id  = $request->input('docente');
        $docente = Docente::find($id);
        $docente->Eliminado = 1;
        $docente->save();
        return response()->json([
            "mensaje" => "Se elimino exitosamente al usuario"
        ],200);
    }

    public function actualizarDocente(Request $request){
        $id = $request->input('id');
        $fotoController = new FotoController();
        $docente = Docente::find($id);
        $idFoto = $fotoController->actualizarRuta($docente->foto,$request->input('foto'));
        $docente->nombre = $request->input('nombre');
        $docente->correo = $request->input('correo');
        $docente->titulo = $request->input('titulo');
        $docente->frase = $request->input('frase');
        $docente->foto = $idFoto;
        $docente->save();
        return response()->json([
            "mensaje" => "Se actualizo exitosamente el docente"
        ],200);
    }

    public function filtrarDocentesSinUsuario($docentes){
        $salida = array();
        foreach ($docentes as $docente){
            if (!($this->tieneUsuario($docente['id']))){
                array_push($salida,$docente);
            }
        }
        return $salida;
    }

    public function tieneUsuario($id){
        $usuarioController = new UsuarioController();
        if ($usuarioController->existeUsuario($id)){
            return true;
        }else{
            return false;
        }
    }

    public function obtenerDocente($id){
        $docente = Docente::find($id);
        return $docente;
    }

    public function obtenerDocentesTodo(Request $request){
        $docentes = Docente::where('Eliminado','=',0)
                            ->where('nombre','<>','SuperUsuario')->get();
        if ($docentes->isNotEmpty()){
            $docentesSalida = $this->llenarTituloFoto($docentes);
            return response()->json([
                "salida" => true,
                "docentes" => $docentesSalida
            ],200);
        }else{
            return response()->json([
                "salida" => false
            ],200);
        }
    }
}