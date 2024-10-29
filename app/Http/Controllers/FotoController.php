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
}