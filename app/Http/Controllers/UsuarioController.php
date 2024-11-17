<?php

namespace App\Http\Controllers;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Docente\DocenteController;
use App\Models\Usuario;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\Console\Input\Input;
use iLLuminate\Support\Facades\DB;

class UsuarioController extends Controller{

    public function iniciarSesion(Request $request){
        try {
            $correo = $request->input('correo');
            $contrasena = $request->input('contrasena');
            $docenteControler = new DocenteController();
            $docente = $docenteControler->existe($correo);

            if (!($docente)){
                return response()->json([
                    "salida" => false,
                    "mensaje" => "El usuario ingresado no esta registrado"
                ],200);
            }

            $usuario = $this->existeUsuario($docente['id']);
            if (!($usuario)){
                return response()->json([
                    "salida" => false,
                    "mensaje" => "El docente no esta registrado como usuario"
                ],200);
            }

            // if (!(Hash::check($contrasena,$usuario['contrasena']))){
            if (!($contrasena==$usuario['contrasena'])){
                return response()->json([
                    "salida" => false,
                    "mensaje" => "Contrana ingresada incorrecta"
                ],200);
            }

            $token = $this->crearToken($usuario['id']);
            if (!($token['salida'])){
                return response()->json($token,200);
            }

            return response()->json([
                "salida" => true,
                "mensaje" => "Inicio de sesion exitoso",
                "idUsuario" => $usuario['id'],
                "permiso" => $usuario['permiso'],
                "idDocente" => $usuario['docente'],
                "token" => $token['token']
            ],200);

        } catch (Exception $e){
            return $this->Error($e);
        }
    }

    public function eliminarUsuario(Request $request){
        try {
            $token = $request->input('token');
            $idUsuario = $request->input('idUsuario');
            if (!($this->tokenValido($idUsuario,$token))){
                return response()->json([
                    "salida" => false,
                    "mensaje" => $this->TOKEN_INVALIDO
                ],200);
            }
            
            $id = $request->input('id');
            $usuario = Usuario::find($id);
            $usuario->Eliminado = 1;
            $usuario->save();
            $this->eliminarToken($id);
            return response()->json([
                "mensaje" => "El usuario fue eliminado exitosamente"
            ],200);
        } catch (Exception $e){
            return $this->Error($e);
        }
    }

    public function existeUsuario(int $idDocente){
        $usuario = Usuario::where('docente','=',$idDocente)
                            ->where('Eliminado','=',0)
                            ->first();
        if ($usuario){
            return $usuario;
        }else{
            return false;
        }
    }

    public function crearUsuario(Request $request){
        try {
            $token = $request->input('token');
            $idUsuario = $request->input('idUsuario');
            if (!($this->tokenValido($idUsuario,$token))){
                return response()->json([
                    "salida" => false,
                    "mensaje" => $this->TOKEN_INVALIDO
                ],200);
            }

            $idDocente = $request->Input('idDocente');
            if (!($this->existeUsuario($idDocente))){
                $usuario = new Usuario();
                $usuario->docente = $idDocente;
                $usuario->contrasena = $request->input('password');
                $usuario->permiso = 0;
                $usuario->save();

                if($this->inicializarToken($usuario->id)){
                    return response()->json([
                        "salida" => true,
                        "mensaje" => "Se registro exitosamente al docente"
                    ],200);
                }else{
                    return response()->json([
                        "salida" => false,
                        "mensaje" => "Hubo un erro al inicializar el token"
                    ],200);
                }
            }else{
                return response()->json([
                    "salida" => false,
                    "mensaje" => "El docente ya se encuentra registrado"
                ],200);
            }
        } catch(Exception $e){
            return $this->Error($e);
        }
    }

    public function obtenerUsuarios(Request $request){
        try{
            $token = $request->input('token');
            $idUsuario = $request->input('idUsuario');
            if (!($this->tokenValido($idUsuario,$token))){
                return response()->json([
                    "salida" => false,
                    "mensaje" =>$this->TOKEN_INVALIDO
                ],200);
            }

            $query ="SELECT d.id , d.nombre, d.correo, t.nombre as titulo, d.frase, d.foto ".
                    "FROM DOCENTE d,USUARIO u, TITULO t ".
                    "WHERE d.id = u.docente AND u.permiso = 0 AND d.Eliminado = 0 AND t.id = d.titulo";
            $usuarios = DB::select($query);
            if ($usuarios){
                return response()->json([
                    "salida" => true,
                    "usuarios" => $usuarios
                ],200);
            }else{
                return response()->json([
                    "salida" => false,
                    "mensaje" => "No hay usuarios"
                ],404);
            }
        } catch (Exception $e){
            return $this->Error($e);
        }
    }
}