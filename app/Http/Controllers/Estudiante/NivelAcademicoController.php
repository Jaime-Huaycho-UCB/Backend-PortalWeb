<?php

namespace App\Http\Controllers\Estudiante;
use App\Http\Controllers\Controller;
use App\Models\Estudiante\NivelAcademico;
use Exception;
use Illuminate\Http\Request;
use PhpParser\Node\Stmt\TryCatch;

class NivelAcademicoController extends Controller{

    public function obtenerNivelAcademico(){
        try{
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
        } catch (Exception $e){
            return $this->Error($e);
        }
    }

    public function ingresarNivelAcademico(Request $request){
        try{
            $token = $request->input('token');
            $idUsuario = $request->input('idUsuario');
            if (!($this->tokenValido($idUsuario,$token))){
                return response()->json([
                    "salida" => false,
                    "mensaje" => $this->TOKEN_INVALIDO
                ],200);
            }

            $nivelAcademico = new NivelAcademico();
            $nivelAcademico->nombre = $request->input('nombre');
            $nivelAcademico->save();
            return response()->json([
                "salida" => true,
                "mensaje" => "Se ingreso exitosamente el nivel academico"
            ],200);
        }catch (Exception $e){
            return $this->Error($e);
        }
    }

    public function obtenerNombre($id){

        if ($id == null){
            return null;
        }

        $nombre = NivelAcademico::where('id','=',$id)->first();
        return $nombre['nombre'];
    }
}