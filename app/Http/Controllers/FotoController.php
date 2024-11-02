<?php

namespace App\Http\Controllers;
use App\Http\Controllers\Controller;
use App\Models\Foto;
use Illuminate\Http\Request;


class FotoController extends Controller{
    public function ingresarFoto($fotoBase64){
        if ($fotoBase64) {
            $fotoContenido = base64_decode($fotoBase64);
            $finfo = new \finfo(FILEINFO_MIME_TYPE);
            $fotoTipo = $finfo->buffer($fotoContenido);
            $foto = new Foto();
            $foto->contenido = $fotoContenido;
            $foto->tipo = $fotoTipo;
            $foto->save();
            return $foto->id;
        }else{
            return null;
        }
    }

    public function obtenerFoto(int $id){
        $foto = Foto::where('id','=',$id)
                    ->where('Eliminado','=',0)
                    ->first();
        $base64Content = base64_encode($foto->contenido);
        $tipo = $foto->tipo; 
        $imagenBase64 = "data:$tipo;base64,$base64Content";
        return $imagenBase64;
    }

    public function eliminarFoto(Request $request){
        $id = $request->input('foto');
        $foto = Foto::find($id);
        $foto->Eliminado = 1;
        $foto->save();
        return response()->json([
            "mensaje" => "La foto se elimino exitosamente"
        ],200);
    }

    public function actualizarRuta(int $id,string $fotoBase64){
        if ($fotoBase64) {
            $fotoContenido = base64_decode($fotoBase64);
            $finfo = new \finfo(FILEINFO_MIME_TYPE);
            $fotoTipo = $finfo->buffer($fotoContenido);
            $foto = Foto::find($id);
            $foto->contenido = $fotoContenido;
            $foto->tipo = $fotoTipo;
            $foto->save();
            return $foto->id;
        }else{
            return null;
        }
        
    }
}