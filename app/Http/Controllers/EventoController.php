<?php

namespace App\Http\Controllers;
use App\Http\Controllers\Controller;
use App\Models\Evento;
use Illuminate\Http\Request;
use Exception;



class EventoController extends Controller{

    public function obtenerEventos(){
        $fotoController = new FotoController();
        $eventos = Evento::where('Eliminado','=',0)->orderBy('fecha','asc')->get();
        if ($eventos->isNotEmpty()){
            $eventosSalida = array();
            foreach ($eventos as $evento){
                $foto = null;
                if ($evento['foto']!=null){
                    $foto = $fotoController->obtenerFoto($evento['foto']);
                }
                $eventoLlena = [
                    "id" => $evento['id'],
                    "nombre" => $evento['nombre'],
                    "descripcion" => $evento['descripcion'],
                    "director" => $evento['director'],
                    "fecha" => $evento['fecha'],
                    "lugar" => $evento['lugar'],
                    "fotoBase64" => $foto
                ];
                array_push($eventosSalida,$eventoLlena);
            }
            return response()->json([
                "salida" => true,
                "eventos" => $eventosSalida
            ],200);
        }else{
            return response()->json([
                "salida" => false,
                "mensaje" => "No hay noticias"
            ],200);
        }
    }

    public function ingresarEvento(Request $request){

        $token = $request->input('token');
        $idUsuario = $request->input('idUsuario');
        if (!($this->tokenValido($idUsuario,$token))){
            return response()->json([
                "salida" => false,
                "mensaje" => $this->TOKEN_INVALIDO
            ],200);
        }

        try{
            $fotoController = new FotoController();
            $foto = $fotoController->ingresarFoto($request->input('fotoBase64'));
            $evento = new Evento();
            $evento->nombre = $request->input('nombre');
            $evento->descripcion = $request->input('descripcion');
            $evento->director = $request->input('director');
            $evento->fecha = $request->input('fecha');
            $evento->lugar = $request->input('lugar');
            $evento->foto = $foto;
            $evento->save();
            return response()->json([
                "salida" => true,
                "mensaje" => "El evento se agrego exitosamente"
            ],200);
        }catch (Exception $e){
            return response()->json([
                "salida" => false,
                "mensaje" => $e->getMessage()
            ],200);
        }
    }

    public function eliminarEvento(Request $request){

        $token = $request->input('token');
        $idUsuario = $request->input('idUsuario');
        if (!($this->tokenValido($idUsuario,$token))){
            return response()->json([
                "salida" => false,
                "mensaje" => $this->TOKEN_INVALIDO
            ],200);
        }

        $idEvento = $request->input('evento');
        $evento = Evento::find($idEvento);
        $evento->Eliminado = 1;
        $evento->save();
        return response()->json([
            "salida" => true,
            "mensaje" => "EL evento fue eliminado exitosamente"
        ],200);
    }

    public function actualizarEvento(Request $request){
        $token = $request->input('token');
        $idUsuario = $request->input('idUsuario');
        if (!($this->tokenValido($idUsuario,$token))){
            return response()->json([
                "salida" => false,
                "mensaje" => $this->TOKEN_INVALIDO
            ],200);
        }


        try{
            $idEvento = $request->input('idEvento');
            $fotoController = new FotoController();
            
            $evento = Evento::find($idEvento);
            $evento->nombre = $request->input('nombre');
            $evento->descripcion = $request->input('descripcion');
            $evento->director = $request->input('director');
            $evento->fecha = $request->input('fecha');
            $evento->lugar = $request->input('lugar');
            if ($request->input('fotoBase64')!=null){
                $foto = $fotoController->ingresarFoto($request->input('fotoBase64'));
                $evento->foto = $foto;
            }
            $evento->save();
            return response()->json([
                "salida" => true,
                "mensaje" => "El evento se actualizo exitosamente"
            ],200);
        }catch (Exception $e){
            return response()->json([
                "salida" => false,
                "mensaje" => $e->getMessage()
            ],200);
        }
    }
}