<?php

namespace App\Http\Controllers\Estudiante;
use App\Http\Controllers\Controller;
use App\Http\Controllers\FotoController;
use App\Models\Estudiante\Estudiante;
use Exception;
use Illuminate\Http\Request;

class EstudianteController extends Controller{

    public function agregarEstudiante(Request $request){

        // $token = $request->input('token');
        // $idUsuario = $request->input('idUsuario');
        // if (!($this->tokenValido($idUsuario,$token))){
        //     return response()->json([
        //         "salida" => false,
        //         "mensaje" => $this->TOKEN_INVALIDO
        //     ],200);
        // }

        $fotoController = new FotoController();

        if (!($this->existe($request->input('correo')))){
            $estudiante = new Estudiante();
            $estudiante->nombre = $request->input('nombre');
            $estudiante->nivelAcademico = $request->input('nivelAcedemico');
            $estudiante->correo = $request->correo;
            $estudiante->tesis = null;
            $estudiante->foto = $fotoController->ingresarFoto($request->input('foto'));
            $estudiante->save();
            return response()->json([
                "salida" => true,
                "mensaje" => "Se agrego exitosamente el docente"
            ],200);
        }else{
            return response()->json([
                "salida" => false,
                "mensaje" => "El estudiante ya existe"
            ],400);
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
}