<?php

namespace App\Http\Controllers\Docente;
use App\Http\Controllers\Controller;
use App\Http\Controllers\FotoController;
use App\Http\Controllers\Docente\TituloController;
use App\Http\Controllers\UsuarioController;
use App\Models\Docente\Docente;
use App\Models\Docente\Titulo;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;


class DocenteController extends Controller{

    public function agregarDocente(Request $request){
        try {
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
                ],200);
            }
        }catch (Exception $e){
            return $this->Error($e);
        }
    }

    public function existe(string $correo){
        $docente = Docente::where('correo','=',$correo)->where('Eliminado','=',0)->first();
        if ($docente){
            return $docente;
        }else{
            return false;
        }
    }

    public function obtenerDocentes(Request $request){
        try{
            $token = $request->input('token');
            $idUsuario = $request->input('idUsuario');
            if (!($this->tokenValido($idUsuario,$token))){
                return response()->json([
                    "salida" => false,
                    "mensaje" => $this->TOKEN_INVALIDO
                ],200);
            }
            
            $docentes = Docente::where('Eliminado','=',0)
                                ->where('correo','<>','superUsuario@gmail.com')
                                ->get();
            if ($docentes->isNotEmpty()){
                $docentesSalida = $this->llenarTituloFoto($docentes);
                $docentesSalida = $this->filtrarDocentesSinUsuario($docentesSalida);
                return response()->json([
                    "salida" => true,
                    "docentes" => $docentesSalida
                ],200);
            }else{
                return response()->json([
                    "salida" => false,
                    "mensaje" => "No hay docentes"
                ],404);
            }
        } catch(Exception $e){
            return $this->Error($e);
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
        try{
            $token = $request->input('token');
            $idUsuario = $request->input('idUsuario');
            if (!($this->tokenValido($idUsuario,$token))){
                return response()->json([
                    "salida" => false,
                    "mensaje" => $this->TOKEN_INVALIDO
                ],200);
            }

            $fotoController = new FotoController();
            $id  = $request->input('docente');
            $docente = Docente::find($id);
            $docente->Eliminado = 1;
            if ($fotoController->eliminarFoto($docente->foto)){
                $docente->save();
                return response()->json([
                    "salida" => true,
                    "mensaje" => "Se elimino exitosamente al docente"
                ],200);
            }else{
                return response()->json([
                    "salida" => false,
                    "mensaje" => "Error al intentar eliminar la foto"
                ],200);
            }
        } catch (Exception $e){
            return $this->Error($e);
        }
    }

    public function actualizarDocente(Request $request){
        try{
            $token = $request->input('token');
            $idUsuario = $request->input('idUsuario');
            if (!($this->tokenValido($idUsuario,$token))){
                return response()->json([
                    "salida" => false,
                    "mensaje" => $this->TOKEN_INVALIDO
                ],200);
            }

            $id = $request->input('id');
            $fotoController = new FotoController();
            $docente = Docente::find($id);

            if ($docente->correo != $request->input('correo')){
                if ($this->existe($request->input('correo'))){
                    return response()->json([
                        "salida" => false,
                        "mensaje" => "EL correo ingresado ya existe"
                    ],200);
                }
            }
            
            $idFoto = $fotoController->actualizarFoto($docente->foto,$request->input('foto'));
            $docente->nombre = $request->input('nombre');
            $docente->correo = $request->input('correo');
            $docente->titulo = $request->input('titulo');
            $docente->frase = $request->input('frase');
            $docente->foto = $idFoto;
            $docente->save();
            return response()->json([
                "mensaje" => "Se actualizo exitosamente el docente"
            ],200);
        } catch(Exception $e){
            return $this->Error($e);
        }
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

    public function obtenerDocentesTodo($idTitulo){
        try {
            if ($idTitulo==0){
                $docentes = Docente::where('Eliminado','=',0)
                ->where('correo','<>','superUsuario@gmail.com')
                ->get();
            }else{
                $docentes = Docente::where('Eliminado','=',0)
                ->where('correo','<>','superUsuario@gmail.com')
                ->where('titulo','=',$idTitulo)
                ->get();
            }
            
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
        } catch (Exception $e) {
            return $this->Error($e);
        }
    }
    
    public function obtenerInformacionDocente(Request $request){
        try{    
            $token = $request->input('token');
            $idUsuario = $request->input('idUsuario');
            if (!($this->tokenValido($idUsuario,$token))){
                return response()->json([
                    "salida" => false,
                    "mensaje" => $this->TOKEN_INVALIDO
                ],200);
            }

            $docente = $this->obtenerDocente($request->input('idDocente'));
            if ($docente){
                $tituloController = new TituloController();
                $fotoController = new FotoController();
                $salida = [
                    "id" => $docente['id'],
                    "nombre" => $docente['nombre'],
                    "correo" => $docente['correo'],
                    "titulo" => $tituloController->obtenerNombre($docente['titulo']),
                    "frase" => $docente['frase'],
                    "foto" => $fotoController->obtenerFoto($docente['foto'])
                ];
                return response()->json([
                    "salida" => true,
                    "informacion" => $salida
                ],200);
            }else{
                return response()->json([
                    "salida" => false,
                    "mensaje"  => "No se pudo obtener al docente"
                ],200);
            }
        } catch (Exception $e){
            return $this->Error($e);
        }
    }
}