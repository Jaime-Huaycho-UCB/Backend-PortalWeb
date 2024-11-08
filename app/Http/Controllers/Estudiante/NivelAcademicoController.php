<?php

namespace App\Http\Controllers\Estudiante;
use App\Http\Controllers\Controller;
use App\Models\Estudiante\NivelAcademico;
use Exception;
use Illuminate\Http\Request;
use PhpParser\Node\Stmt\TryCatch;

class NivelAcademicoController extends Controller{

    public function obtenerNivelAcademico(){
        $niveles = NivelAcademico::where('Eliminado','=',0)->get();
        if ($niveles->isNotEmpty()){
            return response()->json([
                "salida" => true,
                "nivelesAcademicos" => $niveles
            ],200);
        }else{
            return response()->json([
                "salida" => false,
                "mensaje" => "No existen niveles academicos"
            ],200);
        }
    }

    public function ingresarNivelAcademico(Request $request){

        // $token = $request->input('token');
        // $idUsuario = $request->input('idUsuario');
        // if (!($this->tokenValido($idUsuario,$token))){
        //     return response()->json([
        //         "salida" => false,
        //         "mensaje" => $this->TOKEN_INVALIDO
        //     ],200);
        // }

        try{
            $nivelAcademico = new NivelAcademico();
            $nivelAcademico->nombre = $request->input('nombre');
            $nivelAcademico->save();
            return response()->json([
                "salida" => true,
                "mensaje" => "Se ingreso exitosamente el nivel academico"
            ],200);
        }catch (Exception $e){
            return response()->json([
                "salida" =>  false,
                "mensaje" => $e->getMessage()
            ],200);
        }
    }

    public function obtenerNombre(int $id){
        $nombre = NivelAcademico::where('id','=',$id)->first();
        return $nombre['nombre'];
    }
}