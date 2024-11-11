<?php

namespace App\Http\Controllers\Estudiante;
use App\Http\Controllers\Controller;
use App\Http\Controllers\FotoController;
use App\Models\Estudiante\Estudiante;
use Exception;
use Illuminate\Http\Request;

class EstudianteController extends Controller{

    public function obtenerEstudiantes(){
        $estudiantes = Estudiante::where('Eliminado','=',0)->get();
        if ($estudiantes->isNotEmpty()){
            $estudiantesSalida = $this->llenarDatos($estudiantes);
            return response()->json([
                "salida" => true,
                "estudiantes" => $estudiantesSalida
            ],200);
        }else{
            return response()->json([
                "salida" => false,
                "mensaje" => "No hay estudiantes registrados"
            ],200);
        }
    }

    public function llenarDatos($estudiantes){
        $nivelAcademicoController = new NivelAcademicoController();
        $fotoController = new FotoController();
        $salida = array();
        foreach ($estudiantes as $estudiante){
            $preSalida = [
                "id" => $estudiante['id'],
                "nombre" => $estudiante['nombre'],
                "nivelAcademico" => $nivelAcademicoController->obtenerNombre($estudiante['nivelAcademico']),
                "correo" => $estudiante['correo'],
                "tesis" => $estudiante['tesis'],
                "foto" => $fotoController->obtenerFoto($estudiante['foto'])
            ];
            array_push($salida,$preSalida);
        }
        return $salida;
    }

    public function agregarEstudiante(Request $request){

        $token = $request->input('token');
        $idUsuario = $request->input('idUsuario');
        if (!($this->tokenValido($idUsuario,$token))){
            return response()->json([
                "salida" => false,
                "mensaje" => $this->TOKEN_INVALIDO
            ],200);
        }

        $fotoController = new FotoController();

        if (!($this->existe($request->input('correo')))){
            $estudiante = new Estudiante();
            $estudiante->nombre = $request->input('nombre');
            $estudiante->nivelAcademico = $request->input('nivelAcademico');
            $estudiante->correo = $request->correo;
            $estudiante->tesis = null;
            $estudiante->foto = $fotoController->ingresarFoto($request->input('foto'));
            $estudiante->save();
            return response()->json([
                "salida" => true,
                "mensaje" => "Se agrego exitosamente el estudiante"
            ],200);
        }else{
            return response()->json([
                "salida" => false,
                "mensaje" => "El estudiante ya existe"
            ],200);
        }

    }

    public function existe(string $correo){
        $estudiante = Estudiante::where('correo','=',$correo)->first();
        if ($estudiante){
            return $estudiante;
        }else{
            return false;
        }
    }

    public function agregarTesis($idEstudiante,$idTesis){
        try{
            $estudiante = Estudiante::find($idEstudiante);
            $estudiante->tesis = $idTesis;
            $idEstudiante->save();
            return true;
        }catch (Exception $e){
            return false;
        }
    }

    public function eliminarTesis($idEstudiante){
        try{
            $estudiante = Estudiante::find($idEstudiante);
            $estudiante->tesis = null;
            $estudiante->save();
            return true;
        }catch (Exception $e){
            return false;
        }
    }
    public function actualizarEstudiante(Request $request){

        $token = $request->input('token');
        $idUsuario = $request->input('idUsuario');
        if (!($this->tokenValido($idUsuario,$token))){
            return response()->json([
                "salida" => false,
                "mensaje" => $this->TOKEN_INVALIDO
            ],200);
        }

        if (!($this->existe($request->input('correo')))){
            $idEstudiante = $request->input('idEstudiante');
            $estudiante = Estudiante::find($idEstudiante);
            $estudiante->nombre = $request->input('nombre');
            $estudiante->nivelAcademico = $request->input('nivelAcademico');
            $estudiante->correo = $request->correo;
            if ($request->input('foto')!=null){
                $fotoController = new FotoController();
                $estudiante->foto = $fotoController->ingresarFoto($request->input('foto'));
            }
            $estudiante->save();
            return response()->json([
                "salida" => true,
                "mensaje" => "Se actualizo exitosamente al estudiante"
            ],200);
        }else{
            return response()->json([
                "salida" => false,
                "mensaje" => "El estudiante ya existe"
            ],200);
        }
    }

    public function eliminarEstudiante(Request $request){
        $token = $request->input('token');
        $idUsuario = $request->input('idUsuario');
        if (!($this->tokenValido($idUsuario,$token))){
            return response()->json([
                "salida" => false,
                "mensaje" => $this->TOKEN_INVALIDO
            ],200);
        }
        
        try{
            $idEstudiante = $request->input('idEstudiante');
            $estudiante = Estudiante::find($idEstudiante);
            $estudiante->Eliminado = 1;
            $estudiante->save();
            return response()->json([
                "salida" => true,
                "mensaje" => "Se elimino al estudiante exitosamente"
            ],200);
        }catch (Exception $e){
            return response()->json([
                "salida" => false,
                "mensaje" => "Error: {$e->getMessage()}"
            ],200);
        }
    }
}