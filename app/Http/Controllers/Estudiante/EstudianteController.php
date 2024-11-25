<?php

namespace App\Http\Controllers\Estudiante;
use App\Http\Controllers\Controller;
use App\Http\Controllers\FotoController;
use App\Models\Estudiante\Estudiante;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class EstudianteController extends Controller{

    public function obtenerEstudiantes($idSemestre){
        try{
            
            if ($idSemestre!=0){
                $query ="SELECT e.id, e.nombre, n.nombre as nivelAcademico, e.correo, e.tesis, e.foto,s.cadena as semestre ".
                "FROM ESTUDIANTE e,NIVEL_ACADEMICO n,SEMESTRE s ".
                "WHERE e.ELiminado = 0 AND e.semestre = ? AND n.id = e.nivelAcademico AND s.id = e.semestre";
                $estudiantes=DB::select($query,[$idSemestre]);
            }else{
                $query ="SELECT e.id, e.nombre, n.nombre as nivelAcademico, e.correo, e.tesis, e.foto,s.cadena as semestre ".
                "FROM ESTUDIANTE e,NIVEL_ACADEMICO n ".
                "WHERE e.ELiminado = 0 AND n.id = e.nivelAcademico AND s.id = e.semestre";
                $estudiantes=DB::select($query);
            }
            
            if (!($estudiantes)){
                return response()->json([
                    "salida" => false,
                    "mensaje" => "No hay estudiantes registrados"
                ],200);
            }

            $estudiantesSalida = $this->llenarDatos($estudiantes);
            if (!($estudiantesSalida)){
                return response()->json([
                    "salida" => false,
                    "mensaje" => "Hubo un error al llenar las fotos"
                ],200);
            }

            return response()->json([
                "salida" => true,
                "estudiantes" => $estudiantesSalida
            ],200);
        } catch (Exception $e){
            return $this->Error($e);
        }
    }

    private function llenarDatos($estudiantes){
        try{
            $fotoController = new FotoController();
            for ($i=0;$i<count($estudiantes);$i++){
                $estudiantes[$i]->foto=$fotoController->obtenerFoto($estudiantes[$i]->foto);
            }
            return $estudiantes;
        } catch (Exception $e){
            return false;
        }
    }

    public function agregarEstudiante(Request $request){
        try {
            $token = $request->input('token');
            $idUsuario = $request->input('idUsuario');
            if (!($this->tokenValido($idUsuario,$token))){
                return response()->json([
                    "salida" => false,
                    "mensaje" => $this->TOKEN_INVALIDO
                ],200);
            }

            $numSemestre = $request->input('semestre');
            $anio = $request->input('anio');

            $fotoController = new FotoController();
            $semestreController = new SemestreController();
            if (!($this->existe($request->input('correo')))){
                $estudiante = new Estudiante();
                $estudiante->nombre = $request->input('nombre');
                $estudiante->nivelAcademico = $request->input('nivelAcademico');
                $estudiante->correo = $request->input('correo');
                $estudiante->tesis = null;
                $estudiante->foto = $fotoController->ingresarFoto($request->input('foto'));
                $estudiante->semestre = $semestreController->obtenerSemestre($numSemestre,$anio);
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
        } catch (Exception $e){
            return $this->Error($e);
        }
    }

    public function existe(string $correo){
        $estudiante = Estudiante::where('correo','=',$correo)
                                ->where('Eliminado','=',0)
                                ->first();
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
            $estudiante->save();
            return true;
        }catch (Exception $e){
            return false;
        }
    }

    public function eliminarTesis($idEstudiante){
        if ($idEstudiante==null){
            return true;
        }
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
        try{
            $token = $request->input('token');
            $idUsuario = $request->input('idUsuario');
            if (!($this->tokenValido($idUsuario,$token))){
                return response()->json([
                    "salida" => false,
                    "mensaje" => $this->TOKEN_INVALIDO
                ],200);
            }

            $idEstudiante = $request->input('idEstudiante');
            $estudiante = Estudiante::find($idEstudiante);

            if ($estudiante->correo != $request->input('correo')){
                if ($this->existe($request->input('correo'))){
                    return response()->json([
                        "salida" => false,
                        "mensaje" => "El correo del estudiante ya existe"
                    ],200);
                }
            }
            $semestreController = new SemestreController();
            $numSemestre = $request->input('semestre');
            $anio = $request->input('anio');
            
            $estudiante->nombre = $request->input('nombre');
            $estudiante->nivelAcademico = $request->input('nivelAcademico');
            $estudiante->correo = $request->input('correo');
            if ($request->input('foto')!=null){
                $fotoController = new FotoController();
                $estudiante->foto = $fotoController->ingresarFoto($request->input('foto'));
            }
            $estudiante->semestre = $semestreController->obtenerSemestre($numSemestre,$anio);
            $estudiante->save();
            return response()->json([
                "salida" => true,
                "mensaje" => "Se actualizo exitosamente al estudiante"
            ],200);
        } catch (Exception $e){
            return $this->Error($e);
        }
    }

    public function eliminarEstudiante(Request $request){
        try{
            $token = $request->input('token');
            $idUsuario = $request->input('idUsuario');
            if (!($this->tokenValido($idUsuario,$token))){
                return response()->json([
                    "salida" => false,
                    "mensaje" => $this->TOKEN_INVALIDO
                ],200);
            }

            $idEstudiante = $request->input('idEstudiante');
            $estudiante = Estudiante::find($idEstudiante);
            $estudiante->Eliminado = 1;
            $estudiante->save();
            return response()->json([
                "salida" => true,
                "mensaje" => "Se elimino al estudiante exitosamente"
            ],200);
        }catch (Exception $e){
            return $this->Error($e);
        }
    }

    public function obtenerEstudiantePorTesis($idTesis){
        $estudiante = Estudiante::where('tesis','=',$idTesis)->where('Eliminado','=',0)->get();
        if ($estudiante->isNotEmpty()){
            $nivelAcademicoController = new NivelAcademicoController();
            $fotoController = new FotoController();
            $salidaEstudiante = $estudiante[0];
            $preSalida = [
                "id" => $salidaEstudiante['id'],
                "nombre" => $salidaEstudiante['nombre'],
                "nivelAcademico" => $nivelAcademicoController->obtenerNombre($salidaEstudiante['nivelAcademico']),
                "correo" => $salidaEstudiante['correo'],
                "foto" => $fotoController->obtenerFoto($salidaEstudiante['foto'])
            ];
            return $preSalida;
        }else{
            return null;
        }
    }   
}