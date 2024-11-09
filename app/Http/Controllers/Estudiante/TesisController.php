<?php

namespace App\Http\Controllers\Estudiante;
use App\Http\Controllers\Controller;
use App\Models\Estudiante\Tesis;
use Illuminate\Http\Request;
use App\Http\Controllers\Estudiante\EstudianteController;


class TesisController extends Controller{

    public function obtenerTesis($id){
        $tesis = Tesis::where('id','=',$id)->where('Eliminado','=',0)->first();
        if ($tesis){
            $tesis64Content = base64_encode($tesis['contenido']);
            $tipo = $$tesis->tipo; 
            $TesisBase64 = "data:$tipo;base64,$tesis64Content";
            return response()->json([
                "salida" => true,
                "titulo" => $tesis['titulo'],
                "tesis" => $TesisBase64,
                "fechaPublicacion" => $tesis['fechaPublicacion'],
                "resumen" => $tesis['resumen']
            ],200);
        }else{
            return response()->json([
                "salida" => false,
                "mensaje" => "No exite la tesis a encontrar"
            ],200);
        }
    }

    public function ingresarTesis(Request $request){

        $token = $request->input('token');
        $idUsuario = $request->input('idUsuario');
        if (!($this->tokenValido($idUsuario,$token))){
            return response()->json([
                "salida" => false,
                "mensaje" => $this->TOKEN_INVALIDO
            ],200);
        }

        $idEstudiante = $request->input('idEstudiante');
        $estudianteController = new EstudianteController;

        $titulo = $request->input('titulo');
        $fechaPublicacion = $request->input('fechaPublicacion');
        $tesisBase64 = $request->input('tesis');
        $resumen = $request->input('resumen');

        if ($tesisBase64!=null) {
            $tesisContenido = base64_decode($tesisBase64);
            $finfo = new \finfo(FILEINFO_MIME_TYPE);
            $tesisTipo = $finfo->buffer($tesisContenido);
            $tesis = new Tesis();
            $tesis->titulo = $titulo;
            $tesis->contenido = $tesisContenido;
            $tesis->tipo = $tesisTipo;
            $tesis->fechaPublicacion = $fechaPublicacion;
            $tesis->resumen = $resumen;
            $tesis->save();
            $idTesis = $tesis->id;
            
            if ($estudianteController->agregarTesis($idEstudiante,$idTesis)){
                return response()->json([
                    "salida" => true,
                    "mensaje" => " se inreso la tesisi exitosamente"
                ],200);
            }else{
                return response()->json([
                    "salida" => false,
                    "mensaje" => "Eror al enlazar la tesis"
                ],200);
            }
        }else{
            return response()->json([
                "salida" => false,
                "mensaje" => "Tiene que haber contenido"
            ],200);
        }
    }

    public function eliminarTesis(Request $request){

        $token = $request->input('token');
        $idUsuario = $request->input('idUsuario');
        if (!($this->tokenValido($idUsuario,$token))){
            return response()->json([
                "salida" => false,
                "mensaje" => $this->TOKEN_INVALIDO
            ],200);
        }

        $idEstudiante = $request->input('idEstudiante');
        $estudianteController = new EstudianteController;
        if ($estudianteController->eliminarTesis($idEstudiante)){
            $id = $request->input('idTesis');
            $tesis = Tesis::find($id);
            $tesis->Eliminado = 1;
            $tesis->save();
            return response()->json([
                "salida" => true,
                "mensaje" => "La tesis se elimino exitosamente"
            ],200);
        }else{
            return response()->json([
                "salida" => false,
                "mensaje" => "Error al intentar borrar una la tesis"
            ],200);
        }

        
    }
}