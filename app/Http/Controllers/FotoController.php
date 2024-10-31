<?php

namespace App\Http\Controllers;
use App\Http\Controllers\Controller;
use App\Models\Foto;
use Illuminate\Http\Request;


class FotoController extends Controller{
    public function ingresarFoto($ruta){
        $foto = new Foto();
        $foto->ruta = $ruta;
        $foto->save();
        return $foto->id;
    }

    public function obtenerRuta(int $id){
        $ruta = Foto::where('id','=',$id)->first();
        return $ruta['ruta'];
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

    public function actualizarRuta(int $id,string $ruta){
        $foto = Foto::find($id);
        $foto->ruta = $ruta;
        $foto->save();
    }
}