<?php

namespace App\Http\Controllers\Estudiante;
use App\Http\Controllers\Controller;
use App\Models\Estudiante\Tesis;
use Illuminate\Http\Request;
use App\Http\Controllers\Estudiante\EstudianteController;
use Exception;

class TesisController extends Controller{

    public function obtenerTesises(){
        try {
            $tesises = Tesis::where('Eliminado','=',0)->get();
            if ($tesises->isNotEmpty()){
                $estudianteController = new EstudianteController();
                $salida = array();
                foreach ($tesises as $tesis){
                    $salidaTesis = [
                        "id" => $tesis['id'],
                        "titulo" => $tesis['titulo'],
                        "fechaPublicacion" => $tesis['fecha_publicacion'],
                        "resumen" => $tesis['resumen']
                    ];
                    $salidaEstudiante = $estudianteController->obtenerEstudiantePorTesis($tesis['id']);
                    array_push($salida,[
                        "tesis" => $salidaTesis,
                        "estudiante" => $salidaEstudiante
                    ]);
                }
                
                return response()->json([
                    "salida" => true,
                    "tesises" => $salida
                ],200);
            }else{
                return response()->json([
                    "salida" => false,
                    "mensaje" => "No exite la tesis a encontrar"
                ],200);
            }
        } catch (Exception $e){
            return $this->Error($e);
        }
    }

    public function obtenerContenido($idTesis){
        try{
            $tesis = Tesis::find($idTesis);
            $tesis64Content = base64_encode($tesis->contenido);
            $tipo = $tesis->tipo; 
            $TesisBase64 = "data:$tipo;base64,$tesis64Content";
            return response()->json([
                "salida" => true,
                "contenido" => $TesisBase64
            ],200);
        } catch(Exception $e){
            return $this->Error($e);
        }
    }

    public function ingresarTesis(Request $request){
        try {
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
                $tesis->fecha_publicacion = $fechaPublicacion;
                $tesis->resumen = $resumen;

                $tesis->save();
                $idTesis = $tesis->id;
                if ($idEstudiante!=null){
                    if (!($estudianteController->agregarTesis($idEstudiante,$idTesis))){
                        return response()->json([
                            "salida" => false,
                            "mensaje" => "Error al enlazar la tesis con un estudiante"
                        ],200);
                    }
                }
                return response()->json([
                    "salida" => true,
                    "mensaje" => "Se ingreso la tesis exitosamente"
                ],200);
            }else{
                return response()->json([
                    "salida" => false,
                    "mensaje" => "La tesis tiene que tener contenido"
                ],200);
            }
        } catch (Exception $e){
            return $this->Error($e);
        }
    }

    public function eliminarTesis(Request $request){
        
        try {
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
        } catch(Exception $e){
            return $this->Error($e);
        }
    }
}