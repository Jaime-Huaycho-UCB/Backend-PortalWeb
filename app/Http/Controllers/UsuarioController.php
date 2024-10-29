<?php

namespace App\Http\Controllers;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Docente\DocenteController;
use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;


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
                    return response()->json([
                        "salida" => true,
                        "mensaje" => "Inicio de sesion exitoso",
                        "id" => $usuario['id'],
                        "permiso" => $usuario['permiso'],
                        "docente" => $usuario['docente']
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
        $id = $request->input('id');
        $usuario = Usuario::find($id);
        $usuario->Eliminado = 1;
        $usuario->save();
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
}