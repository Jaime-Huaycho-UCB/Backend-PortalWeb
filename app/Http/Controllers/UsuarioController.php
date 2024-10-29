<?php

namespace App\Http\Controllers;
use App\Http\Controllers\Controller;
use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;


class UsuarioController extends Controller{

    public function iniciarSesion(Request $request){
        $correo = $request->input('correo');
        $contrasena = $request->input('contrasena');
        $usuario = $this->existeUsuario($correo);
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
                "mensaje" => "El usuario ingresado no esta registrado"
            ],404);
        }
    }

    public function existeUsuario(string $correo){
        $usuario = Usuario::where('correo','=',$correo)->first();
        if ($usuario){
            return $usuario;
        }else{
            return false;
        }
    }
}