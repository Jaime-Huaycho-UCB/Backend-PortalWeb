<?php

namespace App\Http\Controllers;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Docente\DocenteController;
use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\Console\Input\Input;

class UsuarioController extends Controller{

    public function iniciarSesion(Request $request){
        $correo = $request->input('correo');
        $contrasena = $request->input('contrasena');
        $docenteControler = new DocenteController();
        $docente = $docenteControler->existe($correo);
        if ($docente){
            $usuario = $this->existeUsuario($docente['id']);
            if ($usuario){
                // if (Hash::check($contrasena,$usuario['contrasena'])){
                if ($contrasena==$usuario['contrasena']){
                    $token = $this->crearToken($usuario['id']);
                    if (!($token['salida'])){
                        return $token;
                    }

                    return response()->json([
                        "salida" => true,
                        "mensaje" => "Inicio de sesion exitoso",
                        "idUsuario" => $usuario['id'],
                        "permiso" => $usuario['permiso'],
                        "idDocente" => $usuario['docente'],
                        "token" => $token['token']
                    ],200);
                }else{
                    return response()->json([
                        "salida" => false,
                        "mensaje" => "Contrana ingresada incorrecta"
                    ],401);
                }
            }else{
                return response()->json([
                    "salida" => false,
                    "mensaje" => "El docente no esta registrado como usuario"
                ],404);
            }
        }else{
            return response()->json([
                "salida" => false,
                "mensaje" => "El usuario ingresado no esta registrado"
            ],404);
        }
    }

    public function eliminarUsuario(Request $request){

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

            $this->inicializarToken($usuario->id);

            return response()->json([
                "mensaje" => "Se registro exitosamente al docente"
            ],200);
        }else{
            return response()->json([
                "mensaje" => "El docente ya se encuentra registrado"
            ],404);
        }
    }

    public function obtenerUsuarios(Request $request){

        $token = $request->input('token');
        $idUsuario = $request->input('idUsuario');
        if (!($this->tokenValido($idUsuario,$token))){
            return response()->json([
                "salida" => false,
                "mensaje" =>$this->TOKEN_INVALIDO
            ],200);
        }

        $docenteController = new DocenteController();
        $usuarios = Usuario::where('Eliminado','=',0)
                            ->where('permiso','=',0)
                            ->get();
        $salida = array();
        if ($usuarios->isNotEmpty()){
            foreach ($usuarios as $usuario){
                array_push($salida,$docenteController->obtenerDocente($usuario['docente']));
            }
            return response()->json([
                "salida" => true,
                "usuarios" => $salida
            ],200);
        }else{
            return response()->json([
                "salida" => false,
                "mensaje" => "No hay usuarios"
            ],404);
        }
    }
}